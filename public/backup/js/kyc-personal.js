// ============================================================
// Personal KYC (CSB-IV) Wizard - Navigation, Verify & Submit
// ============================================================

(function () {
    'use strict';

    var totalSteps = 6;
    var currentStep = 1;

    // ------------------------------------------------------------
    // Wizard navigation helpers
    // ------------------------------------------------------------
    function showStep(step) {
        // Hide all panels
        var panels = document.querySelectorAll('.wizard-panel');
        panels.forEach(function (p) {
            p.classList.remove('active');
        });

        // Show target panel
        var target = document.querySelector('.wizard-panel[data-panel="' + step + '"]');
        if (target) {
            target.classList.add('active');
        }

        // Update step indicators
        var steps = document.querySelectorAll('.wizard-step');
        steps.forEach(function (s) {
            var sNum = parseInt(s.getAttribute('data-step'), 10);
            s.classList.remove('active', 'completed');
            if (sNum < step) {
                s.classList.add('completed');
            } else if (sNum === step) {
                s.classList.add('active');
            }
        });

        // Update progress bar
        var barFill = document.getElementById('wizardBarFill');
        if (barFill) {
            var pct = (step / totalSteps) * 100;
            barFill.style.width = pct + '%';
        }

        currentStep = step;

        // If landing on summary step, populate it
        if (step === 4) {
            populateSummary();
        }

        // Scroll to top of form
        var card = document.querySelector('.kyc-card');
        if (card) {
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function validateStep(step) {
        var panel = document.querySelector('.wizard-panel[data-panel="' + step + '"]');
        if (!panel) {
            return true;
        }

        // Check required fields within this panel
        var requiredFields = panel.querySelectorAll('[required]');
        var valid = true;
        var firstInvalid = null;

        requiredFields.forEach(function (field) {
            // Skip hidden file inputs (they are validated by their own logic)
            if (field.type === 'file' && field.style.display === 'none') {
                // For file inputs, check if a file is selected OR an existing value exists
                if (!field.files || field.files.length === 0) {
                    // Check if there's an existing document (edit mode) - allow proceeding
                    // The backend will handle the final validation
                    return;
                }
            }

            if (!field.value || (field.type === 'checkbox' && !field.checked)) {
                valid = false;
                if (!firstInvalid) {
                    firstInvalid = field;
                }
                field.style.borderColor = '#ef4444';
            } else {
                field.style.borderColor = '';
            }
        });

        if (!valid && firstInvalid) {
            firstInvalid.focus();
            showAlert('Please fill in all required fields before continuing.', 'warning');
        }

        return valid;
    }

    // ------------------------------------------------------------
    // Step navigation buttons
    // ------------------------------------------------------------
    document.querySelectorAll('.wizard-next').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var next = parseInt(this.getAttribute('data-next'), 10);
            if (validateStep(currentStep)) {
                showStep(next);
            }
        });
    });

    document.querySelectorAll('.wizard-prev').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var prev = parseInt(this.getAttribute('data-prev'), 10);
            showStep(prev);
        });
    });

    // Allow clicking on completed/active step indicators to navigate
    document.querySelectorAll('.wizard-step').forEach(function (stepEl) {
        stepEl.addEventListener('click', function () {
            var target = parseInt(this.getAttribute('data-step'), 10);
            // Only allow going back to completed steps or current step
            if (target <= currentStep) {
                showStep(target);
            }
        });
        stepEl.style.cursor = 'pointer';
    });

    // ------------------------------------------------------------
    // Aadhaar number input - digits only
    // ------------------------------------------------------------
    var aadharInput = document.getElementById('aadharNumber');
    if (aadharInput) {
        aadharInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);
        });
    }

    // ------------------------------------------------------------
    // Aadhaar Verify button
    // ------------------------------------------------------------
    var aadharVerifyBtn = document.getElementById('aadharVerifyBtn');
    if (aadharVerifyBtn) {
        aadharVerifyBtn.addEventListener('click', function () {
            var aadhar = aadharInput.value.trim();
            var verifiedBadge = document.getElementById('aadharVerifiedBadge');

            if (!aadhar || aadhar.length !== 12) {
                showAlert('Please enter a valid 12-digit Aadhaar number.', 'warning');
                return;
            }

            // Aadhaar must not start with 0 or 1
            if (/^[01]/.test(aadhar)) {
                showAlert('Invalid Aadhaar number. Please check and try again.', 'warning');
                return;
            }

            var originalHTML = this.innerHTML;
            this.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> VERIFYING...';
            this.disabled = true;

            // Simulate verification (format-based client-side check)
            setTimeout(function () {
                aadharVerifyBtn.style.display = 'none';
                if (verifiedBadge) {
                    verifiedBadge.style.display = 'inline-flex';
                }
                aadharVerifyBtn.innerHTML = originalHTML;
            }, 1000);
        });
    }

    // ------------------------------------------------------------
    // PAN number input - uppercase, alphanumeric only
    // ------------------------------------------------------------
    var panInput = document.getElementById('panNumber');
    if (panInput) {
        panInput.addEventListener('input', function () {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 10);
        });
    }

    // ------------------------------------------------------------
    // PAN Verify button (AJAX to backend)
    // ------------------------------------------------------------
    var panVerifyBtn = document.getElementById('panVerifyBtn');
    if (panVerifyBtn) {
        panVerifyBtn.addEventListener('click', function () {
            var pan = panInput.value.trim();
            var verifiedBadge = document.getElementById('panVerifiedBadge');

            if (!pan || pan.length !== 10) {
                showAlert('Please enter a valid 10-character PAN number.', 'warning');
                return;
            }

            // Client-side format check: 5 letters + 4 digits + 1 letter
            if (!/^[A-Z]{5}[0-9]{4}[A-Z]$/.test(pan)) {
                showAlert('Invalid PAN format. Expected format: 5 letters, 4 digits, 1 letter (e.g. ABCDE1234F).', 'warning');
                return;
            }

            var originalHTML = this.innerHTML;
            this.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> VERIFYING...';
            this.disabled = true;

            fetch(form.getAttribute('data-verify-pan-url'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                        ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        : document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ pan_number: pan })
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success) {
                    panVerifyBtn.style.display = 'none';
                    if (verifiedBadge) {
                        verifiedBadge.style.display = 'inline-flex';
                    }
                } else {
                    showAlert(data.message || 'PAN verification failed. Please check the number and try again.', 'error');
                    panVerifyBtn.disabled = false;
                }
                panVerifyBtn.innerHTML = originalHTML;
            })
            .catch(function (error) {
                console.error('PAN verify error:', error);
                showAlert('An error occurred during PAN verification. Please try again.', 'error');
                panVerifyBtn.disabled = false;
                panVerifyBtn.innerHTML = originalHTML;
            });
        });
    }

    // ------------------------------------------------------------
    // Populate Summary (Step 4)
    // ------------------------------------------------------------
    function populateSummary() {
        var aadhar = document.getElementById('aadharNumber');
        var aadharAddress = document.getElementById('aadharAddress');
        var pan = document.getElementById('panNumber');
        var panName = document.getElementById('panHolderName');
        var dob = document.getElementById('panDob');

        setSummary('summaryAadhar', aadhar ? maskAadhar(aadhar.value) : '');
        setSummary('summaryAadharAddress', aadharAddress ? aadharAddress.value : '');
        setSummary('summaryPan', pan ? pan.value : '');
        setSummary('summaryPanName', panName ? panName.value : '');
        setSummary('summaryDob', dob ? formatDob(dob.value) : '');

        setFileSummary('aadharFrontFileInput', 'summaryAadharFront');
        setFileSummary('aadharBackFileInput', 'summaryAadharBack');
        setFileSummary('panFileInput', 'summaryPanDoc');
        setFileSummary('signatureFileInput', 'summarySignature');
    }

    function setSummary(id, value) {
        var el = document.getElementById(id);
        if (el) {
            el.textContent = value || '—';
            if (value) {
                el.classList.add('verified');
            } else {
                el.classList.remove('verified');
            }
        }
    }

    function setFileSummary(inputId, summaryId) {
        var input = document.getElementById(inputId);
        var el = document.getElementById(summaryId);
        if (input && el) {
            if (input.files && input.files.length > 0) {
                el.textContent = input.files[0].name;
                el.classList.add('verified');
            } else {
                el.textContent = 'Not uploaded';
                el.classList.remove('verified');
            }
        }
    }

    function maskAadhar(num) {
        if (!num || num.length < 4) {
            return num || '';
        }
        return 'XXXXXXXX' + num.slice(-4);
    }

    function formatDob(dob) {
        if (!dob) {
            return '';
        }
        var parts = dob.split('-');
        if (parts.length === 3) {
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }
        return dob;
    }

    // ------------------------------------------------------------
    // Form Submit
    // ------------------------------------------------------------
    var form = document.getElementById('personalKycForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = document.getElementById('submitKycBtn');
            var formData = new FormData(form);

            // Validate terms checkbox
            var terms = document.getElementById('termsAccepted');
            if (terms && !terms.checked) {
                showAlert('Please accept the terms and conditions to complete your KYC.', 'warning');
                return;
            }

            // Validate merchant agreement upload
            var agreementInput = document.getElementById('merchantAgreementFileInput');
            if (!agreementInput || agreementInput.files.length === 0) {
                showAlert('Please upload the signed merchant agreement.', 'warning');
                return;
            }

            if (btn) {
                btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> SUBMITTING...';
                btn.style.opacity = '0.8';
                btn.style.pointerEvents = 'none';
            }

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success) {
                    if (btn) {
                        btn.innerHTML = 'SUCCESS';
                        btn.style.background = '#10b981';
                        btn.style.opacity = '1';
                    }
                    setTimeout(function () {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    if (btn) {
                        btn.innerHTML = 'COMPLETE KYC <i class="fas fa-check-circle ms-2"></i>';
                        btn.style.opacity = '1';
                        btn.style.pointerEvents = 'auto';
                    }
                    if (data.errors) {
                        var errorMessage = 'Please fix the following errors:\n';
                        for (var key in data.errors) {
                            if (data.errors.hasOwnProperty(key)) {
                                errorMessage += '- ' + data.errors[key][0] + '\n';
                            }
                        }
                        showAlert(errorMessage, 'error');
                    } else {
                        showAlert(data.message || 'An error occurred. Please try again.', 'error');
                    }
                }
            })
            .catch(function (error) {
                console.error('Submit error:', error);
                if (btn) {
                    btn.innerHTML = 'COMPLETE KYC <i class="fas fa-check-circle ms-2"></i>';
                    btn.style.opacity = '1';
                    btn.style.pointerEvents = 'auto';
                }
                showAlert('An error occurred while submitting. Please try again.', 'error');
            });
        });
    }

    // ------------------------------------------------------------
    // Initialize: if existing KYC data present, show verified states
    // The form carries data-aadhar-verified / data-pan-verified
    // attributes (set by the blade template) so we can avoid mixing
    // Blade directives into a plain JS file.
    // ------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', function () {
        var formEl = document.getElementById('personalKycForm');
        if (!formEl) {
            return;
        }

        // If Aadhaar already verified (existing record), show badge
        if (formEl.getAttribute('data-aadhar-verified') === '1') {
            var aadharBadge = document.getElementById('aadharVerifiedBadge');
            var aadharBtn = document.getElementById('aadharVerifyBtn');
            if (aadharBadge) { aadharBadge.style.display = 'inline-flex'; }
            if (aadharBtn) { aadharBtn.style.display = 'none'; }
        }

        // If PAN already verified (existing record), show badge
        if (formEl.getAttribute('data-pan-verified') === '1') {
            var panBadge = document.getElementById('panVerifiedBadge');
            var panBtn = document.getElementById('panVerifyBtn');
            if (panBadge) { panBadge.style.display = 'inline-flex'; }
            if (panBtn) { panBtn.style.display = 'none'; }
        }

        // ------------------------------------------------------------
        // "Use Aadhaar address as billing address" auto-fill toggle
        // ------------------------------------------------------------
        var useAadharChk = document.getElementById('useAadharAddress');
        var aadharAddrEl = document.getElementById('aadharAddress');
        var billingAddrEl = document.getElementById('billingAddress');

        if (useAadharChk && aadharAddrEl && billingAddrEl) {
            // Remember any manually-entered billing address so the user
            // can restore it if they uncheck the toggle.
            var manualBillingAddress = billingAddrEl.value;

            useAadharChk.addEventListener('change', function () {
                if (useAadharChk.checked) {
                    // Save the current manual value before overwriting
                    manualBillingAddress = billingAddrEl.value;
                    // Copy Aadhaar address into billing address
                    billingAddrEl.value = aadharAddrEl.value;
                    billingAddrEl.setAttribute('readonly', 'readonly');
                    billingAddrEl.classList.add('auto-filled');
                } else {
                    // Restore the manual value
                    billingAddrEl.value = manualBillingAddress;
                    billingAddrEl.removeAttribute('readonly');
                    billingAddrEl.classList.remove('auto-filled');
                }
            });

            // If the Aadhaar address changes while the toggle is on, keep
            // the billing address in sync.
            aadharAddrEl.addEventListener('input', function () {
                if (useAadharChk.checked) {
                    billingAddrEl.value = aadharAddrEl.value;
                }
            });
        }
    });

})();
