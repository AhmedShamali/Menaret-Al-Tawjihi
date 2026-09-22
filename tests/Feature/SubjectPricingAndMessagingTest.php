<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Stage;
use App\Models\Subject;
use App\Models\Message;
use App\Models\StudentMonthlySubscription;

class SubjectPricingAndMessagingTest extends TestCase
{
    use RefreshDatabase;

    protected Stage $stage;
    protected User $admin;
    protected User $teacher;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stage = Stage::create([
            'grade_level' => '12',
            'label_ar' => 'العلمي',
        ]);

        $this->admin = User::create([
            'name' => 'م. أحمد شمالي',
            'email' => 'admin@tawjihi.ps',
            'password' => bcrypt('secret123'),
            'role' => 'admin',
        ]);

        $this->teacher = User::create([
            'name' => 'أستاذ الرياضيات المعتمد',
            'email' => 'teacher@tawjihi.ps',
            'password' => bcrypt('secret123'),
            'role' => 'teacher',
            'stage_id' => $this->stage->id,
        ]);

        $this->student = Student::create([
            'name_ar' => 'طالب تجريبي متميز',
            'name_en' => 'Test Student',
            'nid' => '401122334',
            'email' => 'student.test@tawjihi.ps',
            'password' => bcrypt('password123'),
            'phone' => '0599001122',
            'age' => 18,
            'gender' => 'male',
            'status' => 'active',
            'stage_id' => $this->stage->id,
            'monthly_fee' => 150.00,
        ]);
    }

    /**
     * 1. فحص ترقيم شهور الاشتراكات (1- الشهر الأول ... 12- الشهر الثاني عشر)
     */
    public function test_monthly_subscription_names_are_prefixed_with_numbers(): void
    {
        $namesAr = StudentMonthlySubscription::monthNamesAr();
        
        $this->assertEquals('1- الشهر الأول', $namesAr[1]);
        $this->assertEquals('2- الشهر الثاني', $namesAr[2]);
        $this->assertEquals('3- الشهر الثالث', $namesAr[3]);
        $this->assertEquals('4- الشهر الرابع', $namesAr[4]);
        $this->assertEquals('5- الشهر الخامس', $namesAr[5]);
        $this->assertEquals('6- الشهر السادس', $namesAr[6]);
        $this->assertEquals('7- الشهر السابع', $namesAr[7]);
        $this->assertEquals('8- الشهر الثامن', $namesAr[8]);
        $this->assertEquals('9- الشهر التاسع', $namesAr[9]);
        $this->assertEquals('10- الشهر العاشر', $namesAr[10]);
        $this->assertEquals('11- الشهر الحادي عشر', $namesAr[11]);
        $this->assertEquals('12- الشهر الثاني عشر', $namesAr[12]);
    }

    /**
     * 2. فحص الشفافية المالية للاشتراكات: كم عليه، كم دفع، كم ضل قسط مستحق
     */
    public function test_student_and_admin_see_financial_due_paid_and_remaining(): void
    {
        // تهيئة اشتراكات الطالب
        StudentMonthlySubscription::create([
            'student_id' => $this->student->id,
            'academic_year' => '2026-2027',
            'month' => 1,
            'status' => 'paid',
            'amount' => 150.00,
            'paid_amount' => 150.00,
            'paid_at' => now(),
        ]);

        StudentMonthlySubscription::create([
            'student_id' => $this->student->id,
            'academic_year' => '2026-2027',
            'month' => 2,
            'status' => 'partial',
            'amount' => 150.00,
            'paid_amount' => 50.00,
            'paid_at' => now(),
        ]);

        StudentMonthlySubscription::create([
            'student_id' => $this->student->id,
            'academic_year' => '2026-2027',
            'month' => 3,
            'status' => 'unpaid',
            'amount' => 150.00,
            'paid_amount' => 0.00,
        ]);

        // صفحة الطالب
        $studentResponse = $this->actingAs($this->student, 'student')
            ->get(route('student.subscriptions.index'));

        $studentResponse->assertStatus(200);
        $studentResponse->assertSee('كم عليّ');
        $studentResponse->assertSee('كم دفعت');
        $studentResponse->assertSee('كم ضل قسط مستحق');
        $studentResponse->assertSee('1- الشهر الأول');
        $studentResponse->assertSee('2- الشهر الثاني');

        // صفحة المدير
        $adminResponse = $this->actingAs($this->admin)
            ->get(route('admin.subscriptions.monthly'));

        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('كم عليه:');
        $adminResponse->assertSee('كم دفع:');
        $adminResponse->assertSee('كم ضل عليه:');
    }

    /**
     * 3. فحص تسعيرة المواد وحساب نسبة الخصم والسعر بعد الخصم
     */
    public function test_subject_pricing_discount_calculations_and_api(): void
    {
        // مادة بسعر 200 شيكل وخصم 50 شيكل (السعر بعد الخصم 150)
        $subject = Subject::create([
            'subject_key' => 'physics_advanced',
            'name_ar' => 'الفيزياء المتقدمة',
            'name_en' => 'Advanced Physics',
            'stage_id' => $this->stage->id,
            'teacher_id' => $this->teacher->id,
            'price_ils' => 200.00,
            'discount_price_ils' => 150.00,
            'is_published' => true,
        ]);

        $this->assertTrue($subject->has_discount);
        $this->assertEquals(25.0, $subject->discount_percentage);
        $this->assertEquals(150.00, $subject->price_after_discount);
        $this->assertEquals(50.00, $subject->discount_amount);

        // تعديل السعر والخصم كمدير عبر الـ API
        $updateResponse = $this->actingAs($this->admin)
            ->postJson(route('admin.subjects.pricing.update', $subject->id), [
                'price_ils' => 300.00,
                'discount_percentage' => 20, // 20% خصم من 300 = 60 شيكل، السعر بعد الخصم = 240
            ]);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJsonFragment([
            'status' => 'success',
            'has_discount' => true,
            'discount_percentage' => 20,
            'price_after_discount' => 240,
        ]);

        $subject->refresh();
        $this->assertEquals(300.00, $subject->price_ils);
        $this->assertEquals(240.00, $subject->discount_price_ils);
        $this->assertEquals(20.0, $subject->discount_percentage);

        // فحص ظهور الخصم في كتالوج المواد للطلاب
        $catalogResponse = $this->actingAs($this->student, 'student')
            ->get(route('student.courses.catalog'));

        $catalogResponse->assertStatus(200);
        $catalogResponse->assertSee('240');
        $catalogResponse->assertSee('300');
        $catalogResponse->assertSee('20%');
    }

    /**
     * 4. فحص مراسلات الأدمن مع المعلم (Admin ↔ Teacher)
     */
    public function test_admin_and_teacher_messaging_flow(): void
    {
        // إرسال رسالة من الأدمن للمعلم
        $sendResponse = $this->actingAs($this->admin)
            ->postJson(route('admin.teachers.send'), [
                'teacher_id' => $this->teacher->id,
                'message' => 'السلام عليكم أستاذنا، يرجى مراجعة نتائج الاختبار التجريبي.',
            ]);

        $sendResponse->assertStatus(200);
        $sendResponse->assertJsonFragment([
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('messages', [
            'admin_id' => $this->admin->id,
            'teacher_id' => $this->teacher->id,
            'sender_type' => 'admin',
            'message' => 'السلام عليكم أستاذنا، يرجى مراجعة نتائج الاختبار التجريبي.',
        ]);

        // المعلم يجلب الرسائل من الأدمن
        $fetchResponse = $this->actingAs($this->teacher)
            ->getJson(route('teacher.admin.chat.messages'));

        $fetchResponse->assertStatus(200);
        $fetchResponse->assertJsonStructure(['messages']);

        // المعلم يرد على الأدمن
        $teacherReply = $this->actingAs($this->teacher)
            ->postJson(route('teacher.admin.chat.send'), [
                'message' => 'وعليكم السلام، تم اعتماد كافة النتائج في المنظومة.',
            ]);

        $teacherReply->assertStatus(200);
        $teacherReply->assertJsonFragment([
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('messages', [
            'admin_id' => $this->admin->id,
            'teacher_id' => $this->teacher->id,
            'sender_type' => 'teacher',
            'message' => 'وعليكم السلام، تم اعتماد كافة النتائج في المنظومة.',
        ]);
    }

    /**
     * 5. فحص مراسلات المعلم مع الطالب (Teacher ↔ Student)
     */
    public function test_teacher_and_student_messaging_flow(): void
    {
        // الطالب يرسل للمعلم
        $studentSend = $this->actingAs($this->student, 'student')
            ->postJson(route('student.send.teacher'), [
                'teacher_id' => $this->teacher->id,
                'message' => 'أستاذ ممكن توضيح السؤال الثالث في الواجب؟',
            ]);

        $studentSend->assertStatus(200);

        $this->assertDatabaseHas('messages', [
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'sender_type' => 'student',
            'message' => 'أستاذ ممكن توضيح السؤال الثالث في الواجب؟',
        ]);

        // المعلم يجلب الرسائل
        $teacherFetch = $this->actingAs($this->teacher)
            ->getJson(route('teacher.messages.fetch', $this->student->id));

        $teacherFetch->assertStatus(200);
        $teacherFetch->assertJsonFragment([
            'status' => 'success',
        ]);

        // المعلم يرد على الطالب
        $teacherSend = $this->actingAs($this->teacher)
            ->postJson(route('teacher.messages.send'), [
                'student_id' => $this->student->id,
                'message' => 'أهلاً بك، تم شرح المسألة في ملخص الوحدة الثالثة.',
            ]);

        $teacherSend->assertStatus(200);
        $teacherSend->assertJsonFragment([
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('messages', [
            'teacher_id' => $this->teacher->id,
            'student_id' => $this->student->id,
            'sender_type' => 'teacher',
            'message' => 'أهلاً بك، تم شرح المسألة في ملخص الوحدة الثالثة.',
        ]);
    }

    /**
     * 6. فحص دعم الطلاب المباشر مع الأدمن (Student Support ↔ Admin)
     */
    public function test_student_support_messaging_flow(): void
    {
        // الطالب يرسل استفساراً للدعم
        $supportSend = $this->actingAs($this->student, 'student')
            ->postJson(route('student.support.send'), [
                'admin_id' => $this->admin->id,
                'message' => 'السلام عليكم، لدي استفسار بخصوص تفعيل اشتراك المواد الدراسية.',
            ]);

        $supportSend->assertStatus(200);
        $supportSend->assertJsonFragment([
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('messages', [
            'admin_id' => $this->admin->id,
            'student_id' => $this->student->id,
            'sender_type' => 'student',
            'message' => 'السلام عليكم، لدي استفسار بخصوص تفعيل اشتراك المواد الدراسية.',
        ]);

        // الطالب يجلب رسائل الدعم
        $supportFetch = $this->actingAs($this->student, 'student')
            ->getJson(route('student.support.fetch', $this->admin->id));

        $supportFetch->assertStatus(200);
        $supportFetch->assertJsonFragment([
            'status' => 'success',
        ]);
    }
}
