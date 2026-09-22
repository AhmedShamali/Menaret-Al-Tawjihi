<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('notifications')) {
            return;
        }

        try {
            $notifications = DB::table('notifications')->get();
            foreach ($notifications as $notif) {
                $data = is_array($notif->data) ? $notif->data : json_decode($notif->data, true);
                if (!$data || !is_array($data)) continue;

                $title = $data['title'] ?? '';
                $msg   = $data['message'] ?? '';
                $type  = $data['type'] ?? '';
                $url   = $data['action_url'] ?? $data['url'] ?? '';

                if (str_contains($title, 'شكوى') || 
                    str_contains($title, 'استفسار') || 
                    str_contains($title, 'تذكرة') || 
                    str_contains($msg, 'شكوى') || 
                    str_contains($msg, 'تذكرة') ||
                    $type === 'support') {

                    if (str_contains($url, 'inbox') || str_contains($url, 'messages')) {
                        $data['action_url'] = url('admin/academic-inquiries');
                        DB::table('notifications')->where('id', $notif->id)->update([
                            'data' => json_encode($data, JSON_UNESCAPED_UNICODE)
                        ]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore any issues during data migration
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed
    }
};
