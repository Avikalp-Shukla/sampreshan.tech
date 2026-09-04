/**
 * Firebase OTP Phone Login — Sampreshan.tech
 * Handles phone number verification via Firebase Auth
 *
 * @package SampreShan_Child
 */
(function () {
    'use strict';

    // Firebase config — canonical copy lives in PHP (sp_firebase_config(),
    // same snippet pasted into Digits → Settings → Firebase). The localized
    // `SampreshanFirebase.config` wins; hardcoded values are fallback only.
    const fallbackConfig = {
        apiKey: "AIzaSyBd_Hl4uaivSnm3Ue0N_xcKexKX4PIIUpI",
        authDomain: "sampreshan-tech.firebaseapp.com",
        projectId: "sampreshan-tech",
        storageBucket: "sampreshan-tech.firebasestorage.app",
        messagingSenderId: "780707453290",
        appId: "1:780707453290:web:464c33fa9720bbe1e6ed13",
        measurementId: "G-TXRBLN47KQ"
    };
    const localized = (typeof window.SampreshanFirebase !== 'undefined') ? window.SampreshanFirebase : {};
    const firebaseConfig = (localized.config && localized.config.apiKey) ? localized.config : fallbackConfig;

    function getAjax() {
        const fromFirebase = (typeof window.SampreshanFirebase !== 'undefined') ? window.SampreshanFirebase : {};
        const fromAuth = (typeof window.SampreshanAuth !== 'undefined') ? window.SampreshanAuth : {};
        return {
            ajaxUrl: fromFirebase.ajaxUrl || fromAuth.ajaxUrl || '/wp-admin/admin-ajax.php',
            nonce: fromFirebase.phoneNonce || fromAuth.phoneNonce || ''
        };
    }

    let app = null;
    let auth = null;
    let confirmationResult = null;
    let recaptchaVerifier = null;

    // Initialize Firebase dynamically (compat SDK; single-flight + reuse
    // an already-bootstrapped firebase app, e.g. loaded by Digits).
    let firebaseInitPromise = null;
    function initFirebase() {
        if (app) { return Promise.resolve(app); }
        if (firebaseInitPromise) { return firebaseInitPromise; }
        firebaseInitPromise = new Promise((resolve, reject) => {
            if (typeof window.firebase !== 'undefined' && window.firebase.apps && window.firebase.apps.length) {
                try {
                    app = window.firebase.app();
                    auth = window.firebase.auth();
                    auth.useDeviceLanguage();
                    resolve(app);
                    return;
                } catch (e) { /* fall through to fresh init */ }
            }

            // Load Firebase SDK
            const scripts = [
                'https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js',
                'https://www.gstatic.com/firebasejs/10.12.0/firebase-auth-compat.js'
            ];

            let loaded = 0;
            scripts.forEach(src => {
                if (document.querySelector('script[src="' + src + '"]')) {
                    loaded++;
                    if (loaded === scripts.length) { finish(); }
                    return;
                }
                const script = document.createElement('script');
                script.src = src;
                script.onload = () => {
                    loaded++;
                    if (loaded === scripts.length) { finish(); }
                };
                script.onerror = () => {
                    firebaseInitPromise = null;
                    reject(new Error('Failed to load Firebase SDK'));
                };
                document.head.appendChild(script);
            });

            function finish() {
                try {
                    app = window.firebase.apps && window.firebase.apps.length
                        ? window.firebase.app()
                        : window.firebase.initializeApp(firebaseConfig);
                    auth = window.firebase.auth();
                    auth.useDeviceLanguage();
                    resolve(app);
                } catch (e) {
                    firebaseInitPromise = null;
                    reject(e);
                }
            }
        });
        return firebaseInitPromise;
    }

    // Format phone number for display
    function formatPhone(phone) {
        if (!phone) return '';
        const cleaned = phone.replace(/\D/g, '');
        if (cleaned.length === 10) {
            return `+91 ${cleaned.slice(0, 5)} ${cleaned.slice(5)}`;
        }
        return phone;
    }

    // Show notice message
    function showNotice(container, type, message) {
        const existing = container.querySelector('.sp-notice');
        if (existing) existing.remove();

        const notice = document.createElement('div');
        notice.className = `sp-notice sp-notice--${type}`;
        notice.textContent = message;
        container.prepend(notice);

        setTimeout(() => {
            notice.style.opacity = '0';
            notice.style.transform = 'translateY(-8px)';
            setTimeout(() => notice.remove(), 300);
        }, 5000);
    }

    // Handle Send OTP
    async function handleSendOTP(e) {
        e.preventDefault();
        const form = e.target;
        const phoneInput = form.querySelector('[name="phone"]');
        const sendBtn = form.querySelector('.sp-otp-send-btn');
        const otpSection = form.querySelector('.sp-otp-section');
        const phoneSection = form.querySelector('.sp-phone-section');
        const noticeContainer = form.querySelector('.sp-notice-container') || form;

        let phone = phoneInput.value.trim();

        // Validate Indian phone number
        const cleaned = phone.replace(/\D/g, '');
        if (cleaned.length === 10) {
            phone = '+91' + cleaned;
        } else if (cleaned.length === 12 && cleaned.startsWith('91')) {
            phone = '+' + cleaned;
        } else if (!phone.startsWith('+')) {
            showNotice(noticeContainer, 'error', 'Please enter a valid 10-digit Indian mobile number.');
            return;
        }

        const sendBtnHtml = sendBtn.innerHTML;
        sendBtn.disabled = true;
        sendBtn.textContent = 'Sending...';

        try {
            await initFirebase();

            // Setup reCAPTCHA verifier (reuse the hidden container).
            if (!recaptchaVerifier) {
                let recaptchaContainer = document.getElementById('sp-recaptcha-container');
                if (!recaptchaContainer) {
                    recaptchaContainer = document.createElement('div');
                    recaptchaContainer.id = 'sp-recaptcha-container';
                    recaptchaContainer.style.cssText = 'position:fixed;top:-9999px;left:-9999px;';
                    document.body.appendChild(recaptchaContainer);
                }

                recaptchaVerifier = new window.firebase.auth.RecaptchaVerifier('sp-recaptcha-container', {
                    size: 'invisible',
                    callback: () => {},
                    'expired-callback': () => {
                        recaptchaVerifier = null;
                    }
                });
            }

            if (typeof window.firebase === 'undefined' || !window.firebase.auth) {
                throw new Error('Failed to load Firebase SDK');
            }
            confirmationResult = await auth.signInWithPhoneNumber(phone, recaptchaVerifier);

            // Show OTP section
            if (phoneSection) phoneSection.style.display = 'none';
            if (otpSection) {
                otpSection.style.display = 'block';
                otpSection.querySelector('.sp-otp-phone-display').textContent = formatPhone(phone);
                otpSection.querySelector('[name="otp"]').focus();
            }

            showNotice(noticeContainer, 'success', 'OTP sent successfully! Check your phone.');

        } catch (error) {
            console.error('Firebase OTP error:', error);
            let msg = 'Failed to send OTP. Please try again.';
            if (error.code === 'auth/too-many-requests') {
                msg = 'Too many attempts. Please wait a few minutes.';
            } else if (error.code === 'auth/invalid-phone-number') {
                msg = 'Invalid phone number. Please check and try again.';
            }
            showNotice(noticeContainer, 'error', msg);
            recaptchaVerifier = null;
        } finally {
            sendBtn.disabled = false;
            sendBtn.innerHTML = sendBtnHtml;
        }
    }

    // Handle Verify OTP
    async function handleVerifyOTP(e) {
        e.preventDefault();
        const form = e.target;
        const otpInput = form.querySelector('[name="otp"]');
        const verifyBtn = form.querySelector('.sp-otp-verify-btn');
        const noticeContainer = form.querySelector('.sp-notice-container') || form;

        const otp = otpInput.value.trim();
        if (otp.length !== 6) {
            showNotice(noticeContainer, 'error', 'Please enter the 6-digit OTP.');
            return;
        }
        if (!confirmationResult) {
            showNotice(noticeContainer, 'error', 'Session expired. Please tap Resend OTP and try again.');
            return;
        }

        const verifyBtnHtml = verifyBtn.innerHTML;
        verifyBtn.disabled = true;
        verifyBtn.textContent = 'Verifying...';

        try {
            const result = await confirmationResult.confirm(otp);
            const user = result.user;
            const idToken = await user.getIdToken();

            // Send to WordPress backend
            const wpAjax = getAjax();
            const ajaxUrl = wpAjax.ajaxUrl;
            const nonce = wpAjax.nonce;

            const response = await fetch(ajaxUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'sp_firebase_login',
                    idToken: idToken,
                    phone: user.phoneNumber,
                    nonce: nonce
                })
            });

            const data = await response.json();

            if (data.success) {
                showNotice(noticeContainer, 'success', 'Login successful! Redirecting...');
                setTimeout(() => {
                    window.location.href = data.data?.redirect || '/';
                }, 1000);
            } else {
                showNotice(noticeContainer, 'error', data.data?.message || 'Login failed. Please try again.');
            }

        } catch (error) {
            console.error('OTP verify error:', error);
            let msg = 'Invalid OTP. Please try again.';
            if (error.code === 'auth/code-expired') {
                msg = 'OTP expired. Please request a new one.';
            }
            showNotice(noticeContainer, 'error', msg);
        } finally {
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = verifyBtnHtml;
        }
    }

    // Handle Resend OTP
    async function handleResendOTP(e) {
        e.preventDefault();
        const form = e.target.closest('form');
        const otpSection = form ? form.querySelector('.sp-otp-section') : e.target.closest('.sp-otp-section');
        const phoneSection = form ? form.querySelector('.sp-phone-section') : null;

        if (otpSection) otpSection.style.display = 'none';
        if (phoneSection) phoneSection.style.display = 'block';

        // Reset state so the next submit re-sends the OTP.
        confirmationResult = null;
        recaptchaVerifier = null;
    }

    function isOtpVisible(form) {
        const otpSection = form.querySelector('.sp-otp-section');
        return !!(otpSection && otpSection.style.display === 'block');
    }

    // Bind events on DOM ready
    function init() {
        // Phone login forms
        document.querySelectorAll('.sp-firebase-phone-form').forEach(form => {
            const otpSection = form.querySelector('.sp-otp-section');

            if (otpSection && !confirmationResult) otpSection.style.display = 'none';

            form.addEventListener('submit', (e) => {
                if (isOtpVisible(e.target) && confirmationResult) {
                    handleVerifyOTP(e);
                } else {
                    handleSendOTP(e);
                }
            });

            // Resend button
            const resendBtn = form.querySelector('.sp-otp-resend');
            if (resendBtn) {
                resendBtn.addEventListener('click', handleResendOTP);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
