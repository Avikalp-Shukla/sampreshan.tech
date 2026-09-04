/**
 * petition-form.js — Multi-step petition creation form
 * Handles step navigation, live preview, cover upload, and AJAX submission.
 *
 * @package SampreShan_Child
 */
(function () {
    'use strict';

    if (typeof SampreshanPetitionForm === 'undefined') { return; }

    var form        = document.getElementById('sp-petition-form');
    if (!form) { return; }

    var steps       = form.querySelectorAll('.sp-form-step');
    var stepBtns    = form.querySelectorAll('.sp-step');
    var connectors  = form.querySelectorAll('.sp-step__connector');
    var nextBtns    = form.querySelectorAll('.sp-next-step');
    var prevBtns    = form.querySelectorAll('.sp-prev-step');
    var submitBtn   = document.getElementById('sp-submit-petition');
    var currentStep = 1;
    var totalSteps  = 4;

    /* ── Step navigation ── */
    function goToStep(n) {
        if (n < 1 || n > totalSteps) { return; }

        // Validate current step before advancing
        if (n > currentStep && !validateStep(currentStep)) { return; }

        currentStep = n;

        steps.forEach(function (s) {
            s.classList.toggle('is-active', parseInt(s.dataset.step) === n);
        });

        stepBtns.forEach(function (btn) {
            var sn = parseInt(btn.dataset.step);
            btn.classList.toggle('is-active', sn === n);
            btn.classList.toggle('is-complete', sn < n);
        });

        connectors.forEach(function (c, i) {
            c.classList.toggle('is-complete', i < n - 1);
        });

        // Update hidden field
        var stepInput = document.getElementById('sp-current-step');
        if (stepInput) { stepInput.value = n; }

        // Generate preview on step 4
        if (n === 4) { generatePreview(); }

        // Scroll to top of form
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    nextBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            goToStep(parseInt(btn.dataset.next));
        });
    });

    prevBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            goToStep(parseInt(btn.dataset.prev));
        });
    });

    stepBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var target = parseInt(btn.dataset.step);
            if (target <= currentStep) { goToStep(target); }
        });
    });

    /* ── Validation ── */
    function validateStep(n) {
        var step = form.querySelector('.sp-form-step[data-step="' + n + '"]');
        if (!step) { return true; }

        var required = step.querySelectorAll('[required]');
        var valid = true;

        required.forEach(function (field) {
            if (!field.value.trim()) {
                field.classList.add('is-error');
                valid = false;
                // Shake animation
                field.style.animation = 'none';
                field.offsetHeight; // reflow
                field.style.animation = 'shake 0.4s ease';
            } else {
                field.classList.remove('is-error');
            }
        });

        if (!valid) {
            var firstError = step.querySelector('.is-error');
            if (firstError) { firstError.focus(); }
        }

        return valid;
    }

    /* ── Character counters ── */
    var titleInput = document.getElementById('petition-title');
    var titleCount = document.getElementById('petition-title-count');
    var summaryInput = document.getElementById('petition-summary');
    var summaryCount = document.getElementById('petition-summary-count');

    if (titleInput && titleCount) {
        titleInput.addEventListener('input', function () {
            titleCount.textContent = titleInput.value.length + '/120';
        });
    }

    if (summaryInput && summaryCount) {
        summaryInput.addEventListener('input', function () {
            summaryCount.textContent = summaryInput.value.length + '/300';
        });
    }

    /* ── Cover image upload ── */
    var coverInput   = document.getElementById('petition-cover-input');
    var coverPreview = document.getElementById('sp-cover-preview');
    var coverImg     = document.getElementById('sp-cover-img');
    var coverPH      = document.getElementById('sp-cover-placeholder');
    var coverRemove  = document.getElementById('sp-cover-remove');
    var uploadArea   = document.getElementById('sp-cover-upload');

    if (coverInput) {
        coverInput.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (!file) { return; }
            if (file.size > 5 * 1024 * 1024) {
                alert('File too large. Max 5MB.');
                return;
            }
            var reader = new FileReader();
            reader.onload = function (ev) {
                coverImg.src = ev.target.result;
                coverPreview.style.display = 'block';
                coverPH.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    }

    if (coverRemove) {
        coverRemove.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            coverInput.value = '';
            coverPreview.style.display = 'none';
            coverPH.style.display = '';
        });
    }

    // Drag & drop
    if (uploadArea) {
        ['dragenter', 'dragover'].forEach(function (evt) {
            uploadArea.addEventListener(evt, function (e) {
                e.preventDefault();
                uploadArea.classList.add('is-dragover');
            });
        });
        ['dragleave', 'drop'].forEach(function (evt) {
            uploadArea.addEventListener(evt, function (e) {
                e.preventDefault();
                uploadArea.classList.remove('is-dragover');
            });
        });
        uploadArea.addEventListener('drop', function (e) {
            var files = e.dataTransfer.files;
            if (files.length > 0) {
                coverInput.files = files;
                coverInput.dispatchEvent(new Event('change'));
            }
        });
    }

    /* ── Live preview (step 4) ── */
    function generatePreview() {
        var titleEl    = document.getElementById('sp-preview-title');
        var summaryEl  = document.getElementById('sp-preview-summary');
        var causeEl    = document.getElementById('sp-preview-cause');
        var goalEl     = document.getElementById('sp-preview-goal');
        var coverEl    = document.getElementById('sp-preview-cover');

        var titleInput    = document.getElementById('petition-title');
        var summaryInput  = document.getElementById('petition-summary');
        var causeSelect   = document.getElementById('petition-cause');
        var goalInput     = document.getElementById('petition-goal');

        if (titleEl && titleInput) {
            titleEl.textContent = titleInput.value || 'Your petition title';
        }
        if (summaryEl && summaryInput) {
            summaryEl.textContent = summaryInput.value || 'Your petition summary will appear here...';
        }
        if (causeEl && causeSelect) {
            var selectedOpt = causeSelect.options[causeSelect.selectedIndex];
            causeEl.textContent = selectedOpt && selectedOpt.value ? selectedOpt.text : '';
            causeEl.style.display = (selectedOpt && selectedOpt.value) ? '' : 'none';
        }
        if (goalEl && goalInput) {
            goalEl.textContent = 'Goal: ' + (goalInput.value || '1,000') + ' signatures';
        }
        if (coverEl && coverImg && coverImg.src) {
            coverEl.innerHTML = '<img src="' + coverImg.src + '" alt="Cover">';
        }
    }

    /* ── AJAX submission ── */
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!validateStep(currentStep)) { return; }

            var submitText   = submitBtn.querySelector('.sp-submit-petition__text');
            var submitLoader = submitBtn.querySelector('.sp-submit-petition__loading');

            submitBtn.disabled = true;
            submitText.style.display = 'none';
            submitLoader.style.display = '';

            var formData = new FormData(form);

            // Handle cover image
            var coverFile = coverInput && coverInput.files[0];
            if (coverFile) {
                formData.append('petition_cover', coverFile);
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', SampreshanPetitionForm.ajaxUrl, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState !== 4) { return; }
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.success) {
                        // Show success
                        steps.forEach(function (s) { s.classList.remove('is-active'); });
                        var successStep = form.querySelector('.sp-form-step[data-step="success"]');
                        if (successStep) {
                            successStep.style.display = 'block';
                            successStep.classList.add('is-active');
                        }
                        // Update success links
                        var viewLink = document.getElementById('sp-success-view');
                        if (viewLink && resp.data && resp.data.permalink) {
                            viewLink.href = resp.data.permalink;
                        }
                        // Update step indicators
                        stepBtns.forEach(function (btn) { btn.classList.add('is-complete'); btn.classList.remove('is-active'); });
                    } else {
                        alert(resp.data && resp.data.message ? resp.data.message : 'Something went wrong. Please try again.');
                        submitBtn.disabled = false;
                        submitText.style.display = '';
                        submitLoader.style.display = 'none';
                    }
                } catch (err) {
                    alert('Error parsing response. Please try again.');
                    submitBtn.disabled = false;
                    submitText.style.display = '';
                    submitLoader.style.display = 'none';
                }
            };
            xhr.onerror = function () {
                alert('Network error. Please check your connection.');
                submitBtn.disabled = false;
                submitText.style.display = '';
                submitLoader.style.display = 'none';
            };
            xhr.send(formData);
        });
    }
})();
