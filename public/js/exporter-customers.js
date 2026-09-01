/**
 * exporter-customers.js
 * Handles the Exporter Customer KYC-style 4-step wizard:
 *   - Step navigation (next/prev) + progress bar
 *   - CSB IV/V + business customer type conditional toggles
 *   - LUT bond year/expiry sync
 *   - Cashfree Aadhaar (front + back) & PAN OCR verification with Step-3 autofill
 *   - AJAX form submission with FormData (so files upload correctly)
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('exporterCustomerForm');
        if (!form) {
            return;
        }

        /* ------------------------------------------------------------------
           Wizard navigation & progress
        ------------------------------------------------------------------ */
        var panels = form.querySelectorAll('.wizard-panel');
        var barFill = document.getElementById('wizardBarFill');
        var totalPanels = panels.length;
        var wizardSteps = document.querySelectorAll('.wizard-step');

        function updateProgressBar(currentStep) {
            if (!barFill) {
                return;
            }
            // Count only the visible steps so the fill stays aligned to the
            // circle centres when Step 4 is hidden (CSB IV mode).
            var visible = 0;
            wizardSteps.forEach(function (stepEl) {
                if (!stepEl.classList.contains('d-none')) {
                    visible++;
                }
            });
            if (visible < 1) {
                visible = 1;
            }
            // Fill ends at the centre of the current step's circle
            var pct = ((currentStep - 0.5) / visible) * 100;
            if (pct < 0) {
                pct = 0;
            }
            if (pct > 100) {
                pct = 100;
            }
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
            wizardSteps.forEach(function (stepEl) {
                var stepNum = parseInt(stepEl.getAttribute('data-step'), 10);
                stepEl.classList.toggle('active', stepNum === step);
                stepEl.classList.toggle('completed', stepNum < step);
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

        /* ------------------------------------------------------------------
           Alert helper (renders .kyc-ajax-alert at top of the form)
        ------------------------------------------------------------------ */
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

        /* ------------------------------------------------------------------
           PAN DOB parsing (accepts Y-m-d or d/m/Y)
        ------------------------------------------------------------------ */
        function parseValidPanDob(value) {
            var normalized = String(value || '').trim();
            var match = normalized.match(/^(\d{4})-(\d{2})-(\d{2})$/);
            var year;
            var month;
            var day;

            if (match) {
                year = Number(match[1]);
                month = Number(match[2]);
                day = Number(match[3]);
            } else {
                match = normalized.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
                if (!match) {
                    return null;
                }
                day = Number(match[1]);
                month = Number(match[2]);
                year = Number(match[3]);
            }

            var today = new Date();
            var date = new Date(year, month - 1, day);
            if (year < 1900 || year > today.getFullYear() ||
                month < 1 || month > 12 || day < 1 || day > 31 ||
                date.getFullYear() !== year || date.getMonth() !== month - 1 ||
                date.getDate() !== day ||
                date >= new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
                return null;
            }

            var paddedMonth = String(month).padStart(2, '0');
            var paddedDay = String(day).padStart(2, '0');
            return {
                ymd: String(year).padStart(4, '0') + '-' + paddedMonth + '-' + paddedDay,
                dmy: paddedDay + '/' + paddedMonth + '/' + String(year).padStart(4, '0')
            };
        }

        /* ------------------------------------------------------------------
           Image upload validation (Aadhaar front/back + PAN card)
        ------------------------------------------------------------------ */
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

        /* ------------------------------------------------------------------
           Input sanitisation (numeric + IEC uppercase)
        ------------------------------------------------------------------ */
        var numericFields = [
            document.getElementById('pincode'),
            document.getElementById('phoneNumber'),
            document.getElementById('adCode'),
            document.getElementById('bankAccountNumber'),
            document.getElementById('billingContact')
        ].filter(Boolean);

        numericFields.forEach(function (field) {
            field.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, Number(this.maxLength));
            });
        });

        var iecNumber = document.getElementById('iecNumber');
        if (iecNumber) {
            iecNumber.addEventListener('input', function () {
                this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase().slice(0, 10);
            });
        }

        /* ------------------------------------------------------------------
           CSB IV / CSB V + customer type + LUT conditional logic
        ------------------------------------------------------------------ */
        var customerType = document.getElementById('businessCategoryId');
        var csbType = document.getElementById('csbType');
        var csbTypeCheck = document.getElementById('csbTypeCheck');
        var csbTypeHint = document.getElementById('csbTypeHint');
        var csbVFields = document.getElementById('csbVFields');
        var csbVStepEl = document.querySelector('.wizard-step[data-step="4"]');
        var step3ActionBtn = document.getElementById('step3ActionBtn');
        var isLut = document.getElementById('isLut');
        var lutFields = document.getElementById('lutFields');
        var lutStartYear = document.getElementById('lutBondStartYear');
        var lutEndYear = document.getElementById('lutBondEndYear');
        var lutBondYear = document.getElementById('lutBondYear');
        var lutExpiryDate = document.getElementById('lutExpiryDate');
        var lutDocument = document.getElementById('lutFileInput');

        function isCsbVSelected() {
            return csbTypeCheck ? csbTypeCheck.checked : (csbType && csbType.value === 'csb_v');
        }

        function syncLutExpiryDate() {
            if (!lutEndYear.value) {
                lutExpiryDate.value = '';
                return;
            }
            lutExpiryDate.value = lutEndYear.value + '-03-31';
        }

        function updateLutYears(restoreSavedYear) {
            var startYear = Number(lutStartYear.value);
            var savedEndYear = restoreSavedYear ? lutEndYear.dataset.savedEndYear : '';
            lutEndYear.innerHTML = '';

            if (!startYear) {
                lutEndYear.appendChild(new Option('Select Start Year First', ''));
                lutEndYear.disabled = true;
                lutBondYear.value = '';
                lutExpiryDate.value = '';
                return;
            }

            lutEndYear.appendChild(new Option('Select End Year', ''));
            for (var offset = 1; offset <= 5; offset += 1) {
                lutEndYear.appendChild(new Option(String(startYear + offset), String(startYear + offset)));
            }
            lutEndYear.disabled = false;
            lutEndYear.value = savedEndYear && lutEndYear.querySelector('option[value="' + savedEndYear + '"]')
                ? savedEndYear
                : String(startYear + 1);
            lutBondYear.value = startYear + '-' + lutEndYear.value.slice(-2);
            syncLutExpiryDate();
        }

        function updateLutState() {
            var csbVEnabled = isCsbVSelected();
            var enabled = csbVEnabled && isLut.checked;
            lutFields.classList.toggle('d-none', !csbVEnabled);
            lutFields.querySelectorAll('input, select').forEach(function (field) {
                field.disabled = !enabled;
            });
            lutStartYear.required = enabled;
            lutEndYear.required = enabled;
            lutExpiryDate.required = enabled;
            if (lutDocument) {
                lutDocument.required = enabled;
            }
            if (enabled) {
                updateLutYears(true);
            }
        }

        function updateCustomerTypeState() {
            var selectedOption = customerType.options[customerType.selectedIndex];
            var isBusiness = selectedOption && selectedOption.dataset.userType === 'business';

            if (csbTypeCheck) {
                if (isBusiness) {
                    csbTypeCheck.checked = true;
                    csbTypeCheck.disabled = true;
                } else {
                    csbTypeCheck.disabled = false;
                }
            }
            if (csbTypeHint) {
                csbTypeHint.textContent = isBusiness
                    ? 'Business customers are eligible for CSB V only.'
                    : 'Personal customers can select CSB IV or CSB V.';
            }

            updateCsbState();
        }

        function updateCsbState() {
            var enabled = isCsbVSelected();
            if (csbType) {
                csbType.value = enabled ? 'csb_v' : 'csb_iv';
            }
            csbVFields.classList.toggle('d-none', !enabled);
            csbVFields.setAttribute('aria-hidden', enabled ? 'false' : 'true');
            csbVFields.querySelectorAll('input, select, textarea').forEach(function (field) {
                field.disabled = !enabled;
            });
            csbVFields.querySelectorAll('[data-csb-v-required]').forEach(function (field) {
                field.required = enabled;
            });
            if (csbVStepEl) {
                csbVStepEl.classList.toggle('d-none', !enabled);
            }
            if (step3ActionBtn) {
                if (enabled) {
                    step3ActionBtn.type = 'button';
                    step3ActionBtn.setAttribute('data-next', '4');
                    step3ActionBtn.innerHTML = 'NEXT <i class="fas fa-arrow-right ms-2"></i>';
                } else {
                    step3ActionBtn.type = 'submit';
                    step3ActionBtn.removeAttribute('data-next');
                    step3ActionBtn.innerHTML = 'SAVE CUSTOMER <i class="fas fa-check-circle ms-2"></i>';
                }
            }
            // Refresh the progress fill for the new number of visible steps
            var currentStep = 1;
            panels.forEach(function (panel) {
                if (panel.classList.contains('active')) {
                    currentStep = parseInt(panel.getAttribute('data-panel'), 10) || 1;
                }
            });
            updateProgressBar(currentStep);
            updateLutState();
        }

        if (step3ActionBtn) {
            step3ActionBtn.addEventListener('click', function () {
                if (isCsbVSelected()) {
                    showPanel(4);
                }
            });
        }

        if (customerType) {
            customerType.addEventListener('change', updateCustomerTypeState);
        }
        if (csbTypeCheck) {
            csbTypeCheck.addEventListener('change', updateCsbState);
        }
        if (isLut) {
            isLut.addEventListener('change', updateLutState);
        }
        if (lutStartYear) {
            lutStartYear.addEventListener('change', function () {
                updateLutYears(false);
            });
        }
        if (lutEndYear) {
            lutEndYear.addEventListener('change', function () {
                lutBondYear.value = lutStartYear.value && lutEndYear.value
                    ? lutStartYear.value + '-' + lutEndYear.value.slice(-2)
                    : '';
                syncLutExpiryDate();
            });
        }

        /* ------------------------------------------------------------------
           KYC type toggle (Aadhaar <-> PAN)
        ------------------------------------------------------------------ */
        var kycType = document.getElementById('kycType');
        var aadharKycSection = document.getElementById('aadharKycSection');
        var panKycSection = document.getElementById('panKycSection');

        var aadharVerifyBtn = document.getElementById('aadharVerifyBtn');
        var aadharVerifiedBadge = document.getElementById('aadharVerifiedBadge');
        var aadharVerifyStatus = document.getElementById('aadharVerifyStatus');
        var aadharNumber = document.getElementById('aadharNumber');
        var aadharFrontFileInput = document.getElementById('aadharFrontFileInput');
        var aadharBackFileInput = document.getElementById('aadharBackFileInput');

        var panVerifyBtn = document.getElementById('panVerifyBtn');
        var panVerifiedBadge = document.getElementById('panVerifiedBadge');
        var panVerifyStatus = document.getElementById('panVerifyStatus');
        var panNumber = document.getElementById('panNumber');
        var panHolderName = document.getElementById('panHolderName');
        var panDob = document.getElementById('panDob');
        var panFileInput = document.getElementById('panFileInput');

        var aadharVerified = false;
        var panVerified = false;

        function showKycVerifyStatus(statusEl, message, type) {
            if (!statusEl) {
                return;
            }
            statusEl.style.display = 'block';
            statusEl.className = 'kyc-alert ' + type;
            var icon = 'fa-circle-info';
            if (type === 'success') { icon = 'fa-circle-check'; }
            if (type === 'error') { icon = 'fa-circle-exclamation'; }
            statusEl.innerHTML = '<i class="fas ' + icon + ' me-1"></i>' + message;
        }

        function clearAutofill() {
            ['contactPerson', 'addressLine1', 'pincode', 'city', 'state'].forEach(function (id) {
                var el = document.getElementById(id);
                if (el) {
                    el.classList.remove('auto-filled');
                }
            });
        }

        function setAutofillValue(id, value) {
            var el = document.getElementById(id);
            if (el && value) {
                el.value = value;
                el.classList.add('auto-filled');
            }
        }

        function applyAadharAutofill(autofill) {
            if (!autofill) {
                return;
            }
            setAutofillValue('contactPerson', autofill.name);
            setAutofillValue('addressLine1', autofill.address_line1);
            setAutofillValue('pincode', autofill.pincode);
            setAutofillValue('city', autofill.city);
            setAutofillValue('state', autofill.state);
        }

        function applyPanAutofill(autofill) {
            if (!autofill) {
                return;
            }
            setAutofillValue('contactPerson', autofill.name);
        }

        function resetVerificationState() {
            aadharVerified = false;
            if (aadharVerifyBtn) {
                aadharVerifyBtn.style.display = '';
                aadharVerifyBtn.disabled = false;
                aadharVerifyBtn.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Verify Aadhaar';
            }
            if (aadharVerifiedBadge) { aadharVerifiedBadge.style.display = 'none'; }
            if (aadharVerifyStatus) {
                aadharVerifyStatus.style.display = 'none';
                aadharVerifyStatus.className = 'kyc-alert';
            }
            if (aadharNumber) { aadharNumber.removeAttribute('readonly'); }
            if (aadharFrontFileInput) { aadharFrontFileInput.disabled = false; }
            if (aadharBackFileInput) { aadharBackFileInput.disabled = false; }

            panVerified = false;
            if (panVerifyBtn) {
                panVerifyBtn.style.display = '';
                panVerifyBtn.disabled = false;
                panVerifyBtn.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Verify PAN';
            }
            if (panVerifiedBadge) { panVerifiedBadge.style.display = 'none'; }
            if (panVerifyStatus) {
                panVerifyStatus.style.display = 'none';
                panVerifyStatus.className = 'kyc-alert';
            }
            if (panNumber) { panNumber.removeAttribute('readonly'); }
            if (panHolderName) { panHolderName.removeAttribute('readonly'); }
            if (panDob) { panDob.removeAttribute('readonly'); }
            if (panFileInput) { panFileInput.disabled = false; }

            clearAutofill();
        }

        function updateKycSectionState() {
            var isAadhar = kycType && kycType.value === 'Aadhar Card';
            if (aadharKycSection) { aadharKycSection.classList.toggle('d-none', !isAadhar); }
            if (panKycSection) { panKycSection.classList.toggle('d-none', isAadhar); }
            if (aadharNumber) { aadharNumber.required = isAadhar; }
            if (aadharFrontFileInput) { aadharFrontFileInput.required = isAadhar; }
            if (aadharBackFileInput) { aadharBackFileInput.required = isAadhar; }
            if (panNumber) { panNumber.required = !isAadhar; }
            if (panHolderName) { panHolderName.required = !isAadhar; }
            if (panDob) { panDob.required = !isAadhar; }
            if (panFileInput) { panFileInput.required = !isAadhar; }
        }

        if (kycType) {
            kycType.addEventListener('change', function () {
                updateKycSectionState();
                resetVerificationState();
            });
        }

        /* ------------------------------------------------------------------
           Aadhaar verification (Cashfree OCR) - front + back images
        ------------------------------------------------------------------ */
        if (aadharVerifyBtn) {
            aadharVerifyBtn.addEventListener('click', function () {
                if (!aadharNumber || !aadharNumber.value) {
                    showAlert('Please enter the Aadhaar number first.', 'error');
                    return;
                }
                var aadhar = aadharNumber.value.replace(/\s+/g, '');
                if (!/^[2-9][0-9]{11}$/.test(aadhar)) {
                    showAlert('Invalid Aadhaar number. It must be 12 digits and cannot start with 0 or 1.', 'error');
                    return;
                }
                if (!aadharFrontFileInput.files || !aadharFrontFileInput.files[0]) {
                    showAlert('Please upload the Aadhaar front image before verification.', 'error');
                    return;
                }
                if (!aadharBackFileInput.files || !aadharBackFileInput.files[0]) {
                    showAlert('Please upload the Aadhaar back image before verification.', 'error');
                    return;
                }
                if (!validateImageDocument(aadharFrontFileInput, true)) {
                    return;
                }
                if (!validateImageDocument(aadharBackFileInput, true)) {
                    return;
                }

                var verifyData = new FormData();
                verifyData.append('aadhar_number', aadhar);
                verifyData.append('aadhar_front_document', aadharFrontFileInput.files[0]);
                verifyData.append('aadhar_back_document', aadharBackFileInput.files[0]);

                aadharVerified = false;
                aadharVerifyBtn.disabled = true;
                aadharVerifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Verifying...';

                fetch(form.getAttribute('data-verify-aadhar-url'), {
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
                            aadharVerified = true;
                            aadharVerifyBtn.style.display = 'none';
                            if (aadharVerifiedBadge) { aadharVerifiedBadge.style.display = 'inline-block'; }
                            if (aadharNumber) { aadharNumber.setAttribute('readonly', 'readonly'); }
                            if (aadharFrontFileInput) { aadharFrontFileInput.disabled = false; }
                            if (aadharBackFileInput) { aadharBackFileInput.disabled = false; }
                            showKycVerifyStatus(aadharVerifyStatus, data.message || 'Aadhaar verified successfully!', 'success');
                            applyAadharAutofill(data.autofill || {});
                        } else {
                            var message = data.message || 'Aadhaar verification failed.';
                            if (data.errors) {
                                message = Object.values(data.errors).flat().join(' ');
                            }
                            showKycVerifyStatus(aadharVerifyStatus, message, 'error');
                            aadharVerifyBtn.disabled = false;
                            aadharVerifyBtn.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Verify Aadhaar';
                        }
                    })
                    .catch(function () {
                        aadharVerified = false;
                        aadharVerifyBtn.disabled = false;
                        aadharVerifyBtn.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Verify Aadhaar';
                        showKycVerifyStatus(aadharVerifyStatus, 'Aadhaar verification could not be completed. Please check your connection and try again.', 'error');
                    });
            });

            ['aadharNumber', 'aadharFrontFileInput', 'aadharBackFileInput'].forEach(function (id) {
                var input = document.getElementById(id);
                if (input) {
                    input.addEventListener('change', function () {
                        if (!aadharVerified) {
                            return;
                        }
                        aadharVerified = false;
                        if (aadharVerifyBtn) {
                            aadharVerifyBtn.style.display = '';
                            aadharVerifyBtn.disabled = false;
                        }
                        if (aadharVerifiedBadge) { aadharVerifiedBadge.style.display = 'none'; }
                        if (aadharVerifyStatus) { aadharVerifyStatus.style.display = 'none'; }
                    });
                }
            });
        }

        /* ------------------------------------------------------------------
           PAN verification (Cashfree OCR) - holder name + DOB must match
        ------------------------------------------------------------------ */
        if (panVerifyBtn) {
            panVerifyBtn.addEventListener('click', function () {
                if (!panNumber || !panNumber.value) {
                    showAlert('Please enter the PAN number first.', 'error');
                    return;
                }
                var pan = panNumber.value.toUpperCase().replace(/\s+/g, '');
                if (!/^[A-Z]{5}[0-9]{4}[A-Z]$/.test(pan)) {
                    showAlert('Invalid PAN format. It must be 5 letters, 4 digits, and 1 letter (e.g. ABCDE1234F).', 'error');
                    return;
                }
                if (!panHolderName || !panHolderName.value.trim()) {
                    showAlert('Please enter the PAN holder name before verification.', 'error');
                    return;
                }
                if (!panDob || !panDob.value) {
                    showAlert('Please enter the PAN date of birth before verification.', 'error');
                    return;
                }

                var parsedPanDob = parseValidPanDob(panDob.value);
                if (!parsedPanDob) {
                    showAlert('Please select a valid PAN date of birth in DD/MM/YYYY format.', 'error');
                    if (panDob._flatpickr) {
                        panDob._flatpickr.clear();
                    } else {
                        panDob.value = '';
                    }
                    panDob.focus();
                    return;
                }

                if (!validateImageDocument(panFileInput, true)) {
                    return;
                }

                var verifyData = new FormData();
                verifyData.append('pan_number', pan);
                verifyData.append('pan_holder_name', panHolderName.value.trim());
                verifyData.append('pan_dob', parsedPanDob.dmy);
                verifyData.append('pan_document', panFileInput.files[0]);

                panVerified = false;
                panVerifyBtn.disabled = true;
                panVerifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Verifying...';

                fetch(form.getAttribute('data-verify-pan-url'), {
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
                            if (panVerifiedBadge) { panVerifiedBadge.style.display = 'inline-block'; }
                            if (panNumber) { panNumber.setAttribute('readonly', 'readonly'); }
                            if (panHolderName) { panHolderName.setAttribute('readonly', 'readonly'); }
                            if (panDob) { panDob.setAttribute('readonly', 'readonly'); }
                            if (panFileInput) { panFileInput.disabled = false; }
                            showKycVerifyStatus(panVerifyStatus, data.message || 'PAN verified successfully!', 'success');
                            applyPanAutofill(data.autofill || {});
                        } else {
                            var message = data.message || 'PAN verification failed.';
                            if (data.errors) {
                                message = Object.values(data.errors).flat().join(' ');
                            }
                            showKycVerifyStatus(panVerifyStatus, message, 'error');
                            panVerifyBtn.disabled = false;
                            panVerifyBtn.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Verify PAN';
                        }
                    })
                    .catch(function () {
                        panVerified = false;
                        panVerifyBtn.disabled = false;
                        panVerifyBtn.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Verify PAN';
                        showKycVerifyStatus(panVerifyStatus, 'PAN verification could not be completed. Please check your connection and try again.', 'error');
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
                        if (panVerifyBtn) {
                            panVerifyBtn.style.display = '';
                            panVerifyBtn.disabled = false;
                        }
                        if (panVerifiedBadge) { panVerifiedBadge.style.display = 'none'; }
                        if (panVerifyStatus) { panVerifyStatus.style.display = 'none'; }
                    });
                }
            });
        }

        /* ------------------------------------------------------------------
           Form submission (AJAX with FormData)
        ------------------------------------------------------------------ */
        var submitBtn = document.getElementById('submitCustomerBtn');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            var isAadhar = kycType && kycType.value === 'Aadhar Card';

            if (isAadhar) {
                if (!aadharVerified) {
                    showAlert('Please verify the Aadhaar details and both Aadhaar images before saving the customer.', 'error');
                    showPanel(2);
                    return;
                }
            } else {
                if (!panVerified) {
                    showAlert('Please verify the PAN details and PAN image before saving the customer.', 'error');
                    showPanel(2);
                    return;
                }
            }

            var requiredDocs = isAadhar
                ? ['aadhar_front_document', 'aadhar_back_document']
                : ['pan_document'];

            for (var i = 0; i < requiredDocs.length; i++) {
                var imageInput = form.querySelector('[name="' + requiredDocs[i] + '"]');
                if (!validateImageDocument(imageInput, true)) {
                    showPanel(2);
                    return;
                }
            }

            var formData = new FormData(form);
            // The backend derives kyc_number from pan_number/aadhar_number - never send it directly.
            if (formData.has('kyc_number')) {
                formData.delete('kyc_number');
            }

            if (!isAadhar && panDob) {
                var parsedSubmittedDob = parseValidPanDob(panDob.value);
                if (!parsedSubmittedDob) {
                    showAlert('Please select a valid PAN date of birth in DD/MM/YYYY format.', 'error');
                    showPanel(2);
                    return;
                }
                formData.set('pan_dob', parsedSubmittedDob.dmy);
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';
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
                        showAlert(data.message || 'Customer saved successfully!', 'success');
                        if (data.redirect) {
                            setTimeout(function () {
                                window.location.href = data.redirect;
                            }, 1200);
                        }
                    } else {
                        var msg = data.message || 'Submission failed. Please check your details.';
                        if (data.errors) {
                            msg = Object.values(data.errors).flat().join(' ');
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
                    submitBtn.innerHTML = 'SAVE CUSTOMER <i class="fas fa-check-circle ms-2"></i>';
                }
            }
        });

        /* ------------------------------------------------------------------
           Initial state
        ------------------------------------------------------------------ */
        updateProgressBar(1);
        updateCustomerTypeState();
        updateCsbState();
        updateKycSectionState();
    });
})();
