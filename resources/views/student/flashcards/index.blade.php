@extends('layouts.app')

@section('title', 'بطاقات الاستذكار السريع والقوانين | منارة التوجيهي')

@section('content')
<div class="ed-fc-container">

    <!-- Header -->
    <header class="ed-fc-header">
        <div class="ed-fc-title-box">
            <div class="ed-fc-breadcrumbs">
                <i class="fas fa-home"></i>
                <a href="{{ route('student.dashboard') }}" style="color: inherit; text-decoration: none;">لوحة الطالب</a>
                <i class="fas fa-chevron-left divider"></i>
                <span class="active">بطاقات الاستذكار السريع</span>
            </div>
            <h1>بطاقات استذكار المفاهيم والقوانين الوزارية</h1>
            <p>راجع القوانين الفيزيائية، المتطابقات الرياضية، والتواريخ التاريخية بلمسة واحدة بأسلوب التكرار المتباعد الذكي.</p>
        </div>

        <div class="ed-fc-hint-tag">
            <i class="fas fa-keyboard"></i>
            <span>اختصار: مسطرة (Space) للقلب • الأسهم للتنقل</span>
        </div>
    </header>

    <!-- تبويبات المواد الدراسية -->
    <div class="ed-fc-subjects-bar">
        @foreach($subjects as $sub)
            <a href="{{ route('student.flashcards.index', ['subject' => $sub]) }}"
               class="ed-fc-sub-pill {{ $activeSubject === $sub ? 'active' : '' }}">
                <i class="fas fa-book-open"></i>
                <span>{{ $sub }}</span>
            </a>
        @endforeach
    </div>

    @if($flashcards->count() > 0)
        <!-- منصة البطاقة ثلاثية الأبعاد (3D Stage) -->
        <div class="ed-fc-stage-wrapper">
            <div class="ed-fc-stage" onclick="flipActiveCard()">
                <div class="ed-fc-inner" id="flashcardInner">
                    
                    <!-- الوجه الأمامي: المفهوم / السؤال -->
                    <div class="ed-fc-face ed-fc-front">
                        <div class="ed-fc-meta-top">
                            <span class="ed-badge ed-badge-blue" id="cardCategory">قوانين</span>
                            <span class="ed-fc-counter" id="cardNumber">بطاقة 1 من {{ $flashcards->count() }}</span>
                        </div>
                        <div class="ed-fc-main-content">
                            <h2 class="ed-fc-question" id="cardFrontText">...</h2>
                        </div>
                        <div class="ed-fc-hint-bottom">
                            <i class="fas fa-hand-pointer"></i> اضغط على البطاقة أو اضغط (Space) لإظهار الإجابة
                        </div>
                    </div>

                    <!-- الوجه الخلفي: القانون / الحل النموذجي -->
                    <div class="ed-fc-face ed-fc-back">
                        <div class="ed-fc-meta-top">
                            <span class="ed-badge ed-badge-emerald"><i class="fas fa-check"></i> الحل النموذجي</span>
                            <span class="ed-fc-counter"><i class="fas fa-lightbulb" style="color: #fbbf24;"></i> نموذج معتمد</span>
                        </div>
                        <div class="ed-fc-main-content">
                            <div class="ed-fc-answer" id="cardBackText">...</div>
                        </div>
                        <div class="ed-fc-hint-bottom">
                            <i class="fas fa-undo-alt"></i> اضغط للعودة إلى وجه السؤال
                        </div>
                    </div>

                </div>
            </div>

            <!-- لوحة التحكم والأزرار -->
            <div class="ed-fc-controls">
                
                <div class="ed-fc-nav-buttons">
                    <button type="button" class="ed-btn ed-btn-outline" onclick="prevCard()">
                        <i class="fas fa-chevron-right"></i>
                        <span>السابقة</span>
                    </button>

                    <button type="button" class="ed-btn ed-btn-primary" onclick="flipActiveCard()" style="padding: 12px 28px;">
                        <i class="fas fa-sync-alt"></i>
                        <span>قلب البطاقة</span>
                    </button>

                    <button type="button" class="ed-btn ed-btn-outline" onclick="nextCard()">
                        <span>التالية</span>
                        <i class="fas fa-chevron-left"></i>
                    </button>
                </div>

                <!-- أزرار الإتقان والتقييم الذاتي -->
                <div class="ed-fc-mastery-buttons">
                    <button type="button" class="ed-mastery-btn mastered" onclick="markMastered()">
                        <i class="fas fa-check-circle"></i>
                        <span>أتقنتها تماماً (<strong id="masteredCount">0</strong>)</span>
                    </button>
                    <button type="button" class="ed-mastery-btn review" onclick="markReview()">
                        <i class="fas fa-history"></i>
                        <span>تحتاج لمراجعة لاحقة</span>
                    </button>
                </div>

                <!-- مؤشر التقدم -->
                <div class="ed-fc-progress-box">
                    <div class="ed-fc-progress-labels">
                        <span>معدل استعراض البطاقات</span>
                        <strong id="progressText">0%</strong>
                    </div>
                    <div class="ed-progress-track">
                        <div class="ed-progress-bar" id="progressBarFill" style="width: 0%;"></div>
                    </div>
                </div>

            </div>
        </div>
    @else
        <div class="ed-empty-card" style="margin-top: 20px;">
            <div class="ed-empty-icon"><i class="fas fa-box-open"></i></div>
            <h3>لا توجد بطاقات استذكار لهذه المادة حالياً</h3>
            <p>اختر مادة دراسية أخرى من الشريط العلوي لاستعراض القوانين والمفاهيم المحفوظة.</p>
        </div>
    @endif

</div>

<style>
    .ed-fc-container {
        padding: 24px 32px 60px;
        direction: rtl;
        font-family: 'Alexandria', 'Tajawal', sans-serif;
    }

    /* Header */
    .ed-fc-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .ed-fc-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: #64748b;
        margin-bottom: 8px;
    }

    .ed-fc-breadcrumbs .divider {
        font-size: 0.65rem;
        color: #cbd5e1;
    }

    .ed-fc-breadcrumbs .active {
        color: #1d4ed8;
        font-weight: 600;
    }

    .ed-fc-title-box h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
    }

    .ed-fc-title-box p {
        font-size: 0.9rem;
        color: #64748b;
        margin: 0;
    }

    .ed-fc-hint-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 600;
    }

    /* Subjects Navigation */
    .ed-fc-subjects-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .ed-fc-sub-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        text-decoration: none;
        color: #334155;
        font-size: 0.88rem;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .ed-fc-sub-pill:hover {
        border-color: #1d4ed8;
        color: #1d4ed8;
        transform: translateY(-2px);
    }

    .ed-fc-sub-pill.active {
        background: #1d4ed8;
        border-color: #1d4ed8;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(29, 78, 216, 0.25);
    }

    /* 3D Flashcard Stage */
    .ed-fc-stage-wrapper {
        max-width: 680px;
        margin: 0 auto;
    }

    .ed-fc-stage {
        perspective: 1200px;
        height: 380px;
        margin-bottom: 24px;
        cursor: pointer;
    }

    .ed-fc-inner {
        position: relative;
        width: 100%;
        height: 100%;
        text-align: center;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        transform-style: preserve-3d;
    }

    .ed-fc-inner.is-flipped {
        transform: rotateY(180deg);
    }

    .ed-fc-face {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        border-radius: 22px;
        padding: 32px 28px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.06);
        border: 1.5px solid #e2e8f0;
    }

    .ed-fc-front {
        background: #ffffff;
        color: #0f172a;
    }

    .ed-fc-back {
        background: #0f172a;
        color: #f8fafc;
        transform: rotateY(180deg);
        border-color: #1e293b;
    }

    .ed-fc-meta-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.82rem;
        font-weight: 700;
    }

    .ed-fc-counter {
        color: #64748b;
        font-size: 0.8rem;
    }

    .ed-fc-back .ed-fc-counter {
        color: #94a3b8;
    }

    .ed-fc-main-content {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 1;
        padding: 20px 10px;
    }

    .ed-fc-question {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.6;
        margin: 0;
    }

    .ed-fc-answer {
        font-size: 1.25rem;
        font-weight: 600;
        color: #f1f5f9;
        line-height: 1.8;
        white-space: pre-line;
    }

    .ed-fc-hint-bottom {
        font-size: 0.78rem;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    /* Controls Panel */
    .ed-fc-controls {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .ed-fc-nav-buttons {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .ed-fc-mastery-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .ed-mastery-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        border-radius: 12px;
        font-family: inherit;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }

    .ed-mastery-btn.mastered {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .ed-mastery-btn.mastered:hover {
        background: #d1fae5;
    }

    .ed-mastery-btn.review {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
    }

    .ed-mastery-btn.review:hover {
        background: #f1f5f9;
    }

    .ed-fc-progress-box {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .ed-fc-progress-labels {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        color: #64748b;
    }

    .ed-fc-progress-labels strong {
        color: #1d4ed8;
    }

    /* Empty Card */
    .ed-empty-card {
        background: #ffffff;
        border: 2px dashed #cbd5e1;
        border-radius: 18px;
        padding: 60px 20px;
        text-align: center;
    }

    .ed-empty-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 16px;
    }

    .ed-empty-card h3 {
        font-size: 1.2rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
    }

    .ed-empty-card p {
        font-size: 0.88rem;
        color: #64748b;
        max-width: 460px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .ed-fc-container {
            padding: 18px 16px 60px;
        }
        .ed-fc-stage {
            height: 340px;
        }
    }
</style>

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
