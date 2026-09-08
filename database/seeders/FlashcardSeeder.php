<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flashcard;

class FlashcardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cards = [
            // فيزياء - علمي
            [
                'subject_name' => 'فيزياء',
                'branch' => 'scientific',
                'category' => 'التيار الكهربائي وقوانين كيرشوف',
                'front_text' => 'ما هو نص قانون كيرشوف الأول (قانون العقدة / حفظ الشحنة)؟',
                'back_text' => 'المجموع الجبري للتيارات الداخلة إلى أي نقطة تفرع (عقدة) في دارة كهربائية مغلقة يساوي المجموع الجبري للتيارات الخارجة منها: Σ I_in = Σ I_out.',
                'difficulty' => 'easy',
            ],
            [
                'subject_name' => 'فيزياء',
                'branch' => 'scientific',
                'category' => 'التيار الكهربائي وقوانين كيرشوف',
                'front_text' => 'ما هو نص قانون كيرشوف الثاني (قانون الحلقات / حفظ الطاقة)؟',
                'back_text' => 'المجموع الجبري للتغيرات في فرق الجهد عبر مسار مغلق (حلقة) في دارة كهربائية يساوي صفراً: Σ ΔV = 0 (أو Σ ε = Σ I·R).',
                'difficulty' => 'medium',
            ],
            [
                'subject_name' => 'فيزياء',
                'branch' => 'scientific',
                'category' => 'المجال المغناطيسي',
                'front_text' => 'ما هي القوة المغناطيسية المؤثرة على شحنة متحركة داخل مجال مغناطيسي (قوة لورنتز)؟',
                'back_text' => 'F_B = |q| · v · B · sin(θ) \nحيث θ هي الزاوية المحصورة بين متجه السرعة (v) ومتجه المجال المغناطيسي (B).',
                'difficulty' => 'easy',
            ],
            [
                'subject_name' => 'فيزياء',
                'branch' => 'scientific',
                'category' => 'الحث الكهرومغناطيسي',
                'front_text' => 'ما هو قانون فاراداي في الحث الكهرومغناطيسي؟',
                'back_text' => 'القوة الدافعة الكهربائية الحثية المتولدة في ملف تتناسب طردياً مع المعدل الزمني للتغير في التدفق المغناطيسي الذي يخترقه: ε = - N · (ΔΦ / Δt).',
                'difficulty' => 'medium',
            ],

            // رياضيات - علمي
            [
                'subject_name' => 'رياضيات',
                'branch' => 'scientific',
                'category' => 'التفاضل والتكامل',
                'front_text' => 'ما هي مشتقة الاقتران الأسي الطبيعي: f(x) = e^(g(x))؟',
                'back_text' => 'f\'(x) = g\'(x) · e^(g(x)) \n(مشتقة الأس مضروبة في نفس الاقتران الأسي).',
                'difficulty' => 'easy',
            ],
            [
                'subject_name' => 'رياضيات',
                'branch' => 'scientific',
                'category' => 'التفاضل والتكامل',
                'front_text' => 'ما هي قاعدة التكامل بالأجزاء (Integration by Parts)؟',
                'back_text' => '∫ u · dv = u · v - ∫ v · du \nيستخدم عند تكامل حاصل ضرب اقترانين لا تربطهما علاقة اشتقاق مباشرة (مثل كثير حدود في دائري أو أسي).',
                'difficulty' => 'medium',
            ],
            [
                'subject_name' => 'رياضيات',
                'branch' => 'scientific',
                'category' => 'المتطابقات المثلثية',
                'front_text' => 'ما هي متطابقة جيب ضعف الزاوية: sin(2θ)؟',
                'back_text' => 'sin(2θ) = 2 · sin(θ) · cos(θ).',
                'difficulty' => 'easy',
            ],

            // كيمياء - علمي
            [
                'subject_name' => 'كيمياء',
                'branch' => 'scientific',
                'category' => 'الاتزان الكيميائي',
                'front_text' => 'ما هو نص مبدأ لوشاتيليه (Le Chatelier\'s Principle)؟',
                'back_text' => 'إذا حدث تغير في أحد العوامل المؤثرة على نظام في حالة اتزان (مثل التركيز، أو درجة الحرارة، أو الضغط)، فإن النظام يزاح في الاتجاه الذي يقلل من تأثير هذا التغير.',
                'difficulty' => 'medium',
            ],
            [
                'subject_name' => 'كيمياء',
                'branch' => 'scientific',
                'category' => 'الحموض والقواعد',
                'front_text' => 'كيف يحسب الرقم الهيدروجيني (pH) والرقم الهيدروكسيلي (pOH)؟',
                'back_text' => 'pH = - log [H3O+] \npOH = - log [OH-] \nوالعلاقة بينهما عند 25°C: pH + pOH = 14.',
                'difficulty' => 'easy',
            ],

            // تاريخ - أدبي وعام
            [
                'subject_name' => 'تاريخ',
                'branch' => 'literary',
                'category' => 'تاريخ فلسطين الحديث والمعاصر',
                'front_text' => 'في أي عام صدر وعد بلفور المشؤوم ومن أصدره؟',
                'back_text' => 'صدر في 2 نوفمبر 1917م، وأصدره وزير خارجية بريطانيا آرثر بلفور إلى اللورد ليونيل روتشيلد.',
                'difficulty' => 'easy',
            ],
            [
                'subject_name' => 'تاريخ',
                'branch' => 'literary',
                'category' => 'تاريخ فلسطين الحديث والمعاصر',
                'front_text' => 'متى انطلقت الثورة الفلسطينية الكبرى وما هي أهم مراحلها؟',
                'back_text' => 'انطلقت في أبريل 1936م، بدأت بإضراب عام استمر 6 أشهر (أطول إضراب في التاريخ)، ثم تحولت إلى كفاح مسلح استمر حتى عام 1939م.',
                'difficulty' => 'medium',
            ],
            [
                'subject_name' => 'تاريخ',
                'branch' => 'literary',
                'category' => 'تاريخ فلسطين الحديث والمعاصر',
                'front_text' => 'ما هو تاريخ معركة الكرامة الخالدة وما نتيجتها؟',
                'back_text' => 'وقعت في 21 مارس 1968م، وشكلت أول انتصار عربي وفلسطيني حاسم حطم أسطورة "الجيش الذي لا يقهر".',
                'difficulty' => 'easy',
            ],

            // لغة عربية - مشترك
            [
                'subject_name' => 'لغة عربية',
                'branch' => 'all',
                'category' => 'النحو والإعراب',
                'front_text' => 'ما هو عمل (إن وأخواتها) في الجملة الاسمية؟',
                'back_text' => 'أحرف ناسخة تدخل على الجملة الاسمية فـ (تنصب المبتدأ ويسمى اسمها) و (ترفع الخبر ويسمى خبرها). أخواتها: أن، كأن، لكن، ليت، لعل.',
                'difficulty' => 'easy',
            ],
            [
                'subject_name' => 'لغة عربية',
                'branch' => 'all',
                'category' => 'البلاغة والعروض',
                'front_text' => 'ما هو التشبيه البليغ؟',
                'back_text' => 'هو تشبيه حُذفت منه أداة التشبيه ووجه الشبه معاً، وبقي فقط المشبه والمشبه به (مثال: "العلمُ نورٌ").',
                'difficulty' => 'easy',
            ],
        ];

        foreach ($cards as $card) {
            Flashcard::updateOrCreate(
                [
                    'subject_name' => $card['subject_name'],
                    'front_text' => $card['front_text'],
                ],
                $card
            );
        }
    }
}
