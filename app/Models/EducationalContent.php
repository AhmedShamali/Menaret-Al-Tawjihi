<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalContent extends Model
{
    /** @use HasFactory<\Database\Factories\EducationalContentFactory> */
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'title',
        'type',
        'channel_name',
        'file_size',
        'order',
        'is_visible',
        'url_path',
        'pdf_path', 
    ];

    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    /**
     * استخراج معرف فيديو اليوتيوب YouTube Video ID
     */
    public function getYoutubeIdAttribute()
    {
        if (empty($this->url_path)) return null;
        $pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/|youtube\.com/shorts/)([^"&?/\s]{11})%i';
        if (preg_match($pattern, $this->url_path, $match)) {
            return $match[1];
        }
        return null;
    }

    /**
     * رابط التضمين المباشر لليوتيوب YouTube Embed URL
     */
    public function getYoutubeEmbedUrlAttribute()
    {
        $id = $this->youtube_id;
        return $id ? "https://www.youtube.com/embed/{$id}?rel=0&modestbranding=1" : null;
    }

    /**
     * امتداد الملف المرفق
     */
    public function getFileExtensionAttribute()
    {
        if (empty($this->pdf_path)) return null;
        $clean = parse_url($this->pdf_path, PHP_URL_PATH);
        return strtolower(pathinfo($clean, PATHINFO_EXTENSION) ?: 'pdf');
    }

    /**
     * أيقونة ولون الملف حسب الصيغة
     */
    public function getFileMetaAttribute()
    {
        $ext = $this->file_extension ?? 'pdf';
        return match($ext) {
            'doc', 'docx' => ['icon' => 'fa-solid fa-file-word', 'color' => '#0284c7', 'bg' => '#f0f9ff', 'label' => 'Word'],
            'xls', 'xlsx' => ['icon' => 'fa-solid fa-file-excel', 'color' => '#10b981', 'bg' => '#ecfdf5', 'label' => 'Excel'],
            'ppt', 'pptx' => ['icon' => 'fa-solid fa-file-powerpoint', 'color' => '#f97316', 'bg' => '#fff7ed', 'label' => 'PowerPoint'],
            'zip', 'rar'  => ['icon' => 'fa-solid fa-file-zipper', 'color' => '#8b5cf6', 'bg' => '#f5f3ff', 'label' => 'أرشيف مضغوط'],
            'jpg', 'jpeg', 'png', 'webp' => ['icon' => 'fa-solid fa-file-image', 'color' => '#06b6d4', 'bg' => '#ecfeff', 'label' => 'صورة'],
            'txt'         => ['icon' => 'fa-solid fa-file-lines', 'color' => '#64748b', 'bg' => '#f8fafc', 'label' => 'نص'],
            default       => ['icon' => 'fa-solid fa-file-pdf', 'color' => '#ef4444', 'bg' => '#fef2f2', 'label' => 'PDF'],
        };
    }
}
