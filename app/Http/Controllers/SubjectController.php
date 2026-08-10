<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\EducationalContent;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stages = \App\Models\Stage::with('subjects')->get();
        return view('subjects.index', compact('stages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request)
    {
       $data = $request->validated();

    // إذا لم يحدد المستخدم معلماً، اجعله المعلم الحالي أو معلم افتراضي رقم 1
    $data['user_id'] = $request->user_id ?? auth()->id() ?? 1;

    Subject::create($data);

    return redirect()->route('subjects.index');
    }

    public function files($id)
{
    $subject = Subject::findOrFail($id);

    // تجلب فقط العناصر التي تحتوي على مسار PDF
    $files = EducationalContent::where('subject_id', $id)
        ->whereNotNull('pdf_path')
        ->where('pdf_path', '!=', '')
        ->orderBy('order', 'asc')
        ->get();

    return view('subjects.files', compact('subject', 'files'));
}
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $subject = Subject::with(['stage', 'teacher', 'contents'])->findOrFail($id);
        // 1. جلب الفيديوهات (أي عنصر يحتوي على رابط فيديو أو مسار فيديو محلي)
        $videos = $subject->contents->filter(function ($item) {
            return !empty($item->url_path);
        })->sortBy('order');

        // 2. جلب الكتب والمطبوعات (أي عنصر يحتوي على مسار ملف PDF)
        $files = $subject->contents->filter(function ($item) {
            return !empty($item->pdf_path);
        })->sortBy('order');

        return view('subjects.show', compact('subject', 'videos', 'files'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        //
    }
}
