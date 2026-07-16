<?php

namespace App\Http\Controllers;

use App\Models\Submission_Answer;
use App\Http\Requests\StoreSubmission_AnswerRequest;
use App\Http\Requests\UpdateSubmission_AnswerRequest;

class SubmissionAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreSubmission_AnswerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Submission_Answer $submission_Answer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Submission_Answer $submission_Answer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubmission_AnswerRequest $request, Submission_Answer $submission_Answer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Submission_Answer $submission_Answer)
    {
        //
    }

    public function submit(Request $request, $id) {
        $exam = Exam::with('questions')->findOrFail($id);
        $submission = ExamSubmission::create(['exam_id' => $id, 'student_id' => auth()->id() ?? 1]);
        $total = 0;
        foreach($request->answers as $qId => $val) {
            $q = Question::find($qId);
            $pts = ($q->type == 'mcq' && $val == $q->correct_answer) ? $q->points : 0;
            $total += $pts;
            $path = $request->hasFile("files.$qId") ? $request->file("files.$qId")->store('exam_files', 'public') : null;
            SubmissionAnswer::create(['exam_submission_id'=>$submission->id, 'question_id'=>$qId, 'answer_text'=>is_array($val)?null:$val, 'file_path'=>$path, 'points_awarded'=>$q->type=='mcq'?$pts:null]);
        }
        $submission->update(['total_earned_grade' => $total]);
        return response()->json(['success' => true, 'submission_id' => $submission->id]);
    }
}
