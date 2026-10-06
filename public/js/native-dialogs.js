/**
 * Step by Step - Native Vanilla Dialog & Toast Engine
 * 100% Zero-Dependency, Ultra-Fast Drop-in Replacement for Heavy External Libraries (SweetAlert2)
 * Designed for maximum performance, rich aesthetics, accessibility, and instant 0ms response.
 */
(function() {
    'use strict';

    // Inject styles once
    const styleId = 'stepvoro-native-dialogs-css';
    if (!document.getElementById(styleId)) {
        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = `
            .sv-modal-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.65);
                backdrop-filter: blur(6px);
                -webkit-backdrop-filter: blur(6px);
                z-index: 999999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 16px;
                opacity: 0;
                transition: opacity 0.22s ease;
                font-family: 'Tajawal', 'Alexandria', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                direction: rtl;
                box-sizing: border-box;
            }
            .sv-modal-backdrop.sv-visible {
                opacity: 1;
            }
            .sv-modal-card {
                background: #ffffff;
                width: 100%;
                max-width: 440px;
                border-radius: 18px;
                padding: 26px 24px;
                box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(15, 23, 42, 0.06);
                text-align: center;
                transform: scale(0.92) translateY(12px);
                opacity: 0;
                transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.22s ease;
                position: relative;
                box-sizing: border-box;
            }
            .sv-modal-backdrop.sv-visible .sv-modal-card {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
            .sv-modal-icon-wrap {
                width: 64px;
                height: 64px;
                border-radius: 50%;
                margin: 0 auto 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 28px;
            }
            .sv-icon-success {
                background: #ecfdf5;
                color: #059669;
                border: 2px solid #a7f3d0;
            }
            .sv-icon-error {
                background: #fef2f2;
                color: #dc2626;
                border: 2px solid #fecaca;
            }
            .sv-icon-warning {
                background: #fffbeb;
                color: #d97706;
                border: 2px solid #fde68a;
            }
            .sv-icon-info {
                background: #eff6ff;
                color: #2563eb;
                border: 2px solid #bfdbfe;
            }
            .sv-icon-question {
                background: #f5f3ff;
                color: #7c3aed;
                border: 2px solid #ddd6fe;
            }
            .sv-modal-title {
                font-size: 1.25rem;
                font-weight: 800;
                color: #0f172a;
                margin: 0 0 10px;
                line-height: 1.4;
            }
            .sv-modal-text {
                font-size: 0.95rem;
                color: #475569;
                line-height: 1.6;
                margin: 0 0 18px;
                word-break: break-word;
            }
            .sv-modal-input, .sv-modal-textarea {
                width: 100%;
                padding: 10px 14px;
                border: 1.5px solid #cbd5e1;
                border-radius: 12px;
                font-family: inherit;
                font-size: 0.95rem;
                box-sizing: border-box;
                margin: 10px 0 14px;
                outline: none;
                transition: border-color 0.2s, box-shadow 0.2s;
                background: #f8fafc;
                color: #0f172a;
                text-align: inherit;
            }
            .sv-modal-textarea {
                min-height: 90px;
                resize: vertical;
            }
            .sv-modal-input:focus, .sv-modal-textarea:focus {
                border-color: #2563eb;
                background: #ffffff;
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            }
            .sv-modal-validation {
                display: none;
                background: #fef2f2;
                color: #b91c1c;
                border: 1px solid #fecaca;
                border-radius: 10px;
                padding: 8px 14px;
                font-size: 0.85rem;
                font-weight: 600;
                margin-bottom: 14px;
                text-align: start;
                line-height: 1.5;
            }
            .sv-modal-actions {
                display: flex;
                gap: 10px;
                justify-content: center;
                align-items: center;
                flex-wrap: wrap;
                margin-top: 14px;
            }
            .sv-btn {
                padding: 10px 22px;
                border-radius: 10px;
                font-size: 0.92rem;
                font-weight: 700;
                cursor: pointer;
                border: none;
                outline: none;
                transition: transform 0.15s ease, filter 0.15s ease, background 0.15s ease;
                min-width: 100px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                font-family: inherit;
            }
            .sv-btn:active {
                transform: scale(0.97);
            }
            .sv-btn:disabled {
                opacity: 0.65;
                cursor: not-allowed;
                transform: none !important;
            }
            .sv-btn-confirm {
                background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
                color: #ffffff;
                box-shadow: 0 4px 12px rgba(29, 78, 216, 0.25);
            }
            .sv-btn-confirm:hover:not(:disabled) {
                filter: brightness(1.08);
            }
            .sv-btn-cancel {
                background: #f1f5f9;
                color: #475569;
                border: 1px solid #cbd5e1;
            }
            .sv-btn-cancel:hover:not(:disabled) {
                background: #e2e8f0;
                color: #1e293b;
            }
            .sv-btn-spinner {
                width: 15px;
                height: 15px;
                border: 2px solid rgba(255, 255, 255, 0.35);
                border-top-color: #ffffff;
                border-radius: 50%;
                animation: sv-spin 0.7s linear infinite;
                display: inline-block;
            }
            .sv-spinner {
                width: 44px;
                height: 44px;
                border: 4px solid #e2e8f0;
                border-top-color: #1d4ed8;
                border-radius: 50%;
                animation: sv-spin 0.8s linear infinite;
                margin: 10px auto;
            }
            @keyframes sv-spin {
                to { transform: rotate(360deg); }
            }
            /* Toast Notification */
            .sv-toast-wrap {
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%) translateY(-30px);
                background: #ffffff;
                border-radius: 50px;
                padding: 12px 24px;
                display: flex;
                align-items: center;
                gap: 12px;
                box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.18), 0 0 0 1px rgba(15, 23, 42, 0.08);
                z-index: 1000000;
                opacity: 0;
                transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.22s ease;
                direction: rtl;
                font-family: 'Tajawal', sans-serif;
                max-width: 90vw;
            }
            .sv-toast-wrap.sv-toast-visible {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
            .sv-toast-title {
                font-size: 0.92rem;
                font-weight: 700;
                color: #0f172a;
            }
            @media (max-width: 640px) {
                .sv-modal-card {
                    padding: 22px 18px;
                    border-radius: 16px;
                }
                .sv-btn {
                    flex: 1 1 auto;
                    min-width: 80px;
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
                return '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
            case 'error':
                return '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
            case 'warning':
                return '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
            case 'info':
                return '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
            case 'question':
                return '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
            default:
                return '';
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
                    toast.className = 'sv-toast-wrap';
                    const iconColor = options.icon === 'success' ? '#059669' :
                                      options.icon === 'error' ? '#dc2626' :
                                      options.icon === 'warning' ? '#d97706' : '#2563eb';
                    toast.innerHTML = `
                        <div style="color: ${iconColor}; display: flex; align-items: center; justify-content: center;">
                            ${getIconSvg(options.icon || 'info')}
                        </div>
                        <div class="sv-toast-title">${options.title || options.text || ''}</div>
                    `;
                    document.body.appendChild(toast);
                    activeToast = toast;
                    requestAnimationFrame(() => toast.classList.add('sv-toast-visible'));

                    const timer = options.timer || 2000;
                    setTimeout(() => {
                        toast.classList.remove('sv-toast-visible');
                        setTimeout(() => {
                            if (toast.parentNode) toast.parentNode.removeChild(toast);
                            if (activeToast === toast) activeToast = null;
                            resolve({ isConfirmed: true, isDismissed: false });
                        }, 250);
                    }, timer);
                    return;
                }

                // --- MODAL DIALOG MODE ---
                const backdrop = document.createElement('div');
                backdrop.className = 'sv-modal-backdrop';

                const card = document.createElement('div');
                card.className = 'sv-modal-card';

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
                    } else {
                        inputEl = document.createElement('input');
                        inputEl.type = (options.input === 'password' || options.input === 'email' || options.input === 'number') ? options.input : 'text';
                        inputEl.className = 'sv-modal-input';
                    }
                    if (options.inputPlaceholder) inputEl.placeholder = options.inputPlaceholder;
                    if (options.inputValue) inputEl.value = options.inputValue;
                    card.appendChild(inputEl);
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
                    }
                    actions.appendChild(cancelBtn);
                }

                if (showConfirm || showCancel) {
                    card.appendChild(actions);
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
                    setTimeout(() => inputEl.focus(), 50);
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
            return activeModal ? activeModal.querySelector('.sv-modal-input, .sv-modal-textarea') : null;
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
