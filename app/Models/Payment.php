<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'transaction_number',
        'gateway',
        'amount',
        'currency',
        'status',
        'payment_details',
        'items',
        'receipt_path'
    ];

    protected $casts = [
        'items' => 'array',
        'amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * اسم بوابة الدفع باللغة العربية
     */
    public function getGatewayNameArAttribute(): string
    {
        return match ($this->gateway) {
            'jawwal_pay' => 'محفظة جوال باي (Jawwal Pay)',
            'palpay'     => 'بال باي (PalPay - محفظتي)',
            'bop'        => 'بنك فلسطين (Bank of Palestine)',
            'voucher'    => 'كارت شحن وتفعيل توجيهي',
            default      => 'دفع إلكتروني معتمد'
        };
    }
}
