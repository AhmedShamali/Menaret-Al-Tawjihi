<?php

namespace App\Http\Controllers;

use App\Models\EducationalContent;
use App\Models\Stage;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EducationalContentController extends Controller
{
    public function index()
    {
        $contents = EducationalContent::with('subject.stage')->latest()->get();
        return view('educational_contents.index', compact('contents'));
    }

    public function create()
    {
         $stages = \App\Models\Stage::with('subjects')->get();
        return view('educational_contents.create', compact('stages'));
    }

  public function store(Request $request)
    {
        // 1. تعريب أسماء الحقول ورسائل الخطأ
        $attributes = [
            'subject_id'   => 'المادة',
            'title'        => 'العنوان',
            'type'         => 'النوع',
            'channel_name' => 'اسم القناة',
            'file_size'    => 'حجم الملف',
            'order'        => 'الترتيب',
            'url_path'     => 'رابط المحتوى',
        ];

        $messages = [
            'required' => 'حقل :attribute مطلوب.',
            'numeric'  => 'يجب أن يكون حقل :attribute رقماً.',
            'min'      => 'حقل :attribute يجب أن يكون على الأقل :min حروف.',
        ];

        // 2. التحقق من البيانات
        $validator = validator($request->all(), [
            'subject_id'   => 'required',
            'title'        => 'required|string|min:3',
            'type'         => 'required',
            'channel_name' => 'required',
            'file_size'    => 'required',
            'order'        => 'required|numeric',
        ], $messages, $attributes);

        if ($validator->fails()) {
            return response()->json([
                'icon'   => 'error',
                'tittle' => $validator->errors()->first(),
            ], 400);
        }

        // 3. إنشاء الكائن وحفظ البيانات
        $content = new EducationalContent();
        $content->subject_id   = $request->subject_id;
        $content->title        = $request->title;
        $content->type         = $request->type;
        $content->channel_name = $request->channel_name;
        $content->file_size    = $request->file_size;
        $content->order        = $request->order;

        // منطق الرفع
        if ($request->type == 'video') {
            if ($request->upload_method == 'local' && $request->hasFile('file_upload_video')) {
                // رفع فيديو من الجهاز
                $path = $request->file('file_upload_video')->store('educational/videos', 'public');
                $content->url_path = $path;
            } else {
                // حفظ الرابط (يوتيوب/درايف)
                $content->url_path = $request->url_path;
            }
        } else {
            // رفع ملف PDF
            if ($request->hasFile('file_upload_pdf')) {
                $path = $request->file('file_upload_pdf')->store('educational/pdfs', 'public');
                $content->url_path = $path;
            }
        }

        $isSaved = $content->save();

        if ($isSaved) {
            return response()->json([
                'icon'   => 'success',
                'tittle' => 'تم حفظ المحتوى بنجاح ✅'
            ], 200);
        }

        return response()->json(['icon' => 'error', 'tittle' => 'فشل الحفظ في قاعدة البيانات'], 500);
    }

    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $content = \App\Models\EducationalContent::findOrFail($id);
        $stages = \App\Models\Stage::with('subjects')->get();
        return view('educational_contents.edit', compact('content', 'stages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // 1. المسميات والرسائل
        $attributes = [
            'subject_id'   => 'المادة',
            'title'        => 'العنوان',
            'type'         => 'النوع',
            'channel_name' => 'اسم القناة',
            'file_size'    => 'حجم الملف',
            'order'        => 'الترتيب',
            'url_path'     => 'الرابط'
        ];

        $messages = [
            'required' => 'حقل :attribute مطلوب.',
            'numeric'  => 'يجب أن يكون حقل :attribute رقماً.',
            'min'      => 'حقل :attribute يجب أن يكون على الأقل :min حروف.',
        ];

        // 2. التحقق من البيانات
        $validator = validator($request->all(), [
            'subject_id'   => 'required',
            'title'        => 'required|string|min:3',
            'type'         => 'required',
            'channel_name' => 'required',
            'file_size'    => 'required',
            'order'        => 'required|numeric',
        ], $messages, $attributes);

        if ($validator->fails()) {
            return response()->json([
                'icon'  => 'error',
                'title' => $validator->errors()->first(),
            ], 400);
        }

        // 3. جلب الكائن وتحديث البيانات
        $content = EducationalContent::findOrFail($id);

        $content->subject_id   = $request->subject_id;
        $content->title        = $request->title;
        $content->type         = $request->type;
        $content->channel_name = $request->channel_name;
        $content->file_size    = $request->file_size;
        $content->order        = $request->order;

        // منطق الرفع الذكي (يحدث فقط إذا تم رفع ملف جديد)
        if ($request->type == 'video') {
            if ($request->upload_method == 'local' && $request->hasFile('file_upload_video')) {
                $content->url_path = $request->file('file_upload_video')->store('educational/videos', 'public');
            } elseif ($request->filled('url_path')) {
                $content->url_path = $request->url_path;
            }
        } else {
            if ($request->hasFile('file_upload_pdf')) {
                $content->url_path = $request->file('file_upload_pdf')->store('educational/pdfs', 'public');
            }
        }

        $isSaved = $content->save();

        if ($isSaved) {
            return response()->json([
                'icon'  => 'success',
                'title' => 'تم تحديث البيانات بنجاح 🚀'
            ], 200);
        }

        return response()->json([
            'icon'  => 'error',
            'title' => 'فشلت عملية الحفظ!'
        ], 500);
    }

    public function destroy($id)
    {
        return response()->json(['success' => \App\Models\EducationalContent::destroy($id)]);

    }
}
