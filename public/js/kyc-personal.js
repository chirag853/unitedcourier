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

        var imageOnlyDocumentNames = ['aadhar_front_document', 'aadhar_back_document', 'pan_document'];

        function validateImageDocument(input, required) {
            var file = input && input.files ? input.files[0] : null;
            if (!file) {
                if (required) {
                    showAlert('Please upload all required Aadhaar and PAN images.', 'error');
                    if (input) input.focus();
                    return false;
                }
                return true;
            }

            var extension = file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : '';
            if (['jpg', 'jpeg', 'png'].indexOf(extension) === -1 ||
                ['image/jpeg', 'image/png'].indexOf(file.type) === -1) {
                showAlert('Aadhaar and PAN documents must be JPG, JPEG, or PNG images.', 'error');
                input.value = '';
                return false;
            }
            if (file.size > 5 * 1024 * 1024) {
                showAlert('Aadhaar and PAN images must not exceed 5 MB.', 'error');
                input.value = '';
                return false;
            }
            return true;
        }

        imageOnlyDocumentNames.forEach(function (name) {
            var input = form.querySelector('[name="' + name + '"]');
            if (input) {
                input.addEventListener('change', function () {
                    validateImageDocument(input, false);
                });
            }
        });

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
        var panVerified = false;
        if (panVerifyBtn) {
            panVerifyBtn.addEventListener('click', function () {
                var panInput = document.getElementById('panNumber');
                var holderInput = document.getElementById('panHolderName');
                var dobInput = document.getElementById('panDob');
                var documentInput = document.getElementById('panFileInput');
                if (!panInput || !panInput.value) {
                    showAlert('Please enter your PAN number first.', 'error');
                    return;
                }
                var pan = panInput.value.toUpperCase().replace(/\s+/g, '');
                if (!/^[A-Z]{5}[0-9]{4}[A-Z]$/.test(pan)) {
                    showAlert('Invalid PAN format. It must be 5 letters, 4 digits, and 1 letter (e.g. ABCDE1234F).', 'error');
                    return;
                }
                if (!holderInput || !holderInput.value.trim()) {
                    showAlert('Please enter the PAN holder name before verification.', 'error');
                    return;
                }
                if (!dobInput || !dobInput.value) {
                    showAlert('Please enter the PAN date of birth before verification.', 'error');
                    return;
                }
                if (!validateImageDocument(documentInput, true)) {
                    return;
                }

                var verifyUrl = form.getAttribute('data-verify-pan-url');
                var verifyData = new FormData();
                verifyData.append('pan_number', pan);
                verifyData.append('pan_holder_name', holderInput.value.trim());
                verifyData.append('pan_dob', dobInput.value);
                verifyData.append('pan_document', documentInput.files[0]);

                panVerified = false;
                panVerifyBtn.disabled = true;
                panVerifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Verifying...';

                fetch(verifyUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: verifyData
                })
                    .then(function (res) {
                        return res.json().then(function (data) {
                            return { ok: res.ok, data: data };
                        });
                    })
                    .then(function (result) {
                        var data = result.data || {};
                        if (result.ok && data.success) {
                            panVerified = true;
                            panVerifyBtn.style.display = 'none';
                            if (panVerifiedBadge) {
                                panVerifiedBadge.style.display = 'inline-block';
                            }
                            panInput.setAttribute('readonly', 'readonly');
                            holderInput.setAttribute('readonly', 'readonly');
                            dobInput.setAttribute('readonly', 'readonly');
                            documentInput.disabled = false;
                            showAlert(data.message || 'PAN verified successfully!', 'success');
                        } else {
                            var message = data.message || 'PAN verification failed.';
                            if (data.errors) {
                                message = Object.values(data.errors).flat().join(' ');
                            }
                            showAlert(message, 'error');
                            panVerifyBtn.disabled = false;
                            panVerifyBtn.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Verify PAN';
                        }
                    })
                    .catch(function () {
                        panVerified = false;
                        panVerifyBtn.style.display = '';
                        if (panVerifiedBadge) {
                            panVerifiedBadge.style.display = 'none';
                        }
                        panInput.removeAttribute('readonly');
                        holderInput.removeAttribute('readonly');
                        dobInput.removeAttribute('readonly');
                        panVerifyBtn.disabled = false;
                        panVerifyBtn.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Verify PAN';
                        showAlert('PAN verification could not be completed. Please check your connection and try again.', 'error');
                    });
            });

            ['panNumber', 'panHolderName', 'panDob', 'panFileInput'].forEach(function (id) {
                var input = document.getElementById(id);
                if (input) {
                    input.addEventListener('change', function () {
                        if (!panVerified) {
                            return;
                        }
                        panVerified = false;
                        panVerifyBtn.style.display = '';
                        panVerifyBtn.disabled = false;
                        if (panVerifiedBadge) {
                            panVerifiedBadge.style.display = 'none';
                        }
                    });
                }
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
            if (!panVerified) {
                showAlert('Please verify the PAN details and PAN image before submitting KYC.', 'error');
                showPanel(2);
                return;
            }

            for (var i = 0; i < imageOnlyDocumentNames.length; i++) {
                var imageInput = form.querySelector('[name="' + imageOnlyDocumentNames[i] + '"]');
                if (!validateImageDocument(imageInput, true)) {
                    return;
                }
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
