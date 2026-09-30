<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\Subject;
use App\Models\Stage;

class SitemapController extends Controller
{
    /**
     * Generate dynamic XML sitemap for search engines (Google, Bing, etc.)
     */
    public function index(): Response
    {
        $now = now()->toAtomString();

        $staticRoutes = [
            ['url' => route('home'), 'priority' => '1.0', 'freq' => 'daily', 'lastmod' => $now],
            ['url' => route('courses.catalog'), 'priority' => '0.95', 'freq' => 'daily', 'lastmod' => $now],
            ['url' => route('tawjihi.calculator'), 'priority' => '0.95', 'freq' => 'weekly', 'lastmod' => $now],
            ['url' => route('tawjihi.formulas'), 'priority' => '0.85', 'freq' => 'weekly', 'lastmod' => $now],
            ['url' => route('smart.learning.flashcards'), 'priority' => '0.85', 'freq' => 'weekly', 'lastmod' => $now],
            ['url' => route('stages.index'), 'priority' => '0.80', 'freq' => 'weekly', 'lastmod' => $now],
            ['url' => route('subjects.index'), 'priority' => '0.80', 'freq' => 'weekly', 'lastmod' => $now],
            ['url' => route('students.create'), 'priority' => '0.85', 'freq' => 'weekly', 'lastmod' => $now],
            ['url' => route('public.faq'), 'priority' => '0.70', 'freq' => 'monthly', 'lastmod' => $now],
            ['url' => route('public.contact'), 'priority' => '0.70', 'freq' => 'monthly', 'lastmod' => $now],
            ['url' => route('public.terms'), 'priority' => '0.50', 'freq' => 'monthly', 'lastmod' => $now],
            ['url' => route('public.privacy'), 'priority' => '0.50', 'freq' => 'monthly', 'lastmod' => $now],
        ];

        $subjectRoutes = [];
        $stageRoutes = [];

        try {
            // Dynamic Subjects
            $subjects = Subject::all();
            foreach ($subjects as $subject) {
                $subjectRoutes[] = [
                    'url' => route('subject.show', $subject->id),
                    'priority' => '0.80',
                    'freq' => 'weekly',
                    'lastmod' => ($subject->updated_at ?? now())->toAtomString(),
                ];
                $subjectRoutes[] = [
                    'url' => route('subject.files', $subject->id),
                    'priority' => '0.75',
                    'freq' => 'weekly',
                    'lastmod' => ($subject->updated_at ?? now())->toAtomString(),
                ];
            }

            // Dynamic Stages
            $stages = Stage::all();
            foreach ($stages as $stage) {
                $stageRoutes[] = [
                    'url' => route('stages.show', $stage->id),
                    'priority' => '0.80',
                    'freq' => 'weekly',
                    'lastmod' => ($stage->updated_at ?? now())->toAtomString(),
                ];
            }
        } catch (\Throwable $e) {
            // Log or ignore gracefully so sitemap never fails with 500
        }

        $allUrls = array_merge($staticRoutes, $subjectRoutes, $stageRoutes);

        $xml = view('sitemap.index', compact('allUrls'))->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
