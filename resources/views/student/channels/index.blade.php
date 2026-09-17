@extends('layouts.app')

@section('title', __('Interactive Study Channels') . ' | ' . config('app.name'))

@section('content')
<div class="ed-channels-container">

    <!-- رأس الصفحة الأكاديمي -->
    <header class="ed-channels-header">
        <div class="ed-channels-title-box">
            <div class="ed-channels-breadcrumbs">
                <i class="fas fa-home"></i>
                <a href="{{ route('student.dashboard') }}" style="color: inherit; text-decoration: none;">{{ __('Student Portal') }}</a>
                <i class="fas fa-chevron-{{ app()->getLocale() == 'ar' ? 'left' : 'right' }} divider"></i>
                <span class="active">{{ __('Interactive Study Channels') }}</span>
            </div>
            <h1>{{ __('Interactive Study Channels & Communities') }}</h1>
            <p>{{ __('Connect with fellow students and subject teachers in official, safe, and branch-segregated channels.') }}</p>
        </div>

        <div class="ed-channels-badge">
            <i class="fa-solid fa-tower-broadcast"></i>
            <span>{{ __('Official Broadcasts & Inquiries') }} 🇵🇸</span>
        </div>
    </header>

    <div class="ed-channels-grid">
        <!-- كرت القناة الرسمية للفرع -->
        <div class="ed-card ed-channel-main-card">
            <div class="ed-channel-banner">
                <div class="channel-badge-pill">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>{{ __('Monitored by Academic Faculty') }}</span>
                </div>
                <h2>{{ __('Your Assigned Academic Channel') }}</h2>
                <div class="channel-title-tag">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>{{ $student->stage->label_ar ?? ($student->stage->name_ar ?? __('High School')) }} - {{ $student->gender == 'ذكر' ? __('Male Students Channel') : __('Female Students Channel') }}</span>
                </div>
            </div>

            <div class="ed-channel-body">
                <div class="channel-features-list">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fa-solid fa-bullhorn"></i></div>
                        <div>
                            <strong>{{ __('Instant Announcements') }}</strong>
                            <p>{{ __('Receive live updates regarding ministerial schedules, classes, and study materials.') }}</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fa-solid fa-user-shield"></i></div>
                        <div>
                            <strong>{{ __('Safe & Segregated Environment') }}</strong>
                            <p>{{ __('Channels are strictly segregated by gender to ensure maximum privacy and respect.') }}</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
                        <div>
                            <strong>{{ __('Direct Teacher Guidance') }}</strong>
                            <p>{{ __('Ask questions, discuss challenging exam problems, and get verified teacher answers.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="channel-action-box">
                    @if($request_exists)
                        <div class="channel-status-notice approved">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ __('Your join request is submitted. The teacher or supervisor will approve your access shortly.') }}</span>
                        </div>
                    @else
                        <button onclick="requestJoin()" id="joinBtn" class="ed-btn-join">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>{{ __('Request to Join Channel') }}</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- كرت إرشادات الانضمام والتواصل -->
        <div class="ed-card ed-channel-side-card">
            <div class="ed-card-title">
                <i class="fa-solid fa-circle-info"></i>
                <span>{{ __('Channel Guidelines & Conduct') }}</span>
            </div>

            <ul class="channel-rules-list">
                <li>
                    <i class="fa-solid fa-check text-success"></i>
                    <span>{{ __('Adhere strictly to academic and respectful conduct at all times.') }}</span>
                </li>
                <li>
                    <i class="fa-solid fa-check text-success"></i>
                    <span>{{ __('Only post questions and discussions relevant to Tawjihi curriculum.') }}</span>
                </li>
                <li>
                    <i class="fa-solid fa-check text-success"></i>
                    <span>{{ __('Spam, advertising, or inappropriate comments result in immediate ban.') }}</span>
                </li>
            </ul>

            <div class="channel-support-box">
                <i class="fa-solid fa-headset"></i>
                <div>
                    <strong>{{ __('Need Technical Help?') }}</strong>
                    <p>{{ __('Contact platform academic support if you face difficulties joining.') }}</p>
                    <a href="{{ route('student.support') }}" class="ed-btn-support">
                        {{ __('Open Support Ticket') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.ed-channels-container {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    padding: 0 0 60px;
    box-sizing: border-box;
}

/* Header */
.ed-channels-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 16px;
}

.ed-channels-breadcrumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.82rem;
    color: #64748b;
    margin-bottom: 8px;
}

.ed-channels-breadcrumbs .divider {
    font-size: 0.65rem;
    color: #cbd5e1;
}

.ed-channels-breadcrumbs .active {
    color: #1e3a8a;
    font-weight: 600;
}

.ed-channels-title-box h1 {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 6px;
}

.ed-channels-title-box p {
    font-size: 0.88rem;
    color: #64748b;
    margin: 0;
    max-width: 780px;
    line-height: 1.6;
}

.ed-channels-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: #1e3a8a;
    padding: 8px 16px;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: 700;
}

/* Grid */
.ed-channels-grid {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 24px;
    align-items: start;
}

.ed-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
}

.ed-channel-banner {
    background: linear-gradient(135deg, #1e3a8a 0%, #172554 100%);
    border-radius: 14px;
    padding: 28px 24px;
    color: #ffffff;
    margin-bottom: 24px;
    box-shadow: 0 8px 20px rgba(30, 58, 138, 0.2);
}

.channel-badge-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 0.76rem;
    font-weight: 700;
    color: #dbeafe;
    margin-bottom: 12px;
}

.ed-channel-banner h2 {
    font-size: 1.4rem;
    font-weight: 800;
    margin: 0 0 10px;
}

.channel-title-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    color: #1e3a8a;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 800;
}

/* Features */
.channel-features-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 24px;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 14px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}

.feature-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #eff6ff;
    color: #1e3a8a;
    display: grid;
    place-items: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}

.feature-item strong {
    display: block;
    font-size: 0.92rem;
    color: #0f172a;
    margin-bottom: 2px;
}

.feature-item p {
    margin: 0;
    font-size: 0.82rem;
    color: #64748b;
    line-height: 1.5;
}

/* Actions */
.ed-btn-join {
    width: 100%;
    padding: 14px;
    background: #1e3a8a;
    color: #ffffff;
    border: none;
    border-radius: 12px;
    font-size: 0.95rem;
    font-weight: 800;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    box-shadow: 0 4px 14px rgba(30, 58, 138, 0.25);
    transition: all 0.2s;
}

.ed-btn-join:hover {
    background: #172554;
    transform: translateY(-1px);
}

.channel-status-notice {
    padding: 14px;
    border-radius: 12px;
    font-size: 0.88rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.channel-status-notice.approved {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}

/* Side Card */
.ed-card-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
    margin-bottom: 16px;
}

.channel-rules-list {
    list-style: none;
    padding: 0;
    margin: 0 0 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.channel-rules-list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 0.85rem;
    color: #334155;
    line-height: 1.6;
}

.channel-rules-list li i {
    margin-top: 4px;
    flex-shrink: 0;
}

.channel-support-box {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
}

.channel-support-box i {
    font-size: 1.5rem;
    color: #1e3a8a;
    margin-top: 2px;
}

.channel-support-box strong {
    display: block;
    font-size: 0.9rem;
    color: #0f172a;
    margin-bottom: 4px;
}

.channel-support-box p {
    font-size: 0.8rem;
    color: #64748b;
    margin: 0 0 10px;
    line-height: 1.5;
}

.ed-btn-support {
    display: inline-block;
    padding: 6px 12px;
    background: #1e3a8a;
    color: #ffffff;
    text-decoration: none;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 700;
}

@media (max-width: 860px) {
    .ed-channels-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function requestJoin() {
        axios.post("{{ route('student.channels.join') }}").then(res => {
            Swal.fire({ 
                icon: res.data.icon || 'success', 
                title: res.data.title || '{{ __("Request Sent Successfully") }}',
                confirmButtonColor: '#1e3a8a'
            }).then(() => location.reload());
        });
    }
</script>
@endsection
