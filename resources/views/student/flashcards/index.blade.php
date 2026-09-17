@extends('layouts.app')

@section('title', __('Flashcards & Laws') . ' | ' . config('app.name'))

@section('content')
<div class="ed-fc-container">

    <!-- Header -->
    <header class="ed-fc-header">
        <div class="ed-fc-title-box">
            <div class="ed-fc-breadcrumbs">
                <i class="fas fa-home"></i>
                <a href="{{ route('student.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Student Portal') }}</a>
                <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} divider"></i>
                <span class="active">{{ __('Flashcards & Laws') }}</span>
            </div>
            <h1>{{ __('Ministerial Concepts & Laws Flashcards') }}</h1>
            <p>{{ __('Review physics laws, mathematical identities, and key concepts through intelligent spaced repetition.') }}</p>
        </div>

        <div class="ed-fc-hint-tag">
            <i class="fas fa-keyboard"></i>
            <span>{{ __('Shortcuts: Space to flip • Arrow keys to navigate') }}</span>
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

    <!-- شريط الإجراءات: إضافة بطاقة، تعديل، حذف، إخفاء عشوائي، اختبار إلزامي ومجلدات -->
    <div class="ed-fc-actions-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <button type="button" onclick="openAddCardModal()" class="ed-btn ed-btn-primary" style="font-size: 0.82rem; padding: 8px 16px;">
                <i class="fas fa-plus-circle"></i>
                <span>{{ __('Add New Card') }}</span>
            </button>
            @if($flashcards->count() > 0)
                <button type="button" onclick="openEditCardModal()" class="ed-btn ed-btn-outline" style="font-size: 0.82rem; padding: 8px 14px; color: #d97706; border-color: #fde68a;" title="{{ __('Edit Current Card') }}">
                    <i class="fas fa-pen"></i>
                    <span>{{ __('Edit Current') }}</span>
                </button>
                <button type="button" onclick="deleteCurrentCard()" class="ed-btn ed-btn-outline danger" style="font-size: 0.82rem; padding: 8px 14px;" title="{{ __('Delete Current Card') }}">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button type="button" onclick="toggleHideCurrentCard()" class="ed-btn ed-btn-outline" style="font-size: 0.82rem; padding: 8px 14px;" title="{{ __('Hide / Restore') }}">
                    <i class="fas fa-eye-slash"></i>
                    <span>{{ __('Hide / Restore') }}</span>
                </button>
                <button type="button" onclick="shuffleCards()" class="ed-btn ed-btn-outline" style="font-size: 0.82rem; padding: 8px 14px; color: #6366f1; border-color: #c7d2fe;" title="{{ __('Shuffle Cards') }}">
                    <i class="fas fa-random"></i>
                    <span>{{ __('Shuffle') }}</span>
                </button>
            @endif
        </div>

        @if($flashcards->count() > 0)
            <div>
                <button type="button" onclick="startMandatoryQuiz()" class="ed-btn ed-btn-primary" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border: none; font-size: 0.85rem; padding: 9px 20px; box-shadow: 0 4px 12px rgba(5,150,105,0.25);">
                    <i class="fas fa-graduation-cap"></i>
                    <span>{{ __('Smart Quiz Challenge') }} 🎯</span>
                </button>
            </div>
        @endif
    </div>

    <!-- تصفية المجلدات والأقسام -->
    @if($categories->count() > 0)
        <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; background: #ffffff; padding: 10px 16px; border-radius: 12px; border: 1px solid #e2e8f0;">
            <span style="font-size: 0.8rem; font-weight: 700; color: #64748b;"><i class="fas fa-folder-open" style="color: #d97706;"></i> {{ __('Categories:') }}</span>
            <a href="{{ route('student.flashcards.index', ['subject' => $activeSubject]) }}" style="text-decoration: none; padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 700; background: {{ !request('category') ? '#eff6ff' : '#f8fafc' }}; color: {{ !request('category') ? '#1e3a8a' : '#64748b' }}; border: 1px solid {{ !request('category') ? '#bfdbfe' : '#e2e8f0' }};">
                {{ __('All Categories') }}
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('student.flashcards.index', ['subject' => $activeSubject, 'category' => $cat]) }}" style="text-decoration: none; padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 700; background: {{ request('category') === $cat ? '#eff6ff' : '#f8fafc' }}; color: {{ request('category') === $cat ? '#1e3a8a' : '#64748b' }}; border: 1px solid {{ request('category') === $cat ? '#bfdbfe' : '#e2e8f0' }};">
                    {{ $cat }}
                </a>
            @endforeach
        </div>
    @endif

    @if($flashcards->count() > 0)
        <!-- منصة البطاقة ثلاثية الأبعاد (3D Stage) -->
        <div class="ed-fc-stage-wrapper">
            <div class="ed-fc-stage" onclick="flipActiveCard()">
                <div class="ed-fc-inner" id="flashcardInner">
                    
                    <!-- الوجه الأمامي: المفهوم / السؤال -->
                    <div class="ed-fc-face ed-fc-front">
                        <div class="ed-fc-meta-top">
                            <span class="ed-badge ed-badge-blue" id="cardCategory">{{ __('Laws') }}</span>
                            <span class="ed-fc-counter" id="cardNumber">{{ __('Card') }} 1 {{ __('of') }} {{ $flashcards->count() }}</span>
                        </div>
                        <div class="ed-fc-main-content">
                            <h2 class="ed-fc-question" id="cardFrontText">...</h2>
                        </div>
                        <div class="ed-fc-hint-bottom">
                            <i class="fas fa-hand-pointer"></i> {{ __('Click card or press (Space) to reveal solution') }}
                        </div>
                    </div>

                    <!-- الوجه الخلفي: القانون / الحل النموذجي -->
                    <div class="ed-fc-face ed-fc-back">
                        <div class="ed-fc-meta-top">
                            <span class="ed-badge ed-badge-emerald"><i class="fas fa-check"></i> {{ __('Model Solution') }}</span>
                            <span class="ed-fc-counter"><i class="fas fa-lightbulb" style="color: #fbbf24;"></i> {{ __('Standard Model') }}</span>
                        </div>
                        <div class="ed-fc-main-content">
                            <div class="ed-fc-answer" id="cardBackText">...</div>
                        </div>
                        <div class="ed-fc-hint-bottom">
                            <i class="fas fa-undo-alt"></i> {{ __('Click to return to front side') }}
                        </div>
                    </div>

                </div>
            </div>

            <!-- لوحة التحكم والأزرار -->
            <div class="ed-fc-controls">
                
                <div class="ed-fc-nav-buttons">
                    <button type="button" class="ed-btn ed-btn-outline" onclick="prevCard()">
                        <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'right' : 'left' }}"></i>
                        <span>{{ __('Previous') }}</span>
                    </button>

                    <button type="button" class="ed-btn ed-btn-primary" onclick="flipActiveCard()" style="padding: 12px 28px;">
                        <i class="fas fa-sync-alt"></i>
                        <span>{{ __('Flip Card') }}</span>
                    </button>

                    <button type="button" class="ed-btn ed-btn-outline" onclick="nextCard()">
                        <span>{{ __('Next') }}</span>
                        <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}"></i>
                    </button>
                </div>

                <!-- أزرار الإتقان والتقييم الذاتي -->
                <div class="ed-fc-mastery-buttons">
                    <button type="button" class="ed-mastery-btn mastered" onclick="markMastered()">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ __('Mastered Completely') }} (<strong id="masteredCount">0</strong>)</span>
                    </button>
                    <button type="button" class="ed-mastery-btn review" onclick="markReview()">
                        <i class="fas fa-history"></i>
                        <span>{{ __('Needs Revision') }}</span>
                    </button>
                </div>

                <!-- مؤشر التقدم -->
                <div class="ed-fc-progress-box">
                    <div class="ed-fc-progress-labels">
                        <span>{{ __('Review Progress') }}</span>
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
            <h3>{{ __('No flashcards for this subject currently') }}</h3>
            <p>{{ __('Select another subject from the top bar to browse saved formulas and concepts.') }}</p>
        </div>
    @endif

</div>

<style>
    .ed-fc-container {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 0 0 60px;
        box-sizing: border-box;
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
        color: #1e3a8a;
        font-weight: 600;
    }

    .ed-fc-title-box h1 {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px;
    }

    .ed-fc-title-box p {
        font-size: 0.88rem;
        color: #64748b;
        margin: 0;
        max-width: 760px;
        line-height: 1.6;
    }

    .ed-fc-hint-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Subjects Bar */
    .ed-fc-subjects-bar {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 8px;
        margin-bottom: 24px;
        scrollbar-width: thin;
    }

    .ed-fc-sub-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 700;
        white-space: nowrap;
        transition: all 0.2s;
    }

    .ed-fc-sub-pill:hover {
        border-color: #cbd5e1;
        color: #1e3a8a;
    }

    .ed-fc-sub-pill.active {
        background: #1e3a8a;
        border-color: #1e3a8a;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
    }

    /* 3D Stage */
    .ed-fc-stage-wrapper {
        max-width: 780px;
        margin: 0 auto;
    }

    .ed-fc-stage {
        perspective: 1200px;
        height: 380px;
        cursor: pointer;
        margin-bottom: 24px;
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
        width: 100%;
        height: 100%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        padding: 28px 32px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
    }

    .ed-fc-front {
        background: #ffffff;
    }

    .ed-fc-back {
        background: #f8fafc;
        transform: rotateY(180deg);
        border-color: #cbd5e1;
    }

    .ed-fc-meta-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ed-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
    }

    .ed-badge-blue { background: #eff6ff; color: #1e3a8a; }
    .ed-badge-emerald { background: #d1fae5; color: #059669; }

    .ed-fc-counter {
        font-size: 0.78rem;
        color: #94a3b8;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .ed-fc-main-content {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 0;
    }

    .ed-fc-question {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.5;
    }

    .ed-fc-answer {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e3a8a;
        line-height: 1.6;
        white-space: pre-wrap;
    }

    .ed-fc-hint-bottom {
        font-size: 0.78rem;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    /* Controls */
    .ed-fc-controls {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .ed-fc-nav-buttons {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
    }

    .ed-fc-mastery-buttons {
        display: flex;
        justify-content: center;
        gap: 12px;
    }

    .ed-mastery-btn {
        padding: 8px 18px;
        border-radius: 12px;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: 1px solid transparent;
    }

    .ed-mastery-btn.mastered {
        background: #ecfdf5;
        color: #059669;
        border-color: #a7f3d0;
    }

    .ed-mastery-btn.mastered:hover {
        background: #059669;
        color: #ffffff;
    }

    .ed-mastery-btn.review {
        background: #fffbeb;
        color: #d97706;
        border-color: #fde68a;
    }

    .ed-mastery-btn.review:hover {
        background: #d97706;
        color: #ffffff;
    }

    /* Buttons */
    .ed-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 12px;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }

    .ed-btn-primary {
        background: #1e3a8a;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(30, 58, 138, 0.25);
    }

    .ed-btn-primary:hover {
        background: #172554;
    }

    .ed-btn-outline {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
        padding: 10px 18px;
    }

    .ed-btn-outline:hover {
        border-color: #1e3a8a;
        color: #1e3a8a;
    }

    .ed-btn-outline.danger:hover {
        border-color: #ef4444;
        color: #ef4444;
    }

    /* Progress */
    .ed-fc-progress-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 12px 18px;
    }

    .ed-fc-progress-labels {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 8px;
    }

    .ed-fc-progress-labels strong {
        color: #1e3a8a;
    }

    .ed-progress-track {
        height: 6px;
        background: #f1f5f9;
        border-radius: 999px;
        overflow: hidden;
    }

    .ed-progress-bar {
        height: 100%;
        background: #1e3a8a;
        transition: width 0.3s ease;
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

    @media (max-width: 768px) {
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

        document.getElementById('cardCategory').textContent = card.category || '{{ __("General") }}';
        document.getElementById('cardNumber').textContent = `{{ __("Card") }} ${currentIndex + 1} {{ __("of") }} ${flashcards.length}`;
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

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        if (e.code === 'Space') {
            e.preventDefault();
            flipActiveCard();
        } else if (e.code === 'ArrowLeft') {
            '{{ app()->getLocale() }}' === 'ar' ? nextCard() : prevCard();
        } else if (e.code === 'ArrowRight') {
            '{{ app()->getLocale() }}' === 'ar' ? prevCard() : nextCard();
        }
    });

    // Shuffle
    function shuffleCards() {
        if (!flashcards || flashcards.length < 2) return;
        for (let i = flashcards.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [flashcards[i], flashcards[j]] = [flashcards[j], flashcards[i]];
        }
        currentIndex = 0;
        renderCard();
        Swal.fire({
            icon: 'info',
            title: '{{ __("Random Shuffle 🔀") }}',
            text: '{{ __("Cards have been shuffled for a better memory challenge.") }}',
            timer: 1200,
            showConfirmButton: false
        });
    }

    // Add Card Modal
    function openAddCardModal() {
        Swal.fire({
            title: '{{ __("Add New Flashcard ✨") }}',
            html: `
                <div style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; font-family: inherit;">
                    <div style="margin-bottom: 12px;">
                        <label style="font-size: 0.82rem; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">{{ __("Folder / Category") }}</label>
                        <input type="text" id="swalCatInput" placeholder="{{ __('e.g. Newton Laws, Identities...') }}" class="swal2-input" style="width: 100%; margin: 0; font-size: 0.9rem;">
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label style="font-size: 0.82rem; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">{{ __("Front Side (Concept / Question) *") }}</label>
                        <textarea id="swalFrontInput" rows="2" placeholder="{{ __('Write question or concept here...') }}" class="swal2-textarea" style="width: 100%; margin: 0; font-size: 0.9rem; resize: vertical;"></textarea>
                    </div>
                    <div>
                        <label style="font-size: 0.82rem; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">{{ __("Back Side (Law / Model Solution) *") }}</label>
                        <textarea id="swalBackInput" rows="3" placeholder="{{ __('Write law or detailed solution...') }}" class="swal2-textarea" style="width: 100%; margin: 0; font-size: 0.9rem; resize: vertical;"></textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __("Save Card") }}',
            cancelButtonText: '{{ __("Cancel") }}',
            confirmButtonColor: '#1e3a8a',
            preConfirm: () => {
                const front = document.getElementById('swalFrontInput').value.trim();
                const back = document.getElementById('swalBackInput').value.trim();
                const cat = document.getElementById('swalCatInput').value.trim();
                if (!front || !back) {
                    Swal.showValidationMessage('{{ __("Please fill both sides of the card.") }}');
                    return false;
                }
                return { front, back, cat };
            }
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await axios.post("{{ route('student.flashcards.store') }}", {
                        _token: '{{ csrf_token() }}',
                        subject_name: '{{ $activeSubject }}',
                        category: result.value.cat,
                        front_text: result.value.front,
                        back_text: result.value.back
                    });
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("Saved!") }}',
                        text: res.data.message,
                        timer: 1400,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } catch (e) {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Could not save card.") }}' });
                }
            }
        });
    }

    // Edit Current Card Modal
    function openEditCardModal() {
        if (!flashcards || flashcards.length === 0) return;
        const card = flashcards[currentIndex];

        Swal.fire({
            title: '{{ __("Edit Flashcard 📝") }}',
            html: `
                <div style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; font-family: inherit;">
                    <div style="margin-bottom: 12px;">
                        <label style="font-size: 0.82rem; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">{{ __("Folder / Category") }}</label>
                        <input type="text" id="swalEditCatInput" value="${card.category || ''}" class="swal2-input" style="width: 100%; margin: 0; font-size: 0.9rem;">
                    </div>
                    <div style="margin-bottom: 12px;">
                        <label style="font-size: 0.82rem; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">{{ __("Front Side (Concept / Question) *") }}</label>
                        <textarea id="swalEditFrontInput" rows="2" class="swal2-textarea" style="width: 100%; margin: 0; font-size: 0.9rem; resize: vertical;">${card.front_text}</textarea>
                    </div>
                    <div>
                        <label style="font-size: 0.82rem; font-weight: 700; color: #475569; display: block; margin-bottom: 4px;">{{ __("Back Side (Law / Model Solution) *") }}</label>
                        <textarea id="swalEditBackInput" rows="3" class="swal2-textarea" style="width: 100%; margin: 0; font-size: 0.9rem; resize: vertical;">${card.back_text}</textarea>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '{{ __("Update Changes") }}',
            cancelButtonText: '{{ __("Cancel") }}',
            confirmButtonColor: '#d97706',
            preConfirm: () => {
                const front = document.getElementById('swalEditFrontInput').value.trim();
                const back = document.getElementById('swalEditBackInput').value.trim();
                const cat = document.getElementById('swalEditCatInput').value.trim();
                if (!front || !back) {
                    Swal.showValidationMessage('{{ __("Please fill both sides of the card.") }}');
                    return false;
                }
                return { front, back, cat };
            }
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await axios.put(`{{ url('student/flashcards') }}/${card.id}`, {
                        _token: '{{ csrf_token() }}',
                        category: result.value.cat,
                        front_text: result.value.front,
                        back_text: result.value.back
                    });
                    card.category = result.value.cat;
                    card.front_text = result.value.front;
                    card.back_text = result.value.back;
                    renderCard();
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("Updated!") }}',
                        text: res.data.message,
                        timer: 1400,
                        showConfirmButton: false
                    });
                } catch (e) {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Could not update card.") }}' });
                }
            }
        });
    }

    // Delete Current Card
    function deleteCurrentCard() {
        if (!flashcards || flashcards.length === 0) return;
        const card = flashcards[currentIndex];

        Swal.fire({
            title: '{{ __("Delete Flashcard?") }}',
            text: '{{ __("Are you sure you want to permanently delete this card?") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: '{{ __("Yes, delete") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    await axios.delete(`{{ url('student/flashcards') }}/${card.id}`, {
                        data: { _token: '{{ csrf_token() }}' }
                    });
                    flashcards.splice(currentIndex, 1);
                    if (currentIndex >= flashcards.length) currentIndex = Math.max(0, flashcards.length - 1);
                    renderCard();
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("Deleted!") }}',
                        timer: 1200,
                        showConfirmButton: false
                    });
                    if (flashcards.length === 0) location.reload();
                } catch (e) {
                    Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Could not delete card.") }}' });
                }
            }
        });
    }

    // Hide or restore
    async function toggleHideCurrentCard() {
        if (!flashcards || flashcards.length === 0) return;
        const card = flashcards[currentIndex];

        try {
            const res = await axios.post(`{{ url('student/flashcards') }}/${card.id}/toggle-hide`, {
                _token: '{{ csrf_token() }}'
            });
            Swal.fire({
                icon: 'info',
                title: '{{ __("Visibility Updated") }}',
                text: res.data.message,
                timer: 1400,
                showConfirmButton: false
            });
        } catch (e) {
            Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Could not toggle card visibility.") }}' });
        }
    }

    // Mandatory Quiz Challenge
    function startMandatoryQuiz() {
        if (!flashcards || flashcards.length === 0) return;

        let quizCards = [...flashcards];
        for (let i = quizCards.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [quizCards[i], quizCards[j]] = [quizCards[j], quizCards[i]];
        }

        let qIndex = 0;
        let score = 0;

        function showQuizQuestion() {
            if (qIndex >= quizCards.length) {
                const percentage = Math.round((score / quizCards.length) * 100);
                Swal.fire({
                    icon: percentage >= 70 ? 'success' : 'info',
                    title: '{{ __("Quiz Challenge Finished! 🏁") }}',
                    html: `
                        <div style="text-align: center; font-family: inherit;">
                            <div style="font-size: 2.2rem; font-weight: 900; color: #1e3a8a; margin-bottom: 8px;">${score} / ${quizCards.length}</div>
                            <p style="font-size: 1.05rem; font-weight: 700; color: #0f172a;">{{ __("Mastery Percentage:") }} ${percentage}%</p>
                            <p style="font-size: 0.85rem; color: #64748b;">${percentage >= 85 ? '{{ __("Outstanding performance with honors! 🌟") }}' : (percentage >= 60 ? '{{ __("Good job, remember to revise tricky questions.") }}' : '{{ __("Needs more practice and focus.") }}')}</p>
                        </div>
                    `,
                    confirmButtonText: '{{ __("Close Quiz") }}',
                    confirmButtonColor: '#1e3a8a'
                });
                return;
            }

            const currentQ = quizCards[qIndex];

            Swal.fire({
                title: `{{ __("Question") }} (${qIndex + 1} {{ __("of") }} ${quizCards.length})`,
                html: `
                    <div style="text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}; font-family: inherit;">
                        <span style="font-size: 0.75rem; background: #eff6ff; color: #1e3a8a; padding: 3px 8px; border-radius: 6px; font-weight: 700;">${currentQ.category || '{{ __("General") }}'}</span>
                        <div style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 12px 0 16px; line-height: 1.5;">${currentQ.front_text}</div>
                        <div id="quizAnswerReveal" style="display: none; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px; margin-bottom: 12px;">
                            <span style="font-size: 0.75rem; font-weight: 800; color: #15803d; display: block; margin-bottom: 4px;">{{ __("Standard Approved Solution:") }}</span>
                            <div style="font-size: 0.95rem; color: #166534; font-weight: 700; line-height: 1.5;">${currentQ.back_text}</div>
                        </div>
                        <button type="button" id="btnRevealAnswer" onclick="document.getElementById('quizAnswerReveal').style.display = 'block'; this.style.display = 'none';" style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; color: #475569; cursor: pointer; width: 100%; margin-bottom: 10px;">
                            <i class="fas fa-eye"></i> {{ __("Reveal Model Solution") }}
                        </button>
                    </div>
                `,
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: '<i class="fas fa-check"></i> {{ __("I Knew It (+1)") }}',
                denyButtonText: '<i class="fas fa-times"></i> {{ __("I Did Not Know") }}',
                cancelButtonText: '{{ __("End Quiz") }}',
                confirmButtonColor: '#059669',
                denyButtonColor: '#dc2626',
                cancelButtonColor: '#64748b'
            }).then((res) => {
                if (res.isConfirmed) {
                    score++;
                    qIndex++;
                    showQuizQuestion();
                } else if (res.isDenied) {
                    qIndex++;
                    showQuizQuestion();
                }
            });
        }

        showQuizQuestion();
    }
</script>
@endsection
