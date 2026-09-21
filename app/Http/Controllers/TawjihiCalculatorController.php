<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TawjihiCalculatorController extends Controller
{
    /**
     * عرض صفحة حاسبة معدل التوجيهي ودليل التنسيق
     */
    public function index()
    {
        $universities = $this->getUniversitiesData();
        return view('public.tawjihi_calculator', compact('universities'));
    }

    /**
     * دالة معالجة حساب المعدل وإرجاع التخصصات المقترحة عبر AJAX
     */
    public function calculate(Request $request)
    {
        // التحقق من صلاحية حساب المعدل للطلاب بقرار المدير
        if (\Illuminate\Support\Facades\Auth::guard('student')->check()) {
            $allowGpa = (bool) \App\Models\Setting::get('allow_student_calculate_gpa', 0);
            if (!$allowGpa) {
                return response()->json([
                    'success' => false,
                    'icon'    => 'info',
                    'title'   => 'حساب المعدل مغلق حالياً 🔒',
                    'message' => 'حساب واعتماد المعدل النهائي مغلق حالياً، ويتم تفعيله بقرار إدارة المنصة في نهاية العام الدراسي.',
                    'status'  => 'locked'
                ], 403);
            }
        }

        $branch = $request->input('branch', 'scientific');
        $scores = $request->input('scores', []);

        $result = $this->computeScore($branch, $scores);
        $eligibleMajors = $this->getEligibleMajors($branch, $result['percentage']);

        return response()->json([
            'success' => true,
            'percentage' => $result['percentage'],
            'total_score' => $result['total_score'],
            'max_total' => $result['max_total'],
            'status' => $result['status'],
            'notes' => $result['notes'],
            'eligible_majors' => $eligibleMajors,
        ]);
    }

    /**
     * حساب معدل التوجيهي (الثانوية العامة) في فلسطين لعام 2026 بدقة (نظام 800 علامة ÷ 8)
     */
    private function computeScore($branch, $scores)
    {
        $total = 0;
        $maxTotal = 800;
        $notes = [];
        $passedAll = true;
        $droppedElective = null;
        $countedElectives = [];

        if ($branch === 'scientific') {
            // 1. المواد الإجبارية (5 مواد = 500 علامة)
            $islamic = min(50,  max(0, floatval($scores['islamic'] ?? 0))); // التربية الإسلامية من 50
            $arabic  = min(100, max(0, floatval($scores['arabic'] ?? 0)));  // اللغة العربية من 100
            $english = min(100, max(0, floatval($scores['english'] ?? 0))); // اللغة الإنجليزية من 100
            $math    = min(150, max(0, floatval($scores['math'] ?? 0)));    // الرياضيات من 150
            $physics = min(100, max(0, floatval($scores['physics'] ?? 0))); // الفيزياء من 100

            $compulsoryTotal = $islamic + $arabic + $english + $math + $physics;

            // 2. المواد الاختيارية (أعلى مبحثين من أصل ثلاثة = 200 علامة، وحذف المادة الأقل)
            $chemistry = min(100, max(0, floatval($scores['chemistry'] ?? 0)));
            $biology   = min(100, max(0, floatval($scores['biology'] ?? 0)));
            $tech      = min(100, max(0, floatval($scores['tech'] ?? 0)));

            $electives = [
                'الكيمياء'                  => $chemistry,
                'العلوم الحياتية (الأحياء)' => $biology,
                'التكنولوجيا'               => $tech,
            ];
            arsort($electives);

            // أخذ أعلى مبحثين
            $countedElectives = array_slice($electives, 0, 2, true);
            $droppedElectiveName = array_key_last($electives);
            $droppedElectiveScore = end($electives);
            $droppedElective = [
                'name'  => $droppedElectiveName,
                'score' => $droppedElectiveScore,
            ];

            $electivesTotal = array_sum($countedElectives);

            // 3. مادة الحسم / المكملة (مبحث واحد = 100 علامة)
            $complementary = min(100, max(0, floatval($scores['complementary'] ?? ($scores['mgmt'] ?? 0))));

            $total = $compulsoryTotal + $electivesTotal + $complementary;
            $maxTotal = 800;

            $notes[] = "مجموع المواد الإجبارية الخمسة: {$compulsoryTotal} من 500";
            $notes[] = "تم احتساب أعلى مادتين اختياريتين: " . implode(' + ', array_map(fn($k, $v) => "{$k} ({$v})", array_keys($countedElectives), $countedElectives)) . " = {$electivesTotal} من 200";
            $notes[] = "تم حذف المادة الاختيارية الأقل علامة تلقائياً: {$droppedElectiveName} ({$droppedElectiveScore} من 100)";
            $notes[] = "مادة الحسم / المكملة: {$complementary} من 100";

        } elseif ($branch === 'literary') {
            // 1. المواد الإجبارية (5 مواد = 500 علامة)
            $islamic   = min(50,  max(0, floatval($scores['islamic'] ?? 0)));   // التربية الإسلامية من 50
            $arabic    = min(150, max(0, floatval($scores['arabic'] ?? 0)));    // اللغة العربية من 150
            $english   = min(100, max(0, floatval($scores['english'] ?? 0)));   // اللغة الإنجليزية من 100
            $history   = min(100, max(0, floatval($scores['history'] ?? 0)));   // الدراسات التاريخية من 100
            $geography = min(100, max(0, floatval($scores['geography'] ?? 0))); // الدراسات الجغرافية من 100

            $compulsoryTotal = $islamic + $arabic + $english + $history + $geography;

            // 2. المواد الاختيارية (أعلى مبحثين من أصل ثلاثة = 200 علامة، وحذف المادة الأقل)
            $math       = min(100, max(0, floatval($scores['math'] ?? 0)));
            $sciCulture = min(100, max(0, floatval($scores['sci_culture'] ?? 0)));
            $tech       = min(100, max(0, floatval($scores['tech'] ?? 0)));

            $electives = [
                'الرياضيات (أدبي)' => $math,
                'الثقافة العلمية' => $sciCulture,
                'التكنولوجيا'      => $tech,
            ];
            arsort($electives);

            $countedElectives = array_slice($electives, 0, 2, true);
            $droppedElectiveName = array_key_last($electives);
            $droppedElectiveScore = end($electives);
            $droppedElective = [
                'name'  => $droppedElectiveName,
                'score' => $droppedElectiveScore,
            ];

            $electivesTotal = array_sum($countedElectives);

            // 3. مادة مكملة (مبحث واحد = 100 علامة)
            $complementary = min(100, max(0, floatval($scores['complementary'] ?? ($scores['legal'] ?? 0))));

            $total = $compulsoryTotal + $electivesTotal + $complementary;
            $maxTotal = 800;

            $notes[] = "مجموع المواد الإجبارية الخمسة: {$compulsoryTotal} من 500";
            $notes[] = "تم احتساب أعلى مادتين اختياريتين: " . implode(' + ', array_map(fn($k, $v) => "{$k} ({$v})", array_keys($countedElectives), $countedElectives)) . " = {$electivesTotal} من 200";
            $notes[] = "تم حذف المادة الاختيارية الأقل علامة تلقائياً: {$droppedElectiveName} ({$droppedElectiveScore} من 100)";
            $notes[] = "المادة المكملة المقرة: {$complementary} من 100";

        } elseif ($branch === 'business') {
            // فرع الريادة والأعمال لعام 2026 (5 إجباري = 500 + 2 اختياري = 200 + مكملة = 100 -> المجموع 800)
            $islamic    = min(50,  max(0, floatval($scores['islamic'] ?? 0)));
            $arabic     = min(100, max(0, floatval($scores['arabic'] ?? 0)));
            $english    = min(100, max(0, floatval($scores['english'] ?? 0)));
            $accounting = min(100, max(0, floatval($scores['accounting'] ?? 0)));
            $mgmt       = min(150, max(0, floatval($scores['mgmt'] ?? 0)));

            $compulsoryTotal = $islamic + $arabic + $english + $accounting + $mgmt;

            $projects = min(100, max(0, floatval($scores['projects'] ?? 0)));
            $math     = min(100, max(0, floatval($scores['math'] ?? 0)));
            $tech     = min(100, max(0, floatval($scores['tech'] ?? 0)));

            $electives = [
                'المشاريع الريادية' => $projects,
                'رياضيات الأعمال'   => $math,
                'التكنولوجيا'       => $tech,
            ];
            arsort($electives);

            $countedElectives = array_slice($electives, 0, 2, true);
            $droppedElectiveName = array_key_last($electives);
            $droppedElectiveScore = end($electives);
            $droppedElective = [
                'name'  => $droppedElectiveName,
                'score' => $droppedElectiveScore,
            ];

            $electivesTotal = array_sum($countedElectives);
            $complementary = min(100, max(0, floatval($scores['complementary'] ?? 0)));

            $total = $compulsoryTotal + $electivesTotal + $complementary;
            $maxTotal = 800;

            $notes[] = "مجموع المواد الإجبارية: {$compulsoryTotal} من 500";
            $notes[] = "تم احتساب أعلى مادتين اختياريتين: " . implode(' + ', array_map(fn($k, $v) => "{$k} ({$v})", array_keys($countedElectives), $countedElectives)) . " = {$electivesTotal} من 200";
            $notes[] = "تم حذف المادة الأقل: {$droppedElectiveName} ({$droppedElectiveScore})";
            $notes[] = "المادة المكملة: {$complementary} من 100";

        } else {
            // فروع عامة أخرى
            $sum = 0;
            foreach ($scores as $s) {
                $sum += floatval($s);
            }
            $total = $sum;
            $maxTotal = 800;
        }

        // المعادلة النهائية الرسمية لعام 2026: مجموع العلامات المحتسبة ÷ 8
        $percentage = round($total / 8, 2);
        $status = $percentage >= 50 ? 'ناجح' : 'راسب';

        return [
            'total_score'       => $total,
            'max_total'         => $maxTotal,
            'percentage'        => $percentage,
            'status'            => $status,
            'notes'             => $notes,
            'dropped_elective'  => $droppedElective,
            'counted_electives' => $countedElectives,
        ];
    }

    /**
     * قائمة التخصصات والجامعات الفلسطينية المتوافقة مع المعدل والفرع
     */
    public function getEligibleMajors($branch, $percentage)
    {
        $allMajors = $this->getMajorsDatabase();
        $eligible = [];

        foreach ($allMajors as $major) {
            // التحقق من توافق الفرع
            if (!in_array($branch, $major['allowed_branches'])) {
                continue;
            }

            // التحقق من استيفاء الحد الأدنى للقبول
            if ($percentage >= $major['min_rate']) {
                $major['status'] = 'مضمون القبول بإذن الله';
                $major['diff'] = round($percentage - $major['min_rate'], 1);
                $eligible[] = $major;
            } elseif ($percentage >= ($major['min_rate'] - 2)) {
                $major['status'] = 'منافسة قوية / موازي';
                $major['diff'] = round($percentage - $major['min_rate'], 1);
                $eligible[] = $major;
            }
        }

        // ترتيب التخصصات تنازلياً حسب الحد الأدنى للقبول
        usort($eligible, function ($a, $b) {
            return $b['min_rate'] <=> $a['min_rate'];
        });

        return $eligible;
    }

    /**
     * قاعدة بيانات الجامعات الفلسطينية
     */
    private function getUniversitiesData()
    {
        return [
            ['id' => 'birzeit', 'name' => 'جامعة بيرزيت', 'city' => 'رام الله والبيرة', 'badge' => 'BZU'],
            ['id' => 'najah', 'name' => 'جامعة النجاح الوطنية', 'city' => 'نابلس', 'badge' => 'NNU'],
            ['id' => 'quds', 'name' => 'جامعة القدس (أبو ديس)', 'city' => 'القدس الشريف', 'badge' => 'AQU'],
            ['id' => 'polytechnic', 'name' => 'جامعة بوليتكنك فلسطين', 'city' => 'الخليل', 'badge' => 'PPU'],
            ['id' => 'khadoorie', 'name' => 'جامعة فلسطين التقنية - خضوري', 'city' => 'طولكرم / رام الله', 'badge' => 'PTUK'],
            ['id' => 'aaup', 'name' => 'الجامعة العربية الأمريكية', 'city' => 'جنين / رام الله', 'badge' => 'AAUP'],
            ['id' => 'islamic_gaza', 'name' => 'الجامعة الإسلامية بغزة', 'city' => 'غزة', 'badge' => 'IUG'],
            ['id' => 'azhar_gaza', 'name' => 'جامعة الأزهر - غزة', 'city' => 'غزة', 'badge' => 'AUG'],
        ];
    }

    /**
     * مصفوفة مفاتيح التنسيق والتخصصات المعتمدة في فلسطين
     */
    private function getMajorsDatabase()
    {
        return [
            // الكليات الطبية والصحية
            [
                'title' => 'الطب البشري (Doctor of Medicine)',
                'min_rate' => 95.0,
                'allowed_branches' => ['scientific'],
                'category' => 'العلوم الطبية والصحية',
                'universities' => ['جامعة النجاح', 'جامعة القدس', 'الجامعة الإسلامية', 'الجامعة العربية الأمريكية'],
                'icon' => 'fas fa-stethoscope'
            ],
            [
                'title' => 'طب وجراحة الأسنان',
                'min_rate' => 90.0,
                'allowed_branches' => ['scientific'],
                'category' => 'العلوم الطبية والصحية',
                'universities' => ['الجامعة العربية الأمريكية', 'جامعة القدس', 'جامعة الأزهر', 'جامعة النجاح'],
                'icon' => 'fas fa-tooth'
            ],
            [
                'title' => 'دكتور صيدلة (PharmD)',
                'min_rate' => 88.0,
                'allowed_branches' => ['scientific'],
                'category' => 'العلوم الطبية والصحية',
                'universities' => ['جامعة النجاح', 'جامعة بيرزيت'],
                'icon' => 'fas fa-pills'
            ],
            [
                'title' => 'الصيدلة العامة',
                'min_rate' => 85.0,
                'allowed_branches' => ['scientific'],
                'category' => 'العلوم الطبية والصحية',
                'universities' => ['جامعة بيرزيت', 'جامعة النجاح', 'جامعة القدس', 'جامعة الأزهر'],
                'icon' => 'fas fa-mortar-pestle'
            ],
            [
                'title' => 'التمريض والعلوم الطبية المخبرية',
                'min_rate' => 75.0,
                'allowed_branches' => ['scientific'],
                'category' => 'العلوم الطبية والصحية',
                'universities' => ['جامعة بيرزيت', 'جامعة النجاح', 'جامعة القدس', 'جامعة خضوري', 'الجامعة الإسلامية'],
                'icon' => 'fas fa-user-nurse'
            ],

            // الهندسة وتكنولوجيا المعلومات
            [
                'title' => 'هندسة الذكاء الاصطناعي وعلم البيانات',
                'min_rate' => 85.0,
                'allowed_branches' => ['scientific'],
                'category' => 'الهندسة وتكنولوجيا المعلومات',
                'universities' => ['جامعة بيرزيت', 'جامعة النجاح', 'جامعة بوليتكنك فلسطين', 'الجامعة العربية الأمريكية'],
                'icon' => 'fas fa-robot'
            ],
            [
                'title' => 'هندسة الحاسوب والبرمجيات',
                'min_rate' => 82.0,
                'allowed_branches' => ['scientific'],
                'category' => 'الهندسة وتكنولوجيا المعلومات',
                'universities' => ['جامعة بيرزيت', 'جامعة النجاح', 'جامعة القدس', 'جامعة بوليتكنك فلسطين', 'الجامعة الإسلامية'],
                'icon' => 'fas fa-laptop-code'
            ],
            [
                'title' => 'علم الحاسوب وتكنولوجيا المعلومات (CS / IT)',
                'min_rate' => 70.0,
                'allowed_branches' => ['scientific', 'literary', 'business'],
                'category' => 'الهندسة وتكنولوجيا المعلومات',
                'universities' => ['كافة الجامعات الفلسطينية'],
                'icon' => 'fas fa-code'
            ],
            [
                'title' => 'الهندسة المدنية والمعمارية',
                'min_rate' => 80.0,
                'allowed_branches' => ['scientific'],
                'category' => 'الهندسة وتكنولوجيا المعلومات',
                'universities' => ['جامعة بيرزيت', 'جامعة النجاح', 'جامعة بوليتكنك فلسطين', 'جامعة خضوري'],
                'icon' => 'fas fa-drafting-compass'
            ],
            [
                'title' => 'الأمن السيبراني والشبكات',
                'min_rate' => 75.0,
                'allowed_branches' => ['scientific', 'business'],
                'category' => 'الهندسة وتكنولوجيا المعلومات',
                'universities' => ['جامعة خضوري', 'جامعة النجاح', 'جامعة بوليتكنك فلسطين', 'جامعة القدس'],
                'icon' => 'fas fa-shield-alt'
            ],

            // العلوم الإنسانية والإدارية والقانون
            [
                'title' => 'الحقوق والقانون',
                'min_rate' => 75.0,
                'allowed_branches' => ['scientific', 'literary', 'business'],
                'category' => 'القانون والعلوم الإنسانية',
                'universities' => ['جامعة بيرزيت', 'جامعة النجاح', 'جامعة القدس', 'جامعة الأزهر', 'جامعة الخليل'],
                'icon' => 'fas fa-balance-scale'
            ],
            [
                'title' => 'المحاسبة والعلوم المالية والمصرفية',
                'min_rate' => 68.0,
                'allowed_branches' => ['scientific', 'literary', 'business'],
                'category' => 'الأعمال والإدارة',
                'universities' => ['كافة الجامعات الفلسطينية'],
                'icon' => 'fas fa-chart-line'
            ],
            [
                'title' => 'إدارة الأعمال والتسويق الرقمي',
                'min_rate' => 65.0,
                'allowed_branches' => ['scientific', 'literary', 'business'],
                'category' => 'الأعمال والإدارة',
                'universities' => ['كافة الجامعات الفلسطينية'],
                'icon' => 'fas fa-briefcase'
            ],
            [
                'title' => 'اللغة الإنجليزية والترجمة',
                'min_rate' => 65.0,
                'allowed_branches' => ['scientific', 'literary'],
                'category' => 'الآداب واللغات',
                'universities' => ['كافة الجامعات الفلسطينية'],
                'icon' => 'fas fa-language'
            ],
            [
                'title' => 'الإعلام والاتصال الرقمي',
                'min_rate' => 65.0,
                'allowed_branches' => ['scientific', 'literary', 'business'],
                'category' => 'الإعلام والصحافة',
                'universities' => ['جامعة بيرزيت', 'جامعة النجاح', 'الجامعة الإسلامية', 'جامعة الأقصى'],
                'icon' => 'fas fa-bullhorn'
            ],
            [
                'title' => 'التربية والتعليم الأساسي',
                'min_rate' => 65.0,
                'allowed_branches' => ['scientific', 'literary'],
                'category' => 'العلوم التربوية',
                'universities' => ['كافة الجامعات الفلسطينية'],
                'icon' => 'fas fa-chalkboard-teacher'
            ]
        ];
    }
}
