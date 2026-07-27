/**
 * kyc-personal.js
 * Handles the Personal KYC (CSB-IV) multi-step wizard:
 *   - Step navigation (next/prev)
 *   - Progress bar fill
 *   - Aadhaar & PAN verify buttons
 *   - AJAX form submission with FormData (so files upload correctly)
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('personalKycForm');
        if (!form) {
            return;
        }

        var panels = form.querySelectorAll('.wizard-panel');
        var barFill = document.getElementById('wizardBarFill');
        var totalPanels = panels.length;

        function updateProgressBar(currentStep) {
            if (!barFill) {
                return;
            }
            var pct = (currentStep / totalPanels) * 100;
            barFill.style.width = pct + '%';
        }

        function showPanel(step) {
            panels.forEach(function (panel) {
                if (parseInt(panel.getAttribute('data-panel'), 10) === step) {
                    panel.classList.add('active');
                } else {
                    panel.classList.remove('active');
                }
            });
            updateProgressBar(step);
            try {
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } catch (e) {
                form.scrollTop = 0;
            }
        }

        form.querySelectorAll('.wizard-next').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var next = parseInt(btn.getAttribute('data-next'), 10);
                if (next) {
                    showPanel(next);
                }
            });
        });

        form.querySelectorAll('.wizard-prev').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var prev = parseInt(btn.getAttribute('data-prev'), 10);
                if (prev) {
                    showPanel(prev);
                }
            });
        });

        updateProgressBar(1);

        function showAlert(message, type) {
            var existing = form.querySelector('.kyc-ajax-alert');
            if (existing) {
                existing.remove();
            }
            var alertDiv = document.createElement('div');
            alertDiv.className = 'alert kyc-ajax-alert alert-' + (type === 'error' ? 'danger' : 'success');
            alertDiv.style.marginBottom = '15px';
            alertDiv.textContent = message;
            form.insertBefore(alertDiv, form.firstChild);
            try {
                alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } catch (e) {}
            if (type === 'success') {
                setTimeout(function () {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 5000);
            }
        }

        function getCsrfToken() {
            var meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) {
                return meta.getAttribute('content');
            }
            var tokenInput = form.querySelector('input[name="_token"]');
            if (tokenInput) {
                return tokenInput.value;
            }
            return '';
        }

        var aadharVerifyBtn = document.getElementById('aadharVerifyBtn');
        var aadharVerifiedBadge = document.getElementById('aadharVerifiedBadge');
        if (aadharVerifyBtn) {
            aadharVerifyBtn.addEventListener('click', function () {
                var aadharInput = document.getElementById('aadharNumber');
                if (!aadharInput || !aadharInput.value) {
                    showAlert('Please enter your Aadhaar number first.', 'error');
                    return;
                }
                var aadhar = aadharInput.value.replace(/\s+/g, '');
                if (!/^[2-9][0-9]{11}$/.test(aadhar)) {
                    showAlert('Invalid Aadhaar number. It must be 12 digits and cannot start with 0 or 1.', 'error');
                    return;
                }
                aadharVerifyBtn.style.display = 'none';
                if (aadharVerifiedBadge) {
                    aadharVerifiedBadge.style.display = 'inline-block';
                }
                aadharInput.setAttribute('readonly', 'readonly');
            });
        }

        var panVerifyBtn = document.getElementById('panVerifyBtn');
        var panVerifiedBadge = document.getElementById('panVerifiedBadge');
        if (panVerifyBtn) {
            panVerifyBtn.addEventListener('click', function () {
                var panInput = document.getElementById('panNumber');
                if (!panInput || !panInput.value) {
                    showAlert('Please enter your PAN number first.', 'error');
                    return;
                }
                var pan = panInput.value.toUpperCase().replace(/\s+/g, '');
                if (!/^[A-Z]{5}[0-9]{4}[A-Z]$/.test(pan)) {
                    showAlert('Invalid PAN format. It must be 5 letters, 4 digits, and 1 letter (e.g. ABCDE1234F).', 'error');
                    return;
                }

                var verifyUrl = form.getAttribute('data-verify-pan-url');
                panVerifyBtn.disabled = true;
                panVerifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Verifying...';

                fetch(verifyUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ pan_number: pan })
                })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (data.success) {
                            panVerifyBtn.style.display = 'none';
                            if (panVerifiedBadge) {
                                panVerifiedBadge.style.display = 'inline-block';
                            }
                            panInput.setAttribute('readonly', 'readonly');
                            showAlert('PAN verified successfully!', 'success');
                        } else {
                            showAlert(data.message || 'PAN verification failed.', 'error');
                            panVerifyBtn.disabled = false;
                            panVerifyBtn.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Verify PAN';
                        }
                    })
                    .catch(function () {
                        panVerifyBtn.style.display = 'none';
                        if (panVerifiedBadge) {
                            panVerifiedBadge.style.display = 'inline-block';
                        }
                        panInput.setAttribute('readonly', 'readonly');
                        panVerifyBtn.disabled = false;
                        panVerifyBtn.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Verify PAN';
                    });
            });
        }

        var submitBtn = document.getElementById('submitKycBtn');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var terms = document.getElementById('termsAccepted');
            if (terms && !terms.checked) {
                showAlert('Please accept the terms and conditions to continue.', 'error');
                return;
            }

            var formData = new FormData(form);

            if (terms && terms.checked && !formData.has('terms_accepted')) {
                formData.append('terms_accepted', '1');
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Submitting...';
            }

            fetch(form.getAttribute('action'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        return { ok: response.ok, data: data };
                    });
                })
                .then(function (result) {
                    var data = result.data || {};
                    if (result.ok && data.success) {
                        showAlert(data.message || 'KYC submitted successfully!', 'success');
                        if (data.redirect) {
                            setTimeout(function () {
                                window.location.href = data.redirect;
                            }, 1200);
                        }
                    } else {
                        var msg = data.message || 'Submission failed. Please check your details.';
                        if (data.errors) {
                            var errorList = Object.values(data.errors).flat();
                            msg = errorList.join(' ');
                        }
                        showAlert(msg, 'error');
                        resetSubmitButton();
                    }
                })
                .catch(function () {
                    showAlert('A network error occurred. Please try again.', 'error');
                    resetSubmitButton();
                });

            function resetSubmitButton() {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'COMPLETE KYC <i class="fas fa-check-circle ms-2"></i>';
                }
            }
        });
    });
})();
