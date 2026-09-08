@extends('layouts.app')

@section('title', 'بطاقات الاستذكار السريع والقوانين | توجيهي فلسطين')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Alexandria:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    :root {
        --primary: #2563eb;
        --emerald: #10b981;
        --amber: #f59e0b;
        --rose: #f43f5e;
        --bg-main: #f8fafc;
        --card-bg: #ffffff;
        --border-card: #e2e8f0;
        --text-title: #0f172a;
        --text-body: #334155;
        --text-muted: #64748b;
    }

    * { font-family: 'Alexandria', sans-serif; }

    .flashcards-page-wrapper {
        direction: rtl;
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px 20px 60px;
    }

    .header-banner {
        text-align: center; margin-bottom: 30px;
    }
    .badge-pill {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 6px 16px; background: #eff6ff; border: 1px solid #bfdbfe;
        color: var(--primary); border-radius: 50px; font-size: 0.82rem; font-weight: 700;
        margin-bottom: 12px;
    }
    .header-banner h1 {
        font-size: 2rem; font-weight: 800; color: var(--text-title); margin-bottom: 8px;
    }
    .header-banner p {
        color: var(--text-muted); font-size: 0.95rem; max-width: 600px; margin: 0 auto;
    }

    /* Subject Tabs */
    .subjects-nav {
        display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; margin-bottom: 30px;
    }
    .subject-pill-link {
        padding: 10px 20px; border-radius: 12px; text-decoration: none;
        font-size: 0.9rem; font-weight: 700; background: white;
        border: 1px solid var(--border-card); color: var(--text-body);
        transition: 0.2s ease; display: flex; align-items: center; gap: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .subject-pill-link:hover { border-color: var(--primary); color: var(--primary); }
    .subject-pill-link.active {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white; border-color: transparent; box-shadow: 0 4px 15px rgba(37,99,235,0.3);
    }

    /* 3D Flashcard Stage */
    .flashcard-stage {
        perspective: 1200px;
        max-width: 650px;
        margin: 0 auto 30px;
        height: 380px;
    }
    .flashcard-inner {
        position: relative;
        width: 100%;
        height: 100%;
        text-align: center;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        transform-style: preserve-3d;
        cursor: pointer;
    }
    .flashcard-inner.is-flipped {
        transform: rotateY(180deg);
    }

    .card-face {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        border-radius: 28px;
        padding: 36px 30px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        border: 1.5px solid var(--border-card);
    }

    /* Front Face */
    .card-front {
        background: white;
        color: var(--text-title);
    }
    /* Back Face */
    .card-back {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: white;
        transform: rotateY(180deg);
        border-color: #334155;
    }

    .card-meta-top {
        display: flex; justify-content: space-between; align-items: center;
        font-size: 0.8rem; font-weight: 700; color: var(--text-muted);
    }
    .card-tag {
        background: #f1f5f9; padding: 4px 12px; border-radius: 30px; color: var(--primary);
    }
    .card-back .card-tag {
        background: rgba(255,255,255,0.1); color: #60a5fa;
    }

    .card-content-main {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        flex: 1; padding: 15px 0;
    }
    .card-front .card-text {
        font-size: 1.35rem; font-weight: 800; line-height: 1.6; color: var(--text-title);
    }
    .card-back .card-text {
        font-size: 1.25rem; font-weight: 600; line-height: 1.7; color: #f8fafc; white-space: pre-line;
    }

    .card-hint-bottom {
        font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center;
        justify-content: center; gap: 6px;
    }
    .card-back .card-hint-bottom { color: #94a3b8; }

    /* Controls Toolbar */
    .controls-panel {
        max-width: 650px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px;
    }
    .actions-bar {
        display: flex; justify-content: space-between; align-items: center; gap: 12px;
    }
    .btn-control {
        flex: 1; padding: 12px 18px; border-radius: 12px; font-size: 0.9rem; font-weight: 700;
        cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px;
        border: 1px solid var(--border-card); background: white; color: var(--text-body);
    }
    .btn-control:hover { background: #f8fafc; border-color: #cbd5e1; }
    .btn-flip-primary {
        background: var(--primary); color: white; border: none;
        box-shadow: 0 4px 12px rgba(37,99,235,0.3);
    }
    .btn-flip-primary:hover { background: #1d4ed8; color: white; }

    /* Mastery Buttons */
    .mastery-bar {
        display: flex; gap: 12px;
    }
    .btn-mastered {
        flex: 1; padding: 12px; border-radius: 12px; font-weight: 700; font-size: 0.88rem;
        background: #ecfdf5; border: 1.5px solid #a7f3d0; color: #059669; cursor: pointer;
        transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-mastered:hover { background: #d1fae5; }
    .btn-review {
        flex: 1; padding: 12px; border-radius: 12px; font-weight: 700; font-size: 0.88rem;
        background: #fff1f2; border: 1.5px solid #fecdd3; color: #e11d48; cursor: pointer;
        transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-review:hover { background: #ffe4e6; }

    /* Progress & Counter */
    .progress-bar-wrapper {
        height: 6px; background: #e2e8f0; border-radius: 10px; overflow: hidden; margin-top: 6px;
    }
    .progress-bar-fill {
        height: 100%; background: var(--primary); transition: width 0.3s;
    }
</style>

<div class="flashcards-page-wrapper">
    <!-- Header -->
    <div class="header-banner">
        <div class="badge-pill">
            <i class="fa-solid fa-bolt"></i>
            <span>نظام الحفظ السريع والتكرار المتباعد (Spaced Repetition)</span>
        </div>
        <h1>بطاقات استذكار مفاهيم وقوانين التوجيهي</h1>
        <p>احفظ القوانين الفيزيائية، المتطابقات الرياضية، والتواريخ التاريخية بلمسة واحدة لترسيخها قبل الامتحانات.</p>
    </div>

    <!-- Subject Tabs -->
    <div class="subjects-nav">
        @foreach($subjects as $sub)
            <a href="{{ route('student.flashcards.index', ['subject' => $sub]) }}"
               class="subject-pill-link {{ $activeSubject === $sub ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i> {{ $sub }}
            </a>
        @endforeach
    </div>

    @if($flashcards->count() > 0)
        <!-- Flashcard 3D Stage -->
        <div class="flashcard-stage" onclick="flipActiveCard()">
            <div class="flashcard-inner" id="flashcardInner">
                <!-- الوجه الأمامي (السؤال / المفهوم) -->
                <div class="card-face card-front">
                    <div class="card-meta-top">
                        <span class="card-tag" id="cardCategory">قوانين</span>
                        <span id="cardNumber">بطاقة 1 من {{ $flashcards->count() }}</span>
                    </div>
                    <div class="card-content-main">
                        <div class="card-text" id="cardFrontText">...</div>
                    </div>
                    <div class="card-hint-bottom">
                        <i class="fa-solid fa-hand-pointer"></i> اضغط على البطاقة لقلبها وإظهار الإجابة
                    </div>
                </div>

                <!-- الوجه الخلفي (القانون / الشرح) -->
                <div class="card-face card-back">
                    <div class="card-meta-top">
                        <span class="card-tag">نموذج الحل والشرح</span>
                        <span><i class="fa-solid fa-circle-check" style="color: #34d399;"></i> الإجابة المعتمدة</span>
                    </div>
                    <div class="card-content-main">
                        <div class="card-text" id="cardBackText">...</div>
                    </div>
                    <div class="card-hint-bottom">
                        <i class="fa-solid fa-rotate-left"></i> اضغط للعودة إلى السؤال
                    </div>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div class="controls-panel">
            <div class="actions-bar">
                <button type="button" class="btn-control" onclick="prevCard()">
                    <i class="fa-solid fa-arrow-right"></i> السابقة
                </button>
                <button type="button" class="btn-control btn-flip-primary" onclick="flipActiveCard()">
                    <i class="fa-solid fa-arrows-rotate"></i> قلب البطاقة
                </button>
                <button type="button" class="btn-control" onclick="nextCard()">
                    التالية <i class="fa-solid fa-arrow-left"></i>
                </button>
            </div>

            <div class="mastery-bar">
                <button type="button" class="btn-mastered" onclick="markMastered()">
                    <i class="fa-solid fa-check"></i> أتقنتها تماماً (<span id="masteredCount">0</span>)
                </button>
                <button type="button" class="btn-review" onclick="markReview()">
                    <i class="fa-solid fa-rotate-right"></i> أحتاج مراجعتها
                </button>
            </div>

            <div>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-muted);">
                    <span>مؤشر إنجاز المادة</span>
                    <span id="progressText">0%</span>
                </div>
                <div class="progress-bar-wrapper">
                    <div class="progress-bar-fill" id="progressBarFill" style="width: 0%;"></div>
                </div>
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 60px; background: white; border-radius: 24px; border: 1px solid var(--border-card);">
            <i class="fa-solid fa-box-open" style="font-size: 3rem; color: #94a3b8; margin-bottom: 12px;"></i>
            <h3 style="color: var(--text-title); margin-bottom: 6px;">لا توجد بطاقات متاحة لهذه المادة حالياً</h3>
            <p style="color: var(--text-muted);">اختر مادة أخرى من الأعلى لتجربة بطاقات الاستذكار السريع.</p>
        </div>
    @endif
</div>

<script>
    const flashcards = @json($flashcards);
    let currentIndex = 0;
    let isFlipped = false;
    let masteredIds = new Set();

    function renderCard() {
        if (!flashcards || flashcards.length === 0) return;
        const card = flashcards[currentIndex];

        isFlipped = false;
        document.getElementById('flashcardInner').classList.remove('is-flipped');

        document.getElementById('cardCategory').textContent = card.category || 'عام';
        document.getElementById('cardNumber').textContent = `بطاقة ${currentIndex + 1} من ${flashcards.length}`;
        document.getElementById('cardFrontText').textContent = card.front_text;
        document.getElementById('cardBackText').textContent = card.back_text;

        const percent = Math.round(((currentIndex + 1) / flashcards.length) * 100);
        document.getElementById('progressBarFill').style.width = percent + '%';
        document.getElementById('progressText').textContent = percent + '%';
    }

    function flipActiveCard() {
        isFlipped = !isFlipped;
        document.getElementById('flashcardInner').classList.toggle('is-flipped', isFlipped);
    }

    function nextCard() {
        if (currentIndex < flashcards.length - 1) {
            currentIndex++;
            renderCard();
        } else {
            currentIndex = 0;
            renderCard();
        }
    }

    function prevCard() {
        if (currentIndex > 0) {
            currentIndex--;
            renderCard();
        } else {
            currentIndex = flashcards.length - 1;
            renderCard();
        }
    }

    function markMastered() {
        const card = flashcards[currentIndex];
        masteredIds.add(card.id);
        document.getElementById('masteredCount').textContent = masteredIds.size;
        nextCard();
    }

    function markReview() {
        nextCard();
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderCard();
    });

    // اختصارات لوحة المفاتيح
    document.addEventListener('keydown', (e) => {
        if (e.code === 'Space') {
            e.preventDefault();
            flipActiveCard();
        } else if (e.code === 'ArrowLeft') {
            nextCard();
        } else if (e.code === 'ArrowRight') {
            prevCard();
        }
    });
</script>
@endsection
