<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines (Arabic)
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'يجب قبول :attribute.',
    'accepted_if'          => 'يجب قبول :attribute عندما يكون :other هو :value.',
    'active_url'           => ':attribute ليس رابطاً صحيحاً.',
    'after'                => 'يجب أن يكون :attribute تاريخاً بعد :date.',
    'after_or_equal'       => 'يجب أن يكون :attribute تاريخاً بعد أو يساوي :date.',
    'alpha'                => 'يجب أن يحتوي :attribute على أحرف فقط.',
    'alpha_dash'           => 'يجب أن يحتوي :attribute على أحرف وأرقام وشرطات فقط.',
    'alpha_num'            => 'يجب أن يحتوي :attribute على أحرف وأرقام فقط.',
    'array'                => 'يجب أن يكون :attribute مصفوفة.',
    'ascii'                => 'يجب أن يحتوي :attribute فقط على حروف ورموز أحادية البايت.',
    'before'               => 'يجب أن يكون :attribute تاريخاً قبل :date.',
    'before_or_equal'      => 'يجب أن يكون :attribute تاريخاً قبل أو يساوي :date.',
    'between'              => [
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'file'    => 'يجب أن يكون حجم :attribute بين :min و :max كيلوبايت.',
        'string'  => 'يجب أن يكون طول :attribute بين :min و :max حرفاً.',
        'array'   => 'يجب أن يحتوي :attribute على ما بين :min و :max عنصراً.',
    ],
    'boolean'              => 'يجب أن تكون قيمة :attribute إما صحيحة أو خاطئة.',
    'confirmed'            => 'تأكيد :attribute غير متطابق.',
    'current_password'     => 'كلمة المرور غير صحيحة.',
    'date'                 => ':attribute ليس تاريخاً صحيحاً.',
    'date_equals'          => 'يجب أن يكون :attribute تاريخاً مساوياً لـ :date.',
    'date_format'          => ':attribute لا يتطابق مع الشكل :format.',
    'decimal'              => 'يجب أن يحتوي :attribute على :decimal خانات عشرية.',
    'declined'             => 'يجب رفض :attribute.',
    'declined_if'          => 'يجب رفض :attribute عندما يكون :other هو :value.',
    'different'            => 'يجب أن يكون :attribute مختلفاً عن :other.',
    'digits'               => 'يجب أن يحتوي :attribute على :digits رقماً.',
    'digits_between'       => 'يجب أن يكون :attribute بين :min و :max رقماً.',
    'dimensions'           => ':attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct'             => 'لحقل :attribute قيمة مكررة.',
    'doesnt_end_with'      => 'يجب ألا ينتهي :attribute بأحد القيم التالية: :values.',
    'doesnt_start_with'    => 'يجب ألا يبدأ :attribute بأحد القيم التالية: :values.',
    'email'                => 'يجب أن يكون :attribute بريداً إلكترونياً صحيحاً.',
    'ends_with'            => 'يجب أن ينتهي :attribute بأحد القيم التالية: :values.',
    'enum'                 => ':attribute المختار غير صالح.',
    'exists'               => ':attribute المختار غير صالح.',
    'file'                 => 'يجب أن يكون :attribute ملفاً.',
    'filled'               => 'حقل :attribute إجباري.',
    'gt'                   => [
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من :value.',
        'file'    => 'يجب أن يكون حجم :attribute أكبر من :value كيلوبايت.',
        'string'  => 'يجب أن يتجاوز طول :attribute :value حرفاً.',
        'array'   => 'يجب أن يحتوي :attribute على أكثر من :value عنصراً.',
    ],
    'gte'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من أو تساوي :value.',
        'file'    => 'يجب أن يكون حجم :attribute أكبر من أو يساوي :value كيلوبايت.',
        'string'  => 'يجب ألا يقل طول :attribute عن :value حرفاً.',
        'array'   => 'يجب أن يحتوي :attribute على :value عناصر على الأقل.',
    ],
    'image'                => 'يجب أن يكون :attribute صورة.',
    'in'                   => ':attribute المختار غير صالح.',
    'in_array'             => 'حقل :attribute غير موجود في :other.',
    'integer'              => 'يجب أن يكون :attribute عدداً صحيحاً.',
    'ip'                   => 'يجب أن يكون :attribute عنوان IP صحيحاً.',
    'ipv4'                 => 'يجب أن يكون :attribute عنوان IPv4 صحيحاً.',
    'ipv6'                 => 'يجب أن يكون :attribute عنوان IPv6 صحيحاً.',
    'json'                 => 'يجب أن يكون :attribute نصاً من نوع JSON صالحاً.',
    'lowercase'            => 'يجب أن يكون :attribute بحروف صغيرة.',
    'lt'                   => [
        'numeric' => 'يجب أن تكون قيمة :attribute أقل من :value.',
        'file'    => 'يجب أن يكون حجم :attribute أقل من :value كيلوبايت.',
        'string'  => 'يجب أن يقل طول :attribute عن :value حرفاً.',
        'array'   => 'يجب ألا يحتوي :attribute على أكثر من :value عنصراً.',
    ],
    'lte'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute أقل من أو تساوي :value.',
        'file'    => 'يجب ألا يتجاوز حجم :attribute :value كيلوبايت.',
        'string'  => 'يجب ألا يتجاوز طول :attribute :value حرفاً.',
        'array'   => 'يجب ألا يحتوي :attribute على أكثر من :value عناصر.',
    ],
    'mac_address'          => 'يجب أن يكون :attribute عنوان MAC صحيحاً.',
    'max'                  => [
        'numeric' => 'يجب ألا تكون قيمة :attribute أكبر من :max.',
        'file'    => 'يجب ألا يتجاوز حجم :attribute :max كيلوبايت.',
        'string'  => 'يجب ألا يتجاوز طول :attribute :max حرفاً.',
        'array'   => 'يجب ألا يحتوي :attribute على أكثر من :max عنصراً.',
    ],
    'max_digits'           => 'يجب ألا يحتوي :attribute على أكثر من :max أرقام.',
    'mimes'                => 'يجب أن يكون :attribute ملفاً من نوع: :values.',
    'mimetypes'            => 'يجب أن يكون :attribute ملفاً من نوع: :values.',
    'min'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute على الأقل :min.',
        'file'    => 'يجب ألا يقل حجم :attribute عن :min كيلوبايت.',
        'string'  => 'يجب ألا يقل طول :attribute عن :min حروف.',
        'array'   => 'يجب أن يحتوي :attribute على الأقل على :min عنصراً.',
    ],
    'min_digits'           => 'يجب أن يحتوي :attribute على :min أرقام على الأقل.',
    'missing'              => 'يجب ألا يكون حقل :attribute موجوداً.',
    'missing_if'           => 'يجب ألا يكون حقل :attribute موجوداً عندما يكون :other هو :value.',
    'missing_unless'       => 'يجب ألا يكون حقل :attribute موجوداً ما لم يكن :other هو :value.',
    'missing_with'         => 'يجب ألا يكون حقل :attribute موجوداً عند وجود :values.',
    'missing_with_all'     => 'يجب ألا يكون حقل :attribute موجوداً عند وجود كل من :values.',
    'multiple_of'          => 'يجب أن يكون :attribute مضاعفاً للرقم :value.',
    'not_in'               => ':attribute المختار غير صالح.',
    'not_regex'            => 'صيغة :attribute غير صالحة.',
    'numeric'              => 'يجب أن يكون :attribute رقماً.',
    'password'             => [
        'letters'       => 'يجب أن يحتوي :attribute على حرف واحد على الأقل.',
        'mixed'         => 'يجب أن يحتوي :attribute على حرف كبير وحرف صغير على الأقل.',
        'numbers'       => 'يجب أن يحتوي :attribute على رقم واحد على الأقل.',
        'symbols'       => 'يجب أن يحتوي :attribute على رمز واحد على الأقل.',
        'uncompromised' => 'قيمة :attribute المدخلة ظهرت في تسريب بيانات سابق. يرجى اختيار قيمة مختلفة.',
    ],
    'present'              => 'يجب أن يكون حقل :attribute موجوداً.',
    'prohibited'           => 'حقل :attribute محظور.',
    'prohibited_if'        => 'حقل :attribute محظور عندما يكون :other هو :value.',
    'prohibited_unless'    => 'حقل :attribute محظور ما لم يكن :other ضمن :values.',
    'prohibits'            => 'حقل :attribute يحظر وجود :other.',
    'regex'                => 'صيغة :attribute غير صالحة.',
    'required'             => 'حقل :attribute مطلوب.',
    'required_array_keys'  => 'حقل :attribute يجب أن يحتوي على مدخلات لـ: :values.',
    'required_if'          => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_if_accepted' => 'حقل :attribute مطلوب عندما يتم قبول :other.',
    'required_unless'      => 'حقل :attribute مطلوب ما لم يكن :other ضمن :values.',
    'required_with'        => 'حقل :attribute مطلوب عند وجود :values.',
    'required_with_all'    => 'حقل :attribute مطلوب عند وجود كل من :values.',
    'required_without'     => 'حقل :attribute مطلوب عند عدم وجود :values.',
    'required_without_all' => 'حقل :attribute مطلوب عند عدم وجود أي من :values.',
    'same'                 => 'يجب أن يتطابق :attribute مع :other.',
    'size'                 => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية لـ :size.',
        'file'    => 'يجب أن يكون حجم :attribute :size كيلوبايت.',
        'string'  => 'يجب أن يحتوي :attribute على :size حرفاً بالضبط.',
        'array'   => 'يجب أن يحتوي :attribute على :size عنصراً.',
    ],
    'starts_with'          => 'يجب أن يبدأ :attribute بأحد القيم التالية: :values.',
    'string'               => 'يجب أن يكون :attribute نصاً.',
    'timezone'             => 'يجب أن يكون :attribute نطاقاً زمنياً صحيحاً.',
    'unique'               => 'قيمة :attribute مستخدمة بالفعل.',
    'uploaded'             => 'فشل في تحميل :attribute.',
    'uppercase'            => 'يجب أن يكون :attribute بحروف كبيرة.',
    'url'                  => 'يجب أن يكون :attribute رابطاً صالحاً.',
    'ulid'                 => 'يجب أن يكون :attribute معرف ULID صحيحاً.',
    'uuid'                 => 'يجب أن يكون :attribute معرف UUID صالحاً.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name'                      => 'الاسم',
        'username'                  => 'اسم المستخدم',
        'email'                     => 'البريد الإلكتروني',
        'first_name'                => 'الاسم الأول',
        'last_name'                 => 'اسم العائلة',
        'password'                  => 'كلمة المرور',
        'password_confirmation'     => 'تأكيد كلمة المرور',
        'city'                      => 'المدينة',
        'country'                   => 'الدولة',
        'address'                   => 'العنوان',
        'phone'                     => 'رقم الهاتف',
        'mobile'                    => 'رقم الجوال',
        'age'                       => 'العمر',
        'sex'                       => 'الجنس',
        'gender'                    => 'النوع',
        'day'                       => 'اليوم',
        'month'                     => 'الشهر',
        'year'                      => 'السنة',
        'hour'                      => 'الساعة',
        'minute'                    => 'الدقيقة',
        'second'                    => 'الثانية',
        'title'                     => 'العنوان',
        'content'                   => 'المحتوى',
        'description'               => 'الوصف',
        'excerpt'                   => 'المقتطف',
        'date'                      => 'التاريخ',
        'time'                      => 'الوقت',
        'available'                 => 'متاح',
        'size'                      => 'الحجم',
        'subject_id'                => 'المادة التعليمية',
        'stage_id'                  => 'المرحلة الدراسية',
        'duration_minutes'          => 'مدة الاختبار',
        'pass_marks'                => 'علامة النجاح',
        'total_marks'               => 'الدرجة الكلية',
        'starts_at'                 => 'تاريخ البدء',
        'ends_at'                   => 'تاريخ الانتهاء',
        'questions'                 => 'الأسئلة',
        'questions.*.type'          => 'نوع السؤال',
        'questions.*.question_text' => 'نص السؤال',
        'questions.*.points'        => 'درجة السؤال',
        'questions.*.image'         => 'صورة السؤال',
        'questions.*.a'             => 'الخيار (A)',
        'questions.*.b'             => 'الخيار (B)',
        'questions.*.c'             => 'الخيار (C)',
        'questions.*.d'             => 'الخيار (D)',
        'questions.*.correct_answer'=> 'الإجابة الصحيحة',
    ],

];
