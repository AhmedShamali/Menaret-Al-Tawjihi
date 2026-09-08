<?php

namespace App\Http\Controllers;

use App\Models\VideoNote;
use App\Models\VideoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoNoteController extends Controller
{
    /**
     * جلب ملاحظات الطالب على محتوى معين
     */
    public function fetchNotes($content_id)
    {
        $studentId = Auth::guard('student')->id() ?? Auth::id();
        if (!$studentId) {
            return response()->json(['notes' => []]);
        }

        $notes = VideoNote::where('student_id', $studentId)
            ->where('educational_content_id', $content_id)
            ->orderBy('timestamp_seconds', 'asc')
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'timestamp_seconds' => $note->timestamp_seconds,
                    'formatted_time' => $note->formatted_timestamp,
                    'note_text' => $note->note_text,
                    'created_at' => $note->created_at->diffForHumans(),
                ];
            });

        $progress = VideoProgress::where('student_id', $studentId)
            ->where('educational_content_id', $content_id)
            ->first();

        return response()->json([
            'notes' => $notes,
            'last_position' => $progress ? $progress->last_position_seconds : 0,
        ]);
    }

    /**
     * حفظ ملاحظة جديدة عند دقيقة معينة
     */
    public function storeNote(Request $request)
    {
        $studentId = Auth::guard('student')->id() ?? Auth::id();
        if (!$studentId) {
            return response()->json(['success' => false, 'message' => 'يرجى تسجيل الدخول كطالب أولاً'], 401);
        }

        $request->validate([
            'educational_content_id' => 'required|exists:educational_contents,id',
            'timestamp_seconds'      => 'required|numeric|min:0',
            'note_text'              => 'required|string|max:1000',
        ]);

        $note = VideoNote::create([
            'student_id'             => $studentId,
            'educational_content_id' => $request->educational_content_id,
            'timestamp_seconds'      => intval($request->timestamp_seconds),
            'note_text'              => $request->note_text,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ الملاحظة بنجاح ✨',
            'note'    => [
                'id' => $note->id,
                'timestamp_seconds' => $note->timestamp_seconds,
                'formatted_time' => $note->formatted_timestamp,
                'note_text' => $note->note_text,
                'created_at' => 'الآن',
            ],
        ]);
    }

    /**
     * حذف ملاحظة
     */
    public function destroyNote($id)
    {
        $studentId = Auth::guard('student')->id() ?? Auth::id();
        $note = VideoNote::where('id', $id)->where('student_id', $studentId)->first();

        if ($note) {
            $note->delete();
            return response()->json(['success' => true, 'message' => 'تم حذف الملاحظة']);
        }

        return response()->json(['success' => false, 'message' => 'الملاحظة غير موجودة'], 404);
    }

    /**
     * حفظ تقدم المشاهدة تلقائياً
     */
    public function saveProgress(Request $request)
    {
        $studentId = Auth::guard('student')->id() ?? Auth::id();
        if (!$studentId) {
            return response()->json(['success' => false], 401);
        }

        $contentId = $request->input('educational_content_id');
        $position = intval($request->input('position_seconds', 0));
        $isCompleted = $request->boolean('is_completed', false);

        VideoProgress::updateOrCreate(
            ['student_id' => $studentId, 'educational_content_id' => $contentId],
            ['last_position_seconds' => $position, 'is_completed' => $isCompleted]
        );

        return response()->json(['success' => true]);
    }
}
