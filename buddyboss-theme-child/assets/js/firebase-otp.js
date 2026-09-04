/**
 * Firebase OTP Phone Login — Sampreshan.tech
 * Handles phone number verification via Firebase Auth
 *
 * @package SampreShan_Child
 */
(function () {
    'use strict';

    // Firebase config
    const firebaseConfig = {
        apiKey: "AIzaSyBd_Hl4uaivSnm3Ue0N_xcKexKX4PIIUpI",
        authDomain: "sampreshan-tech.firebaseapp.com",
        projectId: "sampreshan-tech",
        storageBucket: "sampreshan-tech.firebasestorage.app",
        messagingSenderId: "780707453290",
        appId: "1:780707453290:web:464c33fa9720bbe1e6ed13",
        measurementId: "G-TXRBLN47KQ"
    };

    let app = null;
    let auth = null;
    let confirmationResult = null;
    let recaptchaVerifier = null;

    // Initialize Firebase dynamically
    function initFirebase() {
        return new Promise((resolve, reject) => {
            if (app) { resolve(app); return; }

            // Load Firebase SDK
            const scripts = [
                'https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js',
                'https://www.gstatic.com/firebasejs/10.12.0/firebase-auth-compat.js'
            ];

            let loaded = 0;
            scripts.forEach(src => {
                const script = document.createElement('script');
                script.src = src;
                script.onload = () => {
                    loaded++;
                    if (loaded === scripts.length) {
                        try {
                            app = firebase.initializeApp(firebaseConfig);
                            auth = firebase.auth();
                            auth.useDeviceLanguage();
                            resolve(app);
                        } catch (e) {
                            reject(e);
                        }
                    }
                };
                script.onerror = () => reject(new Error('Failed to load Firebase SDK'));
                document.head.appendChild(script);
            });
        });
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

        sendBtn.disabled = true;
        sendBtn.textContent = 'Sending...';

        try {
            await initFirebase();

            // Setup reCAPTCHA verifier
            if (!recaptchaVerifier) {
                const recaptchaContainer = document.createElement('div');
                recaptchaContainer.id = 'sp-recaptcha-container';
                recaptchaContainer.style.cssText = 'position:fixed;top:-9999px;left:-9999px;';
                document.body.appendChild(recaptchaContainer);

                recaptchaVerifier = new firebase.auth.RecaptchaVerifier('sp-recaptcha-container', {
                    size: 'invisible',
                    callback: () => {},
                    'expired-callback': () => {
                        recaptchaVerifier = null;
                    }
                });
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
            sendBtn.textContent = 'Send OTP';
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

        verifyBtn.disabled = true;
        verifyBtn.textContent = 'Verifying...';

        try {
            const result = await confirmationResult.confirm(otp);
            const user = result.user;
            const idToken = await user.getIdToken();

            // Send to WordPress backend
            const wpAjax = window.SampreshanAuth || {};
            const ajaxUrl = wpAjax.ajaxUrl || '/wp-admin/admin-ajax.php';
            const nonce = wpAjax.phoneNonce || '';

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
            verifyBtn.textContent = 'Verify & Login';
        }
    }

    // Handle Resend OTP
    async function handleResendOTP(e) {
        e.preventDefault();
        const phoneSection = e.target.closest('.sp-otp-section')?.previousElementSibling ||
                            document.querySelector('.sp-phone-section');
        const otpSection = e.target.closest('.sp-otp-section');

        if (otpSection) otpSection.style.display = 'none';
        if (phoneSection) phoneSection.style.display = 'block';

        // Reset reCAPTCHA
        recaptchaVerifier = null;
    }

    // Bind events on DOM ready
    function init() {
        // Phone login forms
        document.querySelectorAll('.sp-firebase-phone-form').forEach(form => {
            const phoneSection = form.querySelector('.sp-phone-section');
            const otpSection = form.querySelector('.sp-otp-section');

            if (otpSection) otpSection.style.display = 'none';

            form.addEventListener('submit', (e) => {
                if (e.target.querySelector('.sp-otp-section')?.style.display === 'block') {
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
