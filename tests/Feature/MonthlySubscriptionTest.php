<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Stage;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\StudentMonthlySubscription;

class MonthlySubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Stage::create([
            'grade_level' => '12',
            'label_ar' => 'العلمي',
        ]);
    }

    public function test_admin_can_update_month_status_and_persists_on_reload(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@tawjihi.ps',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $stage = Stage::first();
        $student = Student::create([
            'name_ar' => 'أحمد علي',
            'name_en' => 'Ahmed Ali',
            'nid' => '400000001',
            'email' => 'ahmed@tawjihi.ps',
            'password' => bcrypt('secret123'),
            'phone' => '0599111222',
            'age' => 18,
            'gender' => 'male',
            'status' => 'active',
            'stage_id' => $stage->id,
            'monthly_fee' => 150.00,
        ]);

        // 1. First load initializes subscriptions
        $this->actingAs($admin)
            ->get(route('admin.subscriptions.monthly'))
            ->assertStatus(200);

        $subMonth1 = StudentMonthlySubscription::where('student_id', $student->id)
            ->where('month', 1)
            ->first();

        $this->assertNotNull($subMonth1);

        // 2. Admin sets Month 1 to 'unpaid'
        $updateResponse = $this->actingAs($admin)
            ->postJson(route('admin.subscriptions.monthly.update'), [
                'student_id' => $student->id,
                'month' => 1,
                'academic_year' => '2026-2027',
                'status' => 'unpaid',
                'amount' => 150.00,
                'notes' => 'تعديل يدوي غير مسدد',
            ]);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJsonFragment([
            'success' => true,
            'status' => 'unpaid',
        ]);

        // Verify in DB that is_manual is true and status is unpaid
        $subMonth1->refresh();
        $this->assertEquals('unpaid', $subMonth1->status);
        $this->assertTrue($subMonth1->is_manual);

        // 3. Admin reloads the index page (the exact bug report: "لما اتحكم بالشهر ما بحفظ لما اعيد تحميل الصفحة")
        $this->actingAs($admin)
            ->get(route('admin.subscriptions.monthly'))
            ->assertStatus(200);

        // Verify that Month 1 status is STILL unpaid and was NOT reverted by sync!
        $subMonth1->refresh();
        $this->assertEquals('unpaid', $subMonth1->status, 'Month 1 must remain unpaid after page reload!');
        $this->assertTrue($subMonth1->is_manual);

        // 4. Admin updates student fee
        $feeResponse = $this->actingAs($admin)
            ->postJson(route('admin.subscriptions.monthly.updateStudentFee'), [
                'student_id' => $student->id,
                'monthly_fee' => 120.00,
                'custom_discount_percent' => 10,
                'custom_discount_fixed' => 0,
                'discount_notes' => 'خصم تفوق 10%',
            ]);

        $feeResponse->assertStatus(200);
        $feeResponse->assertJsonFragment(['success' => true]);

        // Net fee should be 120 * 0.9 = 108
        $student->refresh();
        $this->assertEquals(108.00, $student->monthlyAmountDue());

        // 5. Test filtering by status alone works
        $filterResponse = $this->actingAs($admin)
            ->get(route('admin.subscriptions.monthly', ['status' => 'unpaid']))
            ->assertStatus(200)
            ->assertSee('أحمد علي');
    }

    public function test_admin_can_record_partial_payment_and_view_accurate_remaining_and_aggregates(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin2@tawjihi.ps',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $stage = Stage::first();
        $student = Student::create([
            'name_ar' => 'محمود كمال',
            'name_en' => 'Mahmoud Kamal',
            'nid' => '400000002',
            'email' => 'mahmoud@tawjihi.ps',
            'password' => bcrypt('secret123'),
            'phone' => '0599222333',
            'age' => 18,
            'gender' => 'male',
            'status' => 'active',
            'stage_id' => $stage->id,
            'monthly_fee' => 150.00,
        ]);

        // 1. Initial load
        $this->actingAs($admin)
            ->get(route('admin.subscriptions.monthly'))
            ->assertStatus(200);

        // 2. Admin records partial payment: student owes 150 ₪ but pays 100 ₪, remaining must be 50 ₪
        $partialResponse = $this->actingAs($admin)
            ->postJson(route('admin.subscriptions.monthly.update'), [
                'student_id'    => $student->id,
                'month'         => 2,
                'academic_year' => '2026-2027',
                'amount'        => 150.00,
                'paid_amount'   => 100.00,
                'notes'         => 'دفع 100 ومتبقي عليه 50 لنهاية الشهر',
            ]);

        $partialResponse->assertStatus(200);
        $partialResponse->assertJson([
            'success'          => true,
            'status'           => 'partial',
            'amount'           => 150.00,
            'paid_amount'      => 100.00,
            'remaining_amount' => 50.00,
        ]);

        $subMonth2 = StudentMonthlySubscription::where('student_id', $student->id)
            ->where('month', 2)
            ->first();

        $this->assertNotNull($subMonth2);
        $this->assertEquals('partial', $subMonth2->status);
        $this->assertEquals(100.00, (float)$subMonth2->paid_amount);
        $this->assertEquals(50.00, (float)$subMonth2->remaining_amount);

        // 3. Admin loads monthly view and verifies the counters
        $indexResponse = $this->actingAs($admin)
            ->get(route('admin.subscriptions.monthly'));

        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('إجمالي المستحق المطلوب');
        $indexResponse->assertSee('إجمالي الإيراد المحصل');
        $indexResponse->assertSee('إجمالي الرصيد المتبقي');
        $indexResponse->assertSee('سداد جزئي');

        // 4. Admin completes remaining payment (pays 150 total)
        $fullResponse = $this->actingAs($admin)
            ->postJson(route('admin.subscriptions.monthly.update'), [
                'student_id'    => $student->id,
                'month'         => 2,
                'academic_year' => '2026-2027',
                'amount'        => 150.00,
                'paid_amount'   => 150.00,
                'notes'         => 'تم سداد المتبقي بالكامل',
            ]);

        $fullResponse->assertStatus(200);
        $fullResponse->assertJson([
            'success'          => true,
            'status'           => 'paid',
            'amount'           => 150.00,
            'paid_amount'      => 150.00,
            'remaining_amount' => 0.00,
        ]);

        $subMonth2->refresh();
        $this->assertEquals('paid', $subMonth2->status);
        $this->assertEquals(0.00, (float)$subMonth2->remaining_amount);
    }

    public function test_student_monthly_fee_is_calculated_dynamically_from_enrolled_subjects_with_breakdown(): void
    {
        $stage = Stage::first();
        
        $subMath = Subject::create([
            'stage_id'    => $stage->id,
            'name_ar'     => 'الرياضيات (علمي)',
            'subject_key' => 'math_test_' . rand(1000, 9999),
            'price_ils'   => 150.00,
            'is_free'     => false,
        ]);

        $subPhysics = Subject::create([
            'stage_id'    => $stage->id,
            'name_ar'     => 'الفيزياء',
            'subject_key' => 'physics_test_' . rand(1000, 9999),
            'price_ils'   => 150.00,
            'is_free'     => false,
        ]);

        $subChemistry = Subject::create([
            'stage_id'    => $stage->id,
            'name_ar'     => 'الكيمياء',
            'subject_key' => 'chem_test_' . rand(1000, 9999),
            'price_ils'   => 120.00,
            'is_free'     => false,
        ]);

        $student = Student::create([
            'name_ar'     => 'ريما فهد',
            'name_en'     => 'Rema Fahd',
            'nid'         => '400099991',
            'email'       => 'rema_test@tawjihi.ps',
            'password'    => bcrypt('secret123'),
            'phone'       => '0599000111',
            'age'         => 18,
            'gender'      => 'أنثى',
            'status'      => 'pending',
            'stage_id'    => $stage->id,
            'monthly_fee' => 150.00, // old default
        ]);

        // Enroll in the 3 subjects
        \App\Models\Enrollment::create(['student_id' => $student->id, 'subject_id' => $subMath->id, 'status' => 'pending']);
        \App\Models\Enrollment::create(['student_id' => $student->id, 'subject_id' => $subPhysics->id, 'status' => 'pending']);
        \App\Models\Enrollment::create(['student_id' => $student->id, 'subject_id' => $subChemistry->id, 'status' => 'pending']);

        // Subtotal = 150 + 150 + 120 = 420.
        // 3 subjects => 15% discount = 420 * 0.15 = 63.
        // Net = 420 - 63 = 357.
        $breakdown = $student->getFeeBreakdown();
        $this->assertEquals(420.00, $breakdown['subtotal']);
        $this->assertEquals(63.00, $breakdown['bundle_discount']);
        $this->assertEquals(357.00, $breakdown['final_amount']);
        $this->assertEquals(357.00, $student->monthlyAmountDue());

        // Test pending approval screen renders breakdown
        auth('student')->login($student);
        $response = $this->get('/student/pending-approval');

        $response->assertStatus(200);
        $response->assertSee('المواد والمباحث الدراسية المسجلة بحسابك');
        $response->assertSee('الرياضيات (علمي)');
        $response->assertSee('الفيزياء');
        $response->assertSee('الكيمياء');
        $response->assertSee('خصم باقة التوجيهي (15%)');
        $response->assertSee('357');
    }

    public function test_admin_can_search_subscriptions_by_nid(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin_search@tawjihi.ps',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $stage = Stage::first();
        $student = Student::create([
            'name_ar' => 'محمود الفلسطيني',
            'name_en' => 'Mahmoud Palestinian',
            'nid' => '875478542',
            'email' => 'mahmoud@tawjihi.ps',
            'password' => bcrypt('secret123'),
            'phone' => '0599887766',
            'age' => 18,
            'gender' => 'male',
            'status' => 'active',
            'stage_id' => $stage->id,
            'monthly_fee' => 150.00,
        ]);

        // Search by nid 875478542
        $response = $this->actingAs($admin)
            ->get(route('admin.subscriptions.monthly', ['search' => '875478542']));

        $response->assertStatus(200);
        $response->assertSee('محمود الفلسطيني');
        $response->assertSee('875478542');
    }

    /**
     * فحص واجهة الإدارة المالية المستقلة للطالب (الشهور الـ 12 والبيانات الفردية)
     */
    public function test_admin_can_view_dedicated_student_subscription_profile(): void
    {
        $admin = User::create([
            'name' => 'Admin Test Profile',
            'email' => 'admin_profile@tawjihi.ps',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $stage = Stage::first();
        $student = Student::create([
            'name_ar' => 'أحمد العبدالله',
            'name_en' => 'Ahmed Alabdallah',
            'nid' => '901234567',
            'email' => 'ahmed.profile@tawjihi.ps',
            'password' => bcrypt('secret123'),
            'phone' => '0599112233',
            'age' => 18,
            'gender' => 'male',
            'status' => 'active',
            'stage_id' => $stage->id,
            'monthly_fee' => 150.00,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.subscriptions.student', ['student' => $student->id]));

        $response->assertStatus(200);
        $response->assertSee('أحمد العبدالله');
        $response->assertSee('901234567');
        $response->assertSee('إجمالي المستحق المطلوب للعام');
        $response->assertSee('إجمالي المبلغ المسدد المعتمد');
        $response->assertSee('الرصيد المتبقي بذمة الطالب');
        $response->assertSee('سجل استحقاقات وسداد الشهور الـ 12');
        $response->assertSee('1- الشهر الأول');
        $response->assertSee('12- الشهر الثاني عشر');
        $response->assertSee('تسجيل وسداد القسط');
    }
}
