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
     * حساب المعدل الوزاري الفلسطيني بدقة
     */
    private function computeScore($branch, $scores)
    {
        $total = 0;
        $maxTotal = 700;
        $notes = [];
        $passedAll = true;

        if ($branch === 'scientific') {
            // الفرع العلمي: رياضيات (200)، فيزياء (100)، عربي (100)، إنجليزي (100)، إسلامية (100)
            $math    = min(200, max(0, floatval($scores['math'] ?? 0))); // الرياضيات من 200
            $physics = min(100, max(0, floatval($scores['physics'] ?? 0)));
            $arabic  = min(100, max(0, floatval($scores['arabic'] ?? 0)));
            $english = min(100, max(0, floatval($scores['english'] ?? 0)));
            $islamic = min(100, max(0, floatval($scores['islamic'] ?? 0)));

            // المواد الاختيارية (أعلى مادة من: كيمياء، أحياء، تكنولوجيا) - من 100
            $chemistry = min(100, max(0, floatval($scores['chemistry'] ?? 0)));
            $biology   = min(100, max(0, floatval($scores['biology'] ?? 0)));
            $tech      = min(100, max(0, floatval($scores['tech'] ?? 0)));

            $electives = [
                'كيمياء' => $chemistry,
                'أحياء' => $biology,
                'تكنولوجيا' => $tech,
            ];
            arsort($electives);
            $bestElectiveName = array_key_first($electives);
            $bestElectiveScore = reset($electives);

            $total = $math + $physics + $arabic + $english + $islamic + $bestElectiveScore;
            $maxTotal = 700;
            $notes[] = "تم احتساب أعلى مادة اختيارية: {$bestElectiveName} ({$bestElectiveScore} من 100)";
        } elseif ($branch === 'literary') {
            // الفرع الأدبي: عربي (150)، إنجليزي (150)، تاريخ (100)، جغرافيا (100)، إسلامية (100)
            $arabic    = min(150, max(0, floatval($scores['arabic'] ?? 0)));    // اللغة العربية من 150
            $english   = min(150, max(0, floatval($scores['english'] ?? 0)));   // اللغة الإنجليزية من 150
            $history   = min(100, max(0, floatval($scores['history'] ?? 0)));   // التاريخ من 100
            $geography = min(100, max(0, floatval($scores['geography'] ?? 0))); // الجغرافيا من 100
            $islamic   = min(100, max(0, floatval($scores['islamic'] ?? 0)));   // التربية الإسلامية من 100

            // المواد الاختيارية (أعلى مادة من: رياضيات أدبي، ثقافة علمية، تكنولوجيا) - من 100
            $math       = min(100, max(0, floatval($scores['math'] ?? 0)));
            $sciCulture = min(100, max(0, floatval($scores['sci_culture'] ?? 0)));
            $tech       = min(100, max(0, floatval($scores['tech'] ?? 0)));

            $electives = [
                'رياضيات أدبي' => $math,
                'ثقافة علمية' => $sciCulture,
                'تكنولوجيا' => $tech,
            ];
            arsort($electives);
            $bestElectiveName = array_key_first($electives);
            $bestElectiveScore = reset($electives);

            $total = $arabic + $english + $history + $geography + $islamic + $bestElectiveScore;
            $maxTotal = 700;
            $notes[] = "تم احتساب أعلى مادة اختيارية: {$bestElectiveName} ({$bestElectiveScore} من 100)";
        } elseif ($branch === 'business') {
            // فرع الريادة والأعمال
            $islamic = floatval($scores['islamic'] ?? 0);
            $arabic  = floatval($scores['arabic'] ?? 0);
            $english = floatval($scores['english'] ?? 0);
            $projects = floatval($scores['projects'] ?? 0);
            $accounting = floatval($scores['accounting'] ?? 0);

            $math = floatval($scores['math'] ?? 0);
            $mgmt = floatval($scores['mgmt'] ?? 0);
            $tech = floatval($scores['tech'] ?? 0);

            $electives = ['رياضيات' => $math, 'إدارة واقتصاد' => $mgmt, 'تكنولوجيا' => $tech];
            arsort($electives);
            $bestElectiveName = array_key_first($electives);
            $bestElectiveScore = reset($electives);

            $total = $islamic + $arabic + $english + $projects + $accounting + ($scores['extra'] ?? 0) + $bestElectiveScore;
            $maxTotal = 700;
            $notes[] = "تم احتساب أعلى مادة اختيارية: {$bestElectiveName}";
        } else {
            // فروع عامة / صناعي
            $sum = 0;
            foreach ($scores as $s) {
                $sum += floatval($s);
            }
            $total = $sum;
            $maxTotal = count($scores) > 0 ? count($scores) * 100 : 700;
        }

        $percentage = $maxTotal > 0 ? round(($total / $maxTotal) * 100, 2) : 0;
        $status = $percentage >= 50 ? 'ناجح' : 'راسب';

        return [
            'total_score' => $total,
            'max_total' => $maxTotal,
            'percentage' => $percentage,
            'status' => $status,
            'notes' => $notes,
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
