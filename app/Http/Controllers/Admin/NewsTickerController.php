<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NewsTickerService;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NewsTickerController extends Controller
{
    /**
     * واجهة إدارة شريط الأخبار العاجلة للمدير
     */
    public function index()
    {
        $items = NewsTickerService::getAll();
        $isEnabled = NewsTickerService::isEnabled();

        return view('admin.news.index', compact('items', 'isEnabled'));
    }

    /**
     * حفظ خبر جديد أو تعديل خبر قائم
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id'             => 'nullable|string',
            'text'           => 'required|string|min:3|max:500',
            'badge'          => 'required|string|max:50',
            'type'           => 'required|string|in:urgent,warning,info,success',
            'url'            => 'nullable|string|max:500',
            'is_active'      => 'nullable|boolean',
            'notify_students'=> 'nullable|boolean',
        ], [
            'text.required'  => 'يرجى كتابة نص الخبر العاجل.',
            'text.min'       => 'نص الخبر قصير جداً.',
            'badge.required' => 'يرجى تحديد وسم التصنيف (مثل: عاجل، إعلان وزاري).',
        ]);

        $item = NewsTickerService::saveItem([
            'text'      => $validated['text'],
            'badge'     => $validated['badge'],
            'type'      => $validated['type'],
            'url'       => $validated['url'] ?? '',
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
        ], $request->input('id'));

        // إرسال إشعار فوري للطلاب إذا طلب المدير ذلك
        if ($request->boolean('notify_students')) {
            try {
                $stages = \App\Models\Stage::all();
                foreach ($stages as $stage) {
                    NotificationService::notifyStageStudents(
                        $stage->id,
                        $validated['badge'] . ' 📢',
                        $validated['text'],
                        'system',
                        $validated['url'] ?? route('home')
                    );
                }
            } catch (\Throwable $e) {}
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الخبر ونشره في الشريط الإخباري بنجاح! 🚀',
                'item'    => $item,
            ]);
        }

        return redirect()->back()->with('success', 'تم حفظ الخبر ونشره في شريط الواجهة الرئيسية بنجاح.');
    }

    /**
     * تفعيل أو إيقاف خبر محدد
     */
    public function toggle($id, Request $request)
    {
        $status = NewsTickerService::toggleItem($id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $status ? 'تم تفعيل ظهور الخبر في شريط الواجهة الرئيسية.' : 'تم إيقاف ظهور هذا الخبر مؤقتاً.',
                'status'  => $status,
            ]);
        }

        return redirect()->back()->with('success', 'تم تحديث حالة الخبر بنجاح.');
    }

    /**
     * حذف خبر
     */
    public function destroy($id, Request $request)
    {
        NewsTickerService::deleteItem($id);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف الخبر من الشريط الإخباري بنجاح.',
            ]);
        }

        return redirect()->back()->with('success', 'تم حذف الخبر بنجاح.');
    }

    /**
     * تشغيل أو تعطيل شريط الأخبار كلياً
     */
    public function toggleGlobal(Request $request)
    {
        $enabled = $request->has('enabled') ? (bool) $request->input('enabled') : !NewsTickerService::isEnabled();
        NewsTickerService::setEnabled($enabled);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $enabled ? 'تم تفعيل ظهور شريط الأخبار في الواجهة الرئيسية.' : 'تم إخفاء وتعطيل شريط الأخبار من الواجهة الرئيسية.',
                'enabled' => $enabled,
            ]);
        }

        return redirect()->back()->with('success', 'تم تحديث حالة شريط الأخبار بنجاح.');
    }
}
