<?php

namespace App\Http\Controllers;

use App\Models\{Student, Subject, EducationalContent, Exam};
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $stats = [
            'students' => Student::count(),
            'subjects' => Subject::count(),
            'lessons'  => EducationalContent::count(),
            'exams'    => Exam::count(),
        ];
        return view('welcome', compact('stats'));
    }

    public function faq() { return view('public.faq'); }
    public function contact() { return view('public.contact'); }
    public function terms() { return view('public.terms'); }
    public function privacy() { return view('public.privacy'); }
}


