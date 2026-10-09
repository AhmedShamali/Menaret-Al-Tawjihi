/**
 * Step by Step - Royal Classic Native Vanilla Dialog & Toast Engine
 * 100% Zero-Dependency, Ultra-Fast Drop-in Replacement for SweetAlert2
 * Designed for World-Class Aesthetics, Classical Royal Elegance, 
 * Responsive Across All Screens (Desktop, Tablet, Mobile) & Mobile App/PWA Ready.
 */
(function() {
    'use strict';

    // Inject styles once
    const styleId = 'stepvoro-native-dialogs-css';
    if (!document.getElementById(styleId)) {
        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = `
            :root {
                --sv-font: 'Tajawal', 'Alexandria', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                --sv-primary: #1d4ed8;
                --sv-primary-dark: #1e3a8a;
                --sv-gold: #d97706;
                --sv-emerald: #059669;
                --sv-ruby: #dc2626;
                --sv-amethyst: #7c3aed;
                --sv-card-bg: #ffffff;
                --sv-text-main: #0f172a;
                --sv-text-muted: #475569;
                --sv-border: #e2e8f0;
            }

            .sv-modal-backdrop {
                position: fixed;
                inset: 0;
                background: radial-gradient(circle at center, rgba(15, 23, 42, 0.6) 0%, rgba(15, 23, 42, 0.8) 100%);
                backdrop-filter: blur(8px) saturate(160%);
                -webkit-backdrop-filter: blur(8px) saturate(160%);
                z-index: 999999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: calc(16px + env(safe-area-inset-top, 0px)) calc(16px + env(safe-area-inset-right, 0px)) calc(16px + env(safe-area-inset-bottom, 0px)) calc(16px + env(safe-area-inset-left, 0px));
                opacity: 0;
                transition: opacity 0.24s cubic-bezier(0.16, 1, 0.3, 1);
                font-family: var(--sv-font);
                direction: rtl;
                box-sizing: border-box;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
            .sv-modal-backdrop.sv-visible {
                opacity: 1;
            }

            .sv-modal-card {
                background: var(--sv-card-bg);
                width: 100%;
                max-width: 480px;
                border-radius: 20px;
                padding: 32px 28px 26px;
                box-shadow: 0 25px 60px -12px rgba(15, 23, 42, 0.28), 
                            0 0 0 1px rgba(15, 23, 42, 0.08),
                            inset 0 1px 0 rgba(255, 255, 255, 0.9);
                text-align: center;
                transform: scale(0.93) translateY(14px);
                opacity: 0;
                transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.24s ease;
                position: relative;
                box-sizing: border-box;
                border-top: 4px solid var(--sv-accent-color, #1d4ed8);
                margin: auto;
                max-height: calc(100vh - 40px);
                overflow-y: auto;
            }
            .sv-modal-backdrop.sv-visible .sv-modal-card {
                transform: scale(1) translateY(0);
                opacity: 1;
            }

            /* Close Button */
            .sv-modal-close-btn {
                position: absolute;
                top: 14px;
                inset-inline-end: 14px;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                color: #64748b;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 15px;
                cursor: pointer;
                transition: all 0.2s ease;
                outline: none;
                z-index: 10;
            }
            .sv-modal-close-btn:hover {
                background: #fee2e2;
                color: #dc2626;
                border-color: #fca5a5;
                transform: rotate(90deg);
            }

            /* Classical Icons with Multi-Ring Emblems */
            .sv-modal-icon-wrap {
                width: 72px;
                height: 72px;
                border-radius: 50%;
                margin: 0 auto 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 32px;
                transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                position: relative;
            }
            .sv-modal-backdrop.sv-visible .sv-modal-icon-wrap {
                animation: sv-icon-pop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }
            @keyframes sv-icon-pop {
                0% { transform: scale(0.6); opacity: 0; }
                70% { transform: scale(1.1); }
                100% { transform: scale(1); opacity: 1; }
            }

            .sv-icon-success {
                background: #ecfdf5;
                color: #059669;
                border: 2px solid #a7f3d0;
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.12);
            }
            .sv-icon-error {
                background: #fef2f2;
                color: #dc2626;
                border: 2px solid #fecaca;
                box-shadow: 0 0 0 6px rgba(239, 68, 68, 0.12);
            }
            .sv-icon-warning {
                background: #fffbeb;
                color: #d97706;
                border: 2px solid #fde68a;
                box-shadow: 0 0 0 6px rgba(245, 158, 11, 0.12);
            }
            .sv-icon-info {
                background: #eff6ff;
                color: #1d4ed8;
                border: 2px solid #bfdbfe;
                box-shadow: 0 0 0 6px rgba(37, 99, 235, 0.12);
            }
            .sv-icon-question {
                background: #f5f3ff;
                color: #7c3aed;
                border: 2px solid #ddd6fe;
                box-shadow: 0 0 0 6px rgba(124, 58, 237, 0.12);
            }

            .sv-modal-title {
                font-size: 1.32rem;
                font-weight: 800;
                color: var(--sv-text-main);
                margin: 0 0 10px;
                line-height: 1.4;
                letter-spacing: -0.01em;
            }
            .sv-modal-text {
                font-size: 0.96rem;
                color: var(--sv-text-muted);
                line-height: 1.68;
                margin: 0 0 20px;
                word-break: break-word;
            }
            .sv-modal-text code {
                background: #f1f5f9;
                color: #0f172a;
                padding: 2px 6px;
                border-radius: 4px;
                font-family: monospace;
            }

            /* Classical Inputs */
            .sv-modal-input, .sv-modal-textarea, .sv-modal-select {
                width: 100%;
                padding: 12px 16px;
                border: 1.5px solid #cbd5e1;
                border-radius: 12px;
                font-family: inherit;
                font-size: 0.95rem;
                box-sizing: border-box;
                margin: 10px 0 16px;
                outline: none;
                transition: all 0.2s ease;
                background: #f8fafc;
                color: #0f172a;
                text-align: inherit;
            }
            .sv-modal-textarea {
                min-height: 95px;
                resize: vertical;
            }
            .sv-modal-input:focus, .sv-modal-textarea:focus, .sv-modal-select:focus {
                border-color: var(--sv-primary);
                background: #ffffff;
                box-shadow: 0 0 0 4px rgba(29, 78, 216, 0.12);
            }

            .sv-modal-validation {
                display: none;
                background: #fef2f2;
                color: #b91c1c;
                border: 1px solid #fecaca;
                border-radius: 10px;
                padding: 9px 15px;
                font-size: 0.88rem;
                font-weight: 700;
                margin-bottom: 16px;
                text-align: start;
                line-height: 1.5;
            }

            .sv-modal-footer {
                margin-top: 18px;
                padding-top: 14px;
                border-top: 1px solid #f1f5f9;
                font-size: 0.84rem;
                color: #64748b;
                line-height: 1.5;
            }

            .sv-modal-actions {
                display: flex;
                gap: 12px;
                justify-content: center;
                align-items: center;
                flex-wrap: wrap;
                margin-top: 16px;
            }

            /* Royal Classic Buttons */
            .sv-btn {
                padding: 12px 26px;
                border-radius: 12px;
                font-size: 0.95rem;
                font-weight: 800;
                cursor: pointer;
                border: none;
                outline: none;
                transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
                min-width: 110px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                font-family: inherit;
                text-decoration: none;
                user-select: none;
            }
            .sv-btn:hover:not(:disabled) {
                transform: translateY(-2px);
            }
            .sv-btn:active:not(:disabled) {
                transform: translateY(0) scale(0.98);
            }
            .sv-btn:disabled {
                opacity: 0.65;
                cursor: not-allowed;
                transform: none !important;
            }
            .sv-btn-confirm {
                background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%);
                color: #ffffff;
                border: 1px solid #1e3a8a;
                box-shadow: 0 4px 14px rgba(29, 78, 216, 0.28);
            }
            .sv-btn-confirm:hover:not(:disabled) {
                box-shadow: 0 6px 18px rgba(29, 78, 216, 0.38);
                filter: brightness(1.06);
            }
            .sv-btn-cancel {
                background: #f8fafc;
                color: #334155;
                border: 1.5px solid #cbd5e1;
            }
            .sv-btn-cancel:hover:not(:disabled) {
                background: #f1f5f9;
                border-color: #94a3b8;
                color: #0f172a;
            }
            .sv-btn-spinner {
                width: 16px;
                height: 16px;
                border: 2px solid rgba(255, 255, 255, 0.35);
                border-top-color: #ffffff;
                border-radius: 50%;
                animation: sv-spin 0.7s linear infinite;
                display: inline-block;
            }
            .sv-spinner {
                width: 48px;
                height: 48px;
                border: 4px solid #e2e8f0;
                border-top-color: #1d4ed8;
                border-radius: 50%;
                animation: sv-spin 0.8s linear infinite;
                margin: 16px auto;
            }
            @keyframes sv-spin {
                to { transform: rotate(360deg); }
            }

            /* Royal Toast Notification - Multi-position & Classical Elegance */
            .sv-toast-wrap {
                position: fixed;
                top: calc(20px + env(safe-area-inset-top, 0px));
                inset-inline-end: calc(20px + env(safe-area-inset-right, 0px));
                transform: translateY(-24px) scale(0.96);
                background: #ffffff;
                border-radius: 16px;
                padding: 12px 20px;
                display: flex;
                align-items: center;
                gap: 12px;
                box-shadow: 0 20px 45px -8px rgba(15, 23, 42, 0.24), 
                            0 0 0 1px rgba(15, 23, 42, 0.08),
                            inset 0 1px 0 rgba(255, 255, 255, 0.9);
                z-index: 1000000;
                opacity: 0;
                transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.24s ease;
                direction: rtl;
                font-family: var(--sv-font);
                max-width: min(440px, 92vw);
                box-sizing: border-box;
                border-inline-start: 4px solid var(--sv-toast-accent, #1d4ed8);
                overflow: hidden;
                cursor: pointer;
            }
            .sv-toast-wrap.sv-toast-visible {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
            .sv-toast-wrap.sv-pos-top-start {
                inset-inline-end: auto;
                inset-inline-start: calc(20px + env(safe-area-inset-left, 0px));
            }
            .sv-toast-wrap.sv-pos-top-center {
                inset-inline-end: auto;
                inset-inline-start: 50%;
                transform: translateX(50%) translateY(-24px) scale(0.96);
            }
            .sv-toast-wrap.sv-pos-top-center.sv-toast-visible {
                transform: translateX(50%) translateY(0) scale(1);
            }
            .sv-toast-wrap.sv-pos-bottom-end {
                top: auto;
                bottom: calc(20px + env(safe-area-inset-bottom, 0px));
                transform: translateY(24px) scale(0.96);
            }
            .sv-toast-wrap.sv-pos-bottom-end.sv-toast-visible {
                transform: translateY(0) scale(1);
            }
            .sv-toast-wrap.sv-pos-bottom-start {
                top: auto;
                bottom: calc(20px + env(safe-area-inset-bottom, 0px));
                inset-inline-end: auto;
                inset-inline-start: calc(20px + env(safe-area-inset-left, 0px));
                transform: translateY(24px) scale(0.96);
            }
            .sv-toast-wrap.sv-pos-bottom-start.sv-toast-visible {
                transform: translateY(0) scale(1);
            }
            .sv-toast-icon svg {
                width: 22px;
                height: 22px;
                flex-shrink: 0;
            }
            .sv-toast-title {
                font-size: 0.92rem;
                font-weight: 700;
                color: #0f172a;
                line-height: 1.45;
                flex: 1;
            }
            .sv-toast-progress {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: rgba(15, 23, 42, 0.08);
            }
            .sv-toast-progress-bar {
                height: 100%;
                background: var(--sv-toast-accent, #1d4ed8);
                width: 100%;
                transition: width linear;
            }

            /* Progress bar for timer */
            .sv-timer-progress {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: rgba(29, 78, 216, 0.15);
                border-bottom-left-radius: 20px;
                border-bottom-right-radius: 20px;
                overflow: hidden;
            }
            .sv-timer-progress-bar {
                height: 100%;
                background: var(--sv-accent-color, #1d4ed8);
                width: 100%;
                transition: width linear;
            }

            /* Responsive and Mobile App / Webview Friendly */
            @media (max-width: 640px) {
                .sv-modal-card {
                    padding: 24px 20px 20px;
                    border-radius: 18px;
                    max-width: 92vw;
                }
                .sv-modal-title {
                    font-size: 1.18rem;
                }
                .sv-modal-text {
                    font-size: 0.91rem;
                }
                .sv-btn {
                    flex: 1 1 auto;
                    min-width: 95px;
                    padding: 11px 18px;
                    font-size: 0.9rem;
                }
            }
        `;
        document.head.appendChild(style);
    }

    let activeModal = null;
    let activeToast = null;

    function getIconSvg(type) {
        switch (type) {
            case 'success':
                return '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
            case 'error':
                return '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
            case 'warning':
                return '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
            case 'info':
                return '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
            case 'question':
                return '<svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
            default:
                return '';
        }
    }

    function getAccentColor(icon) {
        switch (icon) {
            case 'success': return '#059669';
            case 'error': return '#dc2626';
            case 'warning': return '#d97706';
            case 'question': return '#7c3aed';
            case 'info':
            default: return '#1d4ed8';
        }
    }

    const NativeSwal = {
        fire: function(arg1, arg2, arg3) {
            let options = {};
            if (typeof arg1 === 'object' && arg1 !== null) {
                options = { ...arg1 };
            } else {
                options = {
                    title: arg1 || '',
                    text: arg2 || '',
                    icon: arg3 || null
                };
            }

            return new Promise((resolve) => {
                // If modal already open, close it instantly first
                if (activeModal && activeModal.parentNode) {
                    activeModal.parentNode.removeChild(activeModal);
                    activeModal = null;
                }
                if (activeToast && activeToast.parentNode) {
                    activeToast.parentNode.removeChild(activeToast);
                    activeToast = null;
                }

                // --- TOAST MODE ---
                if (options.toast) {
                    const toast = document.createElement('div');
                    let posClass = '';
                    const pos = (options.position || 'top-end').toLowerCase();
                    if (pos.includes('start') || pos.includes('left')) {
                        posClass = pos.includes('bottom') ? 'sv-pos-bottom-start' : 'sv-pos-top-start';
                    } else if (pos.includes('center')) {
                        posClass = 'sv-pos-top-center';
                    } else if (pos.includes('bottom')) {
                        posClass = 'sv-pos-bottom-end';
                    }
                    toast.className = `sv-toast-wrap ${posClass}`.trim();
                    const iconColor = getAccentColor(options.icon);
                    toast.style.setProperty('--sv-toast-accent', iconColor);
                    
                    const hasTimer = !!options.timer;
                    const timerMs = options.timer || 2500;
                    
                    toast.innerHTML = `
                        <div class="sv-toast-icon" style="color: ${iconColor}; display: flex; align-items: center; justify-content: center;">
                            ${getIconSvg(options.icon || 'info')}
                        </div>
                        <div class="sv-toast-title">${options.title || options.text || ''}</div>
                        ${hasTimer ? `<div class="sv-toast-progress"><div class="sv-toast-progress-bar" style="transition-duration: ${timerMs}ms;"></div></div>` : ''}
                    `;
                    
                    document.body.appendChild(toast);
                    activeToast = toast;
                    requestAnimationFrame(() => {
                        toast.classList.add('sv-toast-visible');
                        if (hasTimer) {
                            const bar = toast.querySelector('.sv-toast-progress-bar');
                            if (bar) requestAnimationFrame(() => bar.style.width = '0%');
                        }
                    });

                    let dismissed = false;
                    const dismissToast = () => {
                        if (dismissed) return;
                        dismissed = true;
                        toast.classList.remove('sv-toast-visible');
                        setTimeout(() => {
                            if (toast.parentNode) toast.parentNode.removeChild(toast);
                            if (activeToast === toast) activeToast = null;
                            resolve({ isConfirmed: true, isDismissed: false });
                        }, 250);
                    };

                    toast.onclick = dismissToast;
                    setTimeout(dismissToast, timerMs);
                    return;
                }

                // --- MODAL DIALOG MODE ---
                const backdrop = document.createElement('div');
                backdrop.className = 'sv-modal-backdrop';

                const card = document.createElement('div');
                card.className = 'sv-modal-card';
                const accentColor = getAccentColor(options.icon);
                card.style.setProperty('--sv-accent-color', accentColor);

                if (options.width) {
                    card.style.maxWidth = typeof options.width === 'number' ? options.width + 'px' : options.width;
                }

                // Close (X) button if allowed or requested
                if (options.showCloseButton !== false && options.allowOutsideClick !== false) {
                    const closeBtn = document.createElement('button');
                    closeBtn.type = 'button';
                    closeBtn.className = 'sv-modal-close-btn';
                    closeBtn.innerHTML = '✕';
                    closeBtn.title = 'إغلاق';
                    closeBtn.onclick = () => closeModal(false);
                    card.appendChild(closeBtn);
                }

                // Icon
                if (options.icon) {
                    const iconWrap = document.createElement('div');
                    iconWrap.className = 'sv-modal-icon-wrap sv-icon-' + options.icon;
                    iconWrap.innerHTML = getIconSvg(options.icon);
                    card.appendChild(iconWrap);
                }

                // Title
                if (options.title) {
                    const titleEl = document.createElement('h3');
                    titleEl.className = 'sv-modal-title';
                    titleEl.innerHTML = options.title;
                    card.appendChild(titleEl);
                }

                // Text / HTML
                if (options.html) {
                    const htmlEl = document.createElement('div');
                    htmlEl.className = 'sv-modal-text';
                    htmlEl.innerHTML = options.html;
                    card.appendChild(htmlEl);
                } else if (options.text) {
                    const textEl = document.createElement('div');
                    textEl.className = 'sv-modal-text';
                    textEl.innerText = options.text;
                    card.appendChild(textEl);
                }

                // Validation element
                const validationEl = document.createElement('div');
                validationEl.className = 'sv-modal-validation';
                card.appendChild(validationEl);

                // Input element if requested
                let inputEl = null;
                if (options.input) {
                    if (options.input === 'textarea') {
                        inputEl = document.createElement('textarea');
                        inputEl.className = 'sv-modal-textarea';
                    } else if (options.input === 'select') {
                        inputEl = document.createElement('select');
                        inputEl.className = 'sv-modal-select';
                        if (options.inputOptions && typeof options.inputOptions === 'object') {
                            Object.entries(options.inputOptions).forEach(([k, v]) => {
                                const opt = document.createElement('option');
                                opt.value = k;
                                opt.innerText = v;
                                inputEl.appendChild(opt);
                            });
                        }
                    } else {
                        inputEl = document.createElement('input');
                        inputEl.type = (options.input === 'password' || options.input === 'email' || options.input === 'number') ? options.input : 'text';
                        inputEl.className = 'sv-modal-input';
                    }
                    if (options.inputPlaceholder) inputEl.placeholder = options.inputPlaceholder;
                    if (options.inputValue) inputEl.value = options.inputValue;
                    card.appendChild(inputEl);
                }

                // Footer if requested
                if (options.footer) {
                    const footerEl = document.createElement('div');
                    footerEl.className = 'sv-modal-footer';
                    footerEl.innerHTML = options.footer;
                    card.appendChild(footerEl);
                }

                // Buttons container
                const actions = document.createElement('div');
                actions.className = 'sv-modal-actions';

                const showConfirm = options.showConfirmButton !== false;
                const showCancel = options.showCancelButton === true;

                let confirmBtn = null;
                let cancelBtn = null;

                if (showConfirm) {
                    confirmBtn = document.createElement('button');
                    confirmBtn.type = 'button';
                    confirmBtn.className = 'sv-btn sv-btn-confirm';
                    confirmBtn.innerText = options.confirmButtonText || 'موافق';
                    if (options.confirmButtonColor) {
                        confirmBtn.style.background = options.confirmButtonColor;
                        confirmBtn.style.borderColor = options.confirmButtonColor;
                    }
                    actions.appendChild(confirmBtn);
                }

                if (showCancel) {
                    cancelBtn = document.createElement('button');
                    cancelBtn.type = 'button';
                    cancelBtn.className = 'sv-btn sv-btn-cancel';
                    cancelBtn.innerText = options.cancelButtonText || 'إلغاء';
                    if (options.cancelButtonColor) {
                        cancelBtn.style.background = options.cancelButtonColor;
                        cancelBtn.style.color = '#ffffff';
                        cancelBtn.style.borderColor = options.cancelButtonColor;
                    }
                    actions.appendChild(cancelBtn);
                }

                if (showConfirm || showCancel) {
                    card.appendChild(actions);
                }

                // Timer progress bar if requested
                if (options.timer && options.timerProgressBar) {
                    const progressWrap = document.createElement('div');
                    progressWrap.className = 'sv-timer-progress';
                    const progressBar = document.createElement('div');
                    progressBar.className = 'sv-timer-progress-bar';
                    progressBar.style.transitionDuration = options.timer + 'ms';
                    progressWrap.appendChild(progressBar);
                    card.appendChild(progressWrap);
                    requestAnimationFrame(() => {
                        progressBar.style.width = '0%';
                    });
                }

                backdrop.appendChild(card);
                document.body.appendChild(backdrop);
                activeModal = backdrop;

                requestAnimationFrame(() => backdrop.classList.add('sv-visible'));

                const closeModal = (isConfirmed, resolvedValue) => {
                    backdrop.classList.remove('sv-visible');
                    setTimeout(() => {
                        if (backdrop.parentNode) backdrop.parentNode.removeChild(backdrop);
                        if (activeModal === backdrop) activeModal = null;
                        resolve({
                            isConfirmed: isConfirmed,
                            isDismissed: !isConfirmed,
                            value: isConfirmed ? (resolvedValue !== undefined ? resolvedValue : (inputEl ? inputEl.value : true)) : undefined
                        });
                    }, 240);
                };

                const performConfirm = async () => {
                    validationEl.style.display = 'none';
                    let val = inputEl ? inputEl.value : true;

                    if (typeof options.preConfirm === 'function') {
                        try {
                            const res = options.preConfirm(val);
                            if (res === false) return;

                            if (res && typeof res.then === 'function') {
                                if (options.showLoaderOnConfirm) {
                                    if (confirmBtn) {
                                        confirmBtn.disabled = true;
                                        confirmBtn.innerHTML = '<span class="sv-btn-spinner"></span> ' + (options.confirmButtonText || 'موافق');
                                    }
                                    if (cancelBtn) cancelBtn.disabled = true;
                                }
                                const resolved = await res;
                                closeModal(true, resolved !== undefined ? resolved : val);
                                return;
                            }
                            closeModal(true, res !== undefined ? res : val);
                        } catch (err) {
                            if (confirmBtn) {
                                confirmBtn.disabled = false;
                                confirmBtn.innerText = options.confirmButtonText || 'موافق';
                            }
                            if (cancelBtn) cancelBtn.disabled = false;
                            if (err) NativeSwal.showValidationMessage(err.message || String(err));
                        }
                    } else {
                        closeModal(true, val);
                    }
                };

                if (confirmBtn) {
                    confirmBtn.onclick = performConfirm;
                    if (!inputEl) confirmBtn.focus();
                }

                if (cancelBtn) {
                    cancelBtn.onclick = () => closeModal(false);
                }

                if (inputEl) {
                    setTimeout(() => inputEl.focus(), 60);
                    if (inputEl.tagName === 'INPUT') {
                        inputEl.addEventListener('keydown', (e) => {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                performConfirm();
                            }
                        });
                    }
                }

                if (options.allowOutsideClick !== false) {
                    backdrop.addEventListener('click', (e) => {
                        if (e.target === backdrop) closeModal(false);
                    });
                }

                const handleKey = (e) => {
                    if (e.key === 'Escape' && options.allowOutsideClick !== false) {
                        document.removeEventListener('keydown', handleKey);
                        closeModal(false);
                    }
                };
                document.addEventListener('keydown', handleKey);

                // Auto-timer
                if (options.timer) {
                    setTimeout(() => closeModal(true), options.timer);
                }

                // didOpen callback (used for showLoading)
                if (typeof options.didOpen === 'function') {
                    options.didOpen(card);
                }
            });
        },

        showLoading: function() {
            if (!activeModal) {
                this.fire({
                    title: 'جاري المعالجة...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => this.showLoading()
                });
                return;
            }
            const card = activeModal.querySelector('.sv-modal-card');
            if (card) {
                let actions = card.querySelector('.sv-modal-actions');
                if (actions) actions.style.display = 'none';

                let spinner = card.querySelector('.sv-spinner');
                if (!spinner) {
                    spinner = document.createElement('div');
                    spinner.className = 'sv-spinner';
                    card.appendChild(spinner);
                }
            }
        },

        hideLoading: function() {
            this.close();
        },

        isLoading: function() {
            if (!activeModal) return false;
            return !!activeModal.querySelector('.sv-spinner, .sv-btn-spinner');
        },

        showValidationMessage: function(msg) {
            if (activeModal) {
                const valEl = activeModal.querySelector('.sv-modal-validation');
                if (valEl) {
                    valEl.innerHTML = msg;
                    valEl.style.display = 'block';
                }
            }
        },

        resetValidationMessage: function() {
            if (activeModal) {
                const valEl = activeModal.querySelector('.sv-modal-validation');
                if (valEl) valEl.style.display = 'none';
            }
        },

        getInput: function() {
            return activeModal ? activeModal.querySelector('.sv-modal-input, .sv-modal-textarea, .sv-modal-select') : null;
        },

        getPopup: function() {
            return activeModal ? activeModal.querySelector('.sv-modal-card') : null;
        },

        getTitle: function() {
            return activeModal ? activeModal.querySelector('.sv-modal-title') : null;
        },

        getHtmlContainer: function() {
            return activeModal ? activeModal.querySelector('.sv-modal-text') : null;
        },

        clickConfirm: function() {
            if (activeModal) {
                const btn = activeModal.querySelector('.sv-btn-confirm');
                if (btn) btn.click();
            }
        },

        clickCancel: function() {
            if (activeModal) {
                const btn = activeModal.querySelector('.sv-btn-cancel');
                if (btn) btn.click();
            }
        },

        isVisible: function() {
            return !!activeModal;
        },

        close: function() {
            if (activeModal) {
                activeModal.classList.remove('sv-visible');
                setTimeout(() => {
                    if (activeModal && activeModal.parentNode) {
                        activeModal.parentNode.removeChild(activeModal);
                    }
                    activeModal = null;
                }, 200);
            }
            if (activeToast) {
                activeToast.classList.remove('sv-toast-visible');
                setTimeout(() => {
                    if (activeToast && activeToast.parentNode) {
                        activeToast.parentNode.removeChild(activeToast);
                    }
                    activeToast = null;
                }, 200);
            }
        }
    };

    window.Swal = NativeSwal;
    window.sweetAlert = NativeSwal.fire;
})();
