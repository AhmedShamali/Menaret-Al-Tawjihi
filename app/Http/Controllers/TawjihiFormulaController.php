<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TawjihiFormulaController extends Controller
{
    /**
     * استعراض دليل القوانين والقواعد الوزارية الذهبية لطلبة التوجيهي
     */
    public function index(Request $request)
    {
        $categories = [
            'math' => [
                'title' => 'الرياضيات (العلمي والصناعي)',
                'icon' => 'fa-calculator',
                'color' => '#2563eb',
                'sections' => [
                    [
                        'name' => 'قواعد الاشتقاق والتفاضل الأساسية',
                        'items' => [
                            ['name' => 'مشتقة الثابت', 'formula' => '(c)\' = 0', 'desc' => 'مشتقة أي عدد ثابت تساوي صفراً.'],
                            ['name' => 'مشتقة القوة', 'formula' => '(x^n)\' = n \\cdot x^{n-1}', 'desc' => 'تنزيل الأس وإنقاص واحد من القوة.'],
                            ['name' => 'مشتقة حاصل ضرب اقترانين', 'formula' => '(f \\cdot g)\' = f\' \\cdot g + f \\cdot g\'', 'desc' => 'الأول في مشتقة الثاني + الثاني في مشتقة الأول.'],
                            ['name' => 'مشتقة القسمة', 'formula' => '(\\frac{f}{g})\' = \\frac{f\' \\cdot g - f \\cdot g\'}{g^2}', 'desc' => '(المقام × مشتقة البسط - البسط × مشتقة المقام) ÷ مربع المقام.'],
                            ['name' => 'قاعدة السلسلة', 'formula' => '\\frac{dy}{dx} = \\frac{dy}{du} \\cdot \\frac{du}{dx}', 'desc' => 'إيجاد مشتقة الاقتران المركب.'],
                            ['name' => 'مشتقة الجيب', 'formula' => '(\\sin x)\' = \\cos x', 'desc' => 'مع ضرب مشتقة الزاوية دائمًا.'],
                            ['name' => 'مشتقة جيب التمام', 'formula' => '(\\cos x)\' = -\\sin x', 'desc' => 'لاحظ إشارة السالب.'],
                            ['name' => 'مشتقة الظل', 'formula' => '(\\tan x)\' = \\sec^2 x', 'desc' => 'مربع القاطع.'],
                        ]
                    ],
                    [
                        'name' => 'قواعد التكامل والتطبيقات',
                        'items' => [
                            ['name' => 'تكامل القوة', 'formula' => '\\int x^n dx = \\frac{x^{n+1}}{n+1} + C', 'desc' => 'شرط n لا يساوي -1.'],
                            ['name' => 'تكامل الاقتران الأسي الطبيعي', 'formula' => '\\int e^{ax} dx = \\frac{1}{a} e^{ax} + C', 'desc' => 'قسمة على معامل س.'],
                            ['name' => 'تكامل 1/x', 'formula' => '\\int \\frac{1}{x} dx = \\ln|x| + C', 'desc' => 'اللوغاريتم الطبيعي لمطلق س.'],
                            ['name' => 'التكامل بالأجزاء', 'formula' => '\\int u \\, dv = u \\cdot v - \\int v \\, du', 'desc' => 'يستخدم عند ضرب اقترانين لا تربطهما علاقة اشتقاق.'],
                            ['name' => 'حساب المساحة بين منحنيين', 'formula' => 'A = \\int_a^b (f(x) - g(x)) dx', 'desc' => 'الاقتران العلوي ناقص الاقتران السفلي.'],
                        ]
                    ],
                    [
                        'name' => 'المتطابقات المثلثية الهامة',
                        'items' => [
                            ['name' => 'متطابقة فيثاغورس', 'formula' => '\\sin^2 x + \\cos^2 x = 1', 'desc' => 'الأساس لتبسيط كثير من نهايات وتكاملات المثلثي.'],
                            ['name' => 'جيب ضعف الزاوية', 'formula' => '\\sin(2x) = 2 \\sin x \\cos x', 'desc' => 'لتحويل حاصل الضرب إلى زاوية مضاعفة.'],
                            ['name' => 'جيب تمام ضعف الزاوية', 'formula' => '\\cos(2x) = \\cos^2 x - \\sin^2 x = 2\\cos^2 x - 1 = 1 - 2\\sin^2 x', 'desc' => 'ثلاث صيغ هامة جداً في التكامل.'],
                            ['name' => 'متطابقة خفض القوة للـ sin', 'formula' => '\\sin^2 x = \\frac{1 - \\cos(2x)}{2}', 'desc' => 'الحل الأسرع لتكامل sin^2 x.'],
                            ['name' => 'متطابقة خفض القوة للـ cos', 'formula' => '\\cos^2 x = \\frac{1 + \\cos(2x)}{2}', 'desc' => 'الحل الأسرع لتكامل cos^2 x.'],
                        ]
                    ]
                ]
            ],
            'physics' => [
                'title' => 'الفيزياء (العلمي والصناعي)',
                'icon' => 'fa-atom',
                'color' => '#0284c7',
                'sections' => [
                    [
                        'name' => 'الزخم الخطي والدفع والتصادمات',
                        'items' => [
                            ['name' => 'الزخم الخطي', 'formula' => 'p = m \\cdot v', 'desc' => 'كمية متجهة بوحدة (kg.m/s).'],
                            ['name' => 'الدفع', 'formula' => 'I = F \\cdot \\Delta t = \\Delta p = m(v_2 - v_1)', 'desc' => 'الدفع يساوي التغير في الزخم الخطي (مبرهنة الزخم-الدفع).'],
                            ['name' => 'حفظ الزخم الخطي في التصادمات', 'formula' => '\\sum p_i = \\sum p_f \\implies m_1 v_{1i} + m_2 v_{2i} = m_1 v_{1f} + m_2 v_{2f}', 'desc' => 'محفوظ في جميع أنواع التصادمات للأنظمة المعزولة.'],
                            ['name' => 'التصادم المرن', 'formula' => '\\sum KE_i = \\sum KE_f', 'desc' => 'الطاقة الحركية الكلية محفوظة قبل وبعد التصادم.'],
                        ]
                    ],
                    [
                        'name' => 'المجال المغناطيسي والحث الكهرومغناطيسي',
                        'items' => [
                            ['name' => 'القوة المغناطيسية على شحنة متحركة', 'formula' => 'F_B = q \\cdot v \\cdot B \\cdot \\sin\\theta', 'desc' => 'تحدد اتجاهها بقاعدة اليد اليمنى.'],
                            ['name' => 'القوة المغناطيسية على موصل', 'formula' => 'F = I \\cdot L \\cdot B \\cdot \\sin\\theta', 'desc' => 'قوة لورنتز المطبقة على سلك يمر به تيار.'],
                            ['name' => 'التدفق المغناطيسي', 'formula' => '\\Phi = B \\cdot A \\cdot \\cos\\theta', 'desc' => 'يقاس بوحدة الويبر (Weber).'],
                            ['name' => 'قانون فاراداي في الحث', 'formula' => '\\mathcal{E} = -N \\frac{\\Delta \\Phi}{\\Delta t}', 'desc' => 'الإشارة السالبة تمثل قاعدة لينز لمقاومة التغير.'],
                            ['name' => 'القوة الدافعة الحثية في موصل مستقيم', 'formula' => '\\mathcal{E} = B \\cdot L \\cdot v \\cdot \\sin\\theta', 'desc' => 'عند تحريك سلك بسرعة v داخل مجال مغناطيسي.'],
                        ]
                    ],
                    [
                        'name' => 'فيزياء الكم والذرة',
                        'items' => [
                            ['name' => 'طاقة فوتون الضوء', 'formula' => 'E = h \\cdot f = \\frac{h \\cdot c}{\\lambda}', 'desc' => 'حيث h ثابت بلانك (6.63 × 10^-34 J.s).'],
                            ['name' => 'معادلة آينشتاين الكهروضوئية', 'formula' => 'hf = \\phi + KE_{max}', 'desc' => 'طاقة الفوتون = دالة الشغل لفلز المهبط + الطاقة الحركية العظمى للإلكترونات.'],
                            ['name' => 'جهد القطع', 'formula' => 'KE_{max} = e \\cdot V_0', 'desc' => 'الجهد اللازم لإيقاف أسرع الإلكترونات الضوئية.'],
                        ]
                    ]
                ]
            ],
            'chemistry' => [
                'title' => 'الكيمياء العامة والعضوية',
                'icon' => 'fa-flask-vial',
                'color' => '#10b981',
                'sections' => [
                    [
                        'name' => 'سرعة التفاعل والاتزان الكيميائي',
                        'items' => [
                            ['name' => 'قانون سرعة التفاعل العام', 'formula' => 'R = k [A]^x [B]^y', 'desc' => 'حيث x و y رتب المواد المتفاعلة وتُحدد تجريبياً فقط.'],
                            ['name' => 'ثابت الاتزان بدلالة التراكيز', 'formula' => 'K_c = \\frac{[C]^c [D]^d}{[A]^a [B]^b}', 'desc' => 'لا يُكتب في التعبير المواد الصلبة والسوائل النقية.'],
                            ['name' => 'العلاقة بين Kc و Kp', 'formula' => 'K_p = K_c (RT)^{\\Delta n}', 'desc' => 'حيث دلتا n = مجموع مولات الغازات الناتجة - المتفاعلة.'],
                        ]
                    ],
                    [
                        'name' => 'الحموض والقواعد والـ pH',
                        'items' => [
                            ['name' => 'التأين الذاتي للماء', 'formula' => 'K_w = [H_3O^+][OH^-] = 1.0 \\times 10^{-14} \\text{ (عند } 25^\\circ\\text{C)}', 'desc' => 'ثابت في جميع المحاليل المائية.'],
                            ['name' => 'الرقم الهيدروجيني', 'formula' => 'pH = -\\log[H_3O^+]', 'desc' => 'مقياس لحموضة أو قاعدية المحلول من 0 إلى 14.'],
                            ['name' => 'ثابت تأين الحمض الضعيف', 'formula' => 'K_a = \\frac{[H_3O^+][A^-]}{[HA]}', 'desc' => 'كلما زادت قيمة Ka زادت قوة الحمض الضعيف.'],
                        ]
                    ]
                ]
            ],
            'english' => [
                'title' => 'اللغة الإنجليزية (Grammar & Structures)',
                'icon' => 'fa-language',
                'color' => '#8b5cf6',
                'sections' => [
                    [
                        'name' => 'الأزمنة الوزارية الهامة (Key Tenses)',
                        'items' => [
                            ['name' => 'Present Perfect vs Past Simple', 'formula' => 'have/has + V3  vs  V2 (Past Simple)', 'desc' => 'Present perfect for unspecified time or recent events; Past simple with definite past time (yesterday, in 2020, ago).'],
                            ['name' => 'Past Perfect with After / Before', 'formula' => 'After + had + V3 , Subject + V2   |   Before + V2 , Subject + had + V3', 'desc' => 'Had + V3 always describes the earlier action in the past.'],
                            ['name' => 'Reported Speech (Tense Shift)', 'formula' => 'Present Simple → Past Simple | Past Simple → Past Perfect | will → would', 'desc' => 'Shift one tense back into the past when the reporting verb is past (said, told).'],
                        ]
                    ],
                    [
                        'name' => 'الجمل الشرطية (Conditionals)',
                        'items' => [
                            ['name' => 'First Conditional (Possible)', 'formula' => 'If + Present Simple, Subject + will + base form', 'desc' => 'Example: If you study hard, you will pass Tawjihi.'],
                            ['name' => 'Second Conditional (Imaginary/Hypothetical)', 'formula' => 'If + Past Simple, Subject + would + base form', 'desc' => 'Example: If I had more time, I would read the whole book.'],
                            ['name' => 'Third Conditional (Past Regret)', 'formula' => 'If + had + V3, Subject + would have + V3', 'desc' => 'Example: If he had revised early, he would have scored 99%.'],
                        ]
                    ]
                ]
            ]
        ];

        return view('public.tawjihi_formulas', compact('categories'));
    }
}
