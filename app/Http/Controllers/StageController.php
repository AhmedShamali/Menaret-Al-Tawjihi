<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Http\Requests\StoreStageRequest;
use App\Http\Requests\UpdateStageRequest;

class StageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // حذف أي صفوف قديمة لا تنتمي للتوجيهي لضمان ظهور فروع الثانوية العامة فقط
        try {
            $oldStages = \App\Models\Stage::where('grade_level', '<', 120)->get();
            foreach ($oldStages as $oldStage) {
                $subIds = \App\Models\Subject::where('stage_id', $oldStage->id)->pluck('id');
                \App\Models\EducationalContent::whereIn('subject_id', $subIds)->delete();
                \App\Models\Exam::whereIn('subject_id', $subIds)->orWhere('stage_id', $oldStage->id)->delete();
                \App\Models\Student::where('stage_id', $oldStage->id)->delete();
                \App\Models\Subject::where('stage_id', $oldStage->id)->delete();
                $oldStage->delete();
            }
        } catch (\Throwable $e) {}

        // إذا كانت فروع التوجيهي غير مكتملة، يتم تشغيل السيدر تلقائياً
        if (\App\Models\Stage::where('grade_level', '>=', 120)->count() < 3 || \App\Models\Subject::count() === 0) {
            (new \Database\Seeders\StageSeeder())->run();
            (new \Database\Seeders\SubjectSeeder())->run();
        }

        $stages = \App\Models\Stage::withCount('subjects')
            ->with(['subjects' => function ($q) {
                $q->select('id', 'stage_id', 'name_ar', 'color', 'icon');
            }])
            ->where('grade_level', '>=', 120)
            ->orderBy('grade_level', 'desc')
            ->get();

        return view('stages.index', compact('stages'));
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
    public function store(StoreStageRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // إذا لم تكن البيانات مهيئة، تشغيل السيدر لضمان وجود الفروع
        if (\App\Models\Stage::where('grade_level', '>=', 120)->count() < 3 || \App\Models\Subject::count() === 0) {
            (new \Database\Seeders\StageSeeder())->run();
            (new \Database\Seeders\SubjectSeeder())->run();
        }

        $stage = \App\Models\Stage::with(['subjects' => function ($q) {
            $q->withCount(['contents', 'exams']);
        }])
        ->where(function ($q) use ($id) {
            $q->where('id', $id)->orWhere('grade_level', $id);
        })
        ->firstOrFail();

        return view('stages.show', compact('stage'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stage $stage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStageRequest $request, Stage $stage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stage $stage)
    {
        //
    }
}
