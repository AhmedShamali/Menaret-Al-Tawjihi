@extends('layouts.app')

@section('title', 'الملفات والملخصات - ' . ($subject->name_ar ?? $subject->title))

@section('content')
<div class="files-page-container">

    <!-- Header Section & Breadcrumb -->
    <div class="files-header-card">
        <nav class="breadcrumb-nav">
            <a href="/stages">{{ __('المراحل التعليمية') }}</a> /
            <a href="/stages/{{ $subject->stage_id }}">{{ $subject->stage->label_ar ?? 'المرحلة' }}</a> /
            <a href="{{ route('subject.show', $subject->id) }}">{{ $subject->name_ar ?? $subject->title }}</a> /
            <span class="active">{{ __('الملفات والملخصات') }}</span>
        </nav>

        <div class="header-main">
            <div class="header-info">
                <h1 class="page-title">
                    <span class="title-icon">📑</span> المكتبة الرقمية: {{ $subject->name_ar ?? $subject->title }}
                </h1>
                <p class="page-subtitle">
                    @if($subject->hasAssignedTeacher())
                        جميع أوراق العمل، الكتب، والملخصات المعتمدة من أ. {{ $subject->teacher_display_name }}
                    @else
                        جميع أوراق العمل، الكتب، والملخصات المعتمدة لمساق {{ $subject->name_ar }}
                    @endif
                </p>
            </div>

            <a href="{{ route('subject.show', $subject->id) }}" class="btn-back-subject">
                <span>←</span>{{ __('العودة للمادة') }}</a>
        </div>
    </div>

    <!-- Quick Stats Bar & Search Controls -->
    <div class="files-toolbar">
        <div class="toolbar-stats">
            <span class="stat-item"><strong>{{ $files->count() }}</strong>{{ __('ملف متوفر') }}</span>
        </div>

        <div class="toolbar-search">
            <input type="text" id="fileSearchInput" onkeyup="filterFiles()" placeholder="{{ __('ابحث باسم الملف أو الملخص...') }}">
        </div>
    </div>

    <!-- Main Files Grid -->
    <div class="files-grid" id="filesGrid">
        @forelse($files as $file)
            @php
                $filePath = $file->pdf_path;
                $fileUrl = filter_var($filePath, FILTER_VALIDATE_URL) ? $filePath : asset('storage/' . $filePath);
                $ext = $file->file_extension ?? pathinfo($filePath, PATHINFO_EXTENSION) ?: 'pdf';
                $meta = $file->file_meta ?? [
                    'icon' => 'fa-solid fa-file-pdf',
                    'color' => '#ef4444',
                    'bg' => '#fef2f2',
                    'label' => strtoupper($ext)
                ];
            @endphp

            <div class="file-card-item" data-title="{{ strtolower($file->title) }}">
                <div class="file-type-icon" style="background: {{ $meta['bg'] }}; color: {{ $meta['color'] }}; display: grid; place-items: center; width: 44px; height: 44px; border-radius: 12px; font-size: 1.3rem;">
                    <i class="{{ $meta['icon'] }}"></i>
                </div>

                <div class="file-info-body">
                    <h3 class="file-title" title="{{ $file->title }}">{{ $file->title }}</h3>
                    <div class="file-meta-tags">
                        <span class="tag-badge format-badge" style="color: {{ $meta['color'] }}; background: {{ $meta['bg'] }}; font-weight: 700;">{{ $meta['label'] }}</span>
                        <span class="tag-badge size-badge">{{ $file->file_size ?? 'غير محدد' }}</span>
                        @if($file->created_at)
                            <span class="file-date">{{ $file->created_at->format('Y-m-d') }}</span>
                        @endif
                    </div>
                </div>

                <div class="file-action-buttons">
                    <a href="{{ $fileUrl }}" target="_blank" class="btn-action-view" title="{{ __('معاينة الملف') }}" style="display:inline-flex; align-items:center; gap:6px;">
                        <i class="fa-regular fa-eye"></i>{{ __('معاينة') }}</a>
                    <a href="{{ route('content.download', $file->id) }}" class="btn-action-download" title="{{ __('تحميل مباشر إلى جهازك') }}" style="display:inline-flex; align-items:center; gap:6px; background: var(--ed-primary); color: #fff;">
                        <i class="fa-solid fa-cloud-arrow-down"></i>{{ __('تحميل') }}</a>
                </div>
            </div>
        @empty
            <div class="empty-state-card">
                <div class="empty-illustration">📂</div>
                <h3>{{ __('لا توجد ملفات أو ملخصات مرفوعة حالياً') }}</h3>
                <p>{{ __('لم يقم المعلم بفرز أو رفع أي أوراق عمل لهذه المادة بعد. تحقق لاحقاً!') }}</p>
                <a href="{{ route('subject.show', $subject->id) }}" class="btn-primary-return">{{ __('العودة لصفحة الدروس') }}</a>
            </div>
        @endforelse
    </div>

</div>

<!-- Component Styles -->
<style>
    :root {
        --primary-color: #2563eb;
        --primary-hover: #1d4ed8;
        --text-dark: #0f172a;
        --text-muted: #64748b;
        --bg-card: #ffffff;
        --bg-page: #f8fafc;
        --border-color: #e2e8f0;
    }

    .files-page-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* Header Styling */
    .files-header-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }

    .breadcrumb-nav {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 12px;
    }

    .breadcrumb-nav a {
        color: var(--text-muted);
        text-decoration: none;
    }

    .breadcrumb-nav a:hover {
        color: var(--primary-color);
    }

    .breadcrumb-nav .active {
        color: var(--primary-color);
        font-weight: 700;
    }

    .header-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0 0 6px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .page-subtitle {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin: 0;
    }

    .btn-back-subject {
        background-color: #f1f5f9;
        color: var(--text-dark);
        padding: 10px 18px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.88rem;
        transition: background-color 0.2s;
    }

    .btn-back-subject:hover {
        background-color: #e2e8f0;
    }

    /* Toolbar Styling */
    .files-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .toolbar-stats {
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .toolbar-stats strong {
        color: var(--primary-color);
    }

    .toolbar-search input {
        width: 280px;
        padding: 10px 16px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        outline: none;
        font-size: 0.88rem;
        transition: border-color 0.2s;
    }

    .toolbar-search input:focus {
        border-color: var(--primary-color);
    }

    /* Grid Layout */
    .files-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 18px;
    }

    .file-card-item {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .file-card-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.04);
    }

    .file-type-icon {
        font-size: 2.2rem;
    }

    .file-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0 0 8px 0;
        line-height: 1.4;
    }

    .file-meta-tags {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .tag-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
    }

    .format-badge {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .size-badge {
        background-color: #f1f5f9;
        color: var(--text-muted);
    }

    .file-date {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .file-action-buttons {
        display: flex;
        gap: 10px;
        border-top: 1px solid #f1f5f9;
        padding-top: 14px;
    }

    .btn-action-view, .btn-action-download {
        flex: 1;
        text-align: center;
        padding: 8px 0;
        border-radius: 10px;
        text-decoration: none;
        font-size: 0.82rem;
        font-weight: 700;
        transition: opacity 0.2s;
    }

    .btn-action-view {
        background-color: #f1f5f9;
        color: var(--text-dark);
    }

    .btn-action-download {
        background-color: var(--primary-color);
        color: #ffffff;
    }

    .btn-action-view:hover, .btn-action-download:hover {
        opacity: 0.9;
    }

    /* Empty State */
    .empty-state-card {
        grid-column: 1 / -1;
        background: var(--bg-card);
        border: 2px dashed var(--border-color);
        border-radius: 20px;
        padding: 50px 20px;
        text-align: center;
        color: var(--text-muted);
    }

    .empty-illustration {
        font-size: 3.5rem;
        margin-bottom: 12px;
    }

    .empty-state-card h3 {
        color: var(--text-dark);
        margin: 0 0 6px 0;
    }

    .btn-primary-return {
        display: inline-block;
        margin-top: 16px;
        background-color: var(--primary-color);
        color: #ffffff;
        padding: 10px 22px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.88rem;
    }
</style>

<!-- JS Script for Live Filter -->
<script>
    function filterFiles() {
        const query = document.getElementById('fileSearchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.file-card-item');

        cards.forEach(card => {
            const title = card.getAttribute('data-title');
            if (title.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection
