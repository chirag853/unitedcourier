/**
 * exporter-customers.js
 * Handles the Exporter Customer KYC-style 4-step wizard:
 *   - Step navigation (next/prev) + progress bar
 *   - CSB IV/V + business customer type conditional toggles
 *   - LUT bond year/expiry sync
 *   - Cashfree Aadhaar (front + back), PAN & GST OCR verification with Step-3 autofill
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

        // Block "NEXT" navigation until all required information in the
        // current step (Details / KYC Document) has been provided. Step 2 is
        // driven by the customer type: Individual verifies Aadhaar + PAN,
        // Business verifies PAN + GST with Aadhaar optional.
        function validateStep(step) {
            if (step === 1) {
                if (!hasCustomerType()) {
                    showAlert('Please select a customer type before continuing to the next step.', 'error');
                    if (customerType) customerType.focus();
                    return false;
                }
                syncKycTypeFromCustomerType();
                return true;
            }

            if (step === 2) {
                if (!hasCustomerType()) {
                    showAlert('Please select a customer type in Step 1 before continuing.', 'error');
                    showPanel(1);
                    return false;
                }
                var isBusiness = isBusinessCustomerType();
                if (!isBusiness) {
                    // Individual => Aadhaar is mandatory.
                    if (!aadharNumber || !aadharNumber.value) {
                        showAlert('Please enter the Aadhaar number before continuing to the next step.', 'error');
                        if (aadharNumber) aadharNumber.focus();
                        return false;
                    }
                    if (!validateImageDocument(aadharFrontFileInput, true)) {
                        return false;
                    }
                    if (!validateImageDocument(aadharBackFileInput, true)) {
                        return false;
                    }
                    if (!aadharVerified) {
                        showAlert('Please verify the Aadhaar details and both Aadhaar images before continuing to the next step.', 'error');
                        if (aadharVerifyBtn) aadharVerifyBtn.focus();
                        return false;
                    }
                } else {
                    // Business => GST is mandatory.
                    if (!gstKycNumber || !gstKycNumber.value) {
                        showAlert('Please enter the GSTIN before continuing to the next step.', 'error');
                        if (gstKycNumber) gstKycNumber.focus();
                        return false;
                    }
                    if (!gstKycBusinessName || !gstKycBusinessName.value.trim()) {
                        showAlert('Please enter the registered business name before continuing to the next step.', 'error');
                        if (gstKycBusinessName) gstKycBusinessName.focus();
                        return false;
                    }
                    if (!gstKycFileInput || !gstKycFileInput.files || !gstKycFileInput.files[0]) {
                        showAlert('Please upload the GST certificate PDF before continuing to the next step.', 'error');
                        if (gstKycFileInput) gstKycFileInput.focus();
                        return false;
                    }
                    if (!gstKycVerified) {
                        showAlert('Please verify the GSTIN and Business Name before continuing to the next step.', 'error');
                        if (gstKycVerifyBtn) gstKycVerifyBtn.focus();
                        return false;
                    }
                }

                // PAN is mandatory for both Individual and Business customers.
                if (!panNumber || !panNumber.value) {
                    showAlert('Please enter the PAN number before continuing to the next step.', 'error');
                    if (panNumber) panNumber.focus();
                    return false;
                }
                if (!panHolderName || !panHolderName.value.trim()) {
                    showAlert('Please enter the name as on the PAN before continuing to the next step.', 'error');
                    if (panHolderName) panHolderName.focus();
                    return false;
                }
                if (!panDob || !panDob.value) {
                    showAlert('Please select the date of birth as on the PAN before continuing to the next step.', 'error');
                    if (panDob) panDob.focus();
                    return false;
                }
                if (!panFileInput || !panFileInput.files || !panFileInput.files[0]) {
                    showAlert('Please upload the PAN card document before continuing to the next step.', 'error');
                    if (panFileInput) panFileInput.focus();
                    return false;
                }
                if (!validateImageDocument(panFileInput, true)) {
                    return false;
                }
                if (!panVerified) {
                    showAlert('Please verify the PAN details and PAN card document before continuing to the next step.', 'error');
                    if (panVerifyBtn) panVerifyBtn.focus();
                    return false;
                }
                return true;
            }

            return true;
        }

        form.querySelectorAll('.wizard-next').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var next = parseInt(btn.getAttribute('data-next'), 10);
                if (next) {
                    var currentPanel = btn.closest('.wizard-panel');
                    var currentStep = currentPanel ? parseInt(currentPanel.getAttribute('data-panel'), 10) : 0;
                    if (!validateStep(currentStep)) {
                        return;
                    }
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
           Image upload validation (Aadhaar front/back + PAN document)
        ------------------------------------------------------------------ */
        var imageOnlyDocumentNames = ['aadhar_front_document', 'aadhar_back_document', 'pan_document'];

        function validateImageDocument(input, required) {
            var file = input && input.files ? input.files[0] : null;
            if (!file) {
                if (required) {
                    showAlert('Please upload the required document images.', 'error');
                    if (input) input.focus();
                    return false;
                }
                return true;
            }

            var extension = file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : '';
            if (['jpg', 'jpeg', 'png'].indexOf(extension) === -1 ||
                ['image/jpeg', 'image/png'].indexOf(file.type) === -1) {
                showAlert('Documents must be JPG, JPEG, or PNG images.', 'error');
                input.value = '';
                return false;
            }
            if (file.size > 5 * 1024 * 1024) {
                showAlert('Document images must not exceed 5 MB.', 'error');
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
        var kycType = document.getElementById('kycType');

        // Keep the Step-1 KYC Type dropdown in lockstep with the customer type.
        // syncKycTypeFromCustomerType() is declared below, but function
        // declarations are hoisted, so it is safe to wire it up here - as early
        // as possible - AND again at the document level via event delegation.
        // That way, even if an unrelated later error interrupts the rest of the
        // DOMContentLoaded wiring, selecting Individual can never leave
        // GST (Normal) in the KYC Type dropdown.
        if (customerType) {
            customerType.addEventListener('change', syncKycTypeFromCustomerType);
            document.addEventListener('change', function (e) {
                if (e.target === customerType && kycType) {
                    syncKycTypeFromCustomerType();
                }
            });
        }

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
        var isGst = document.getElementById('isGst');
        var gstFields = document.getElementById('gstFields');
        var gstNumber = document.getElementById('gstNumber');
        var gstBusinessName = document.getElementById('gstBusinessName');
        var gstFileInput = document.getElementById('gstFileInput');
        var gstVerifyStatus = document.getElementById('gstVerifyStatus');
        var verifyGstBtn = document.getElementById('verifyGstBtn');
        var gstVerified = false;
        // True when the account's GST was already Cashfree-verified in a previous
        // session (session keys set by customer.verify.gst / CSB5) and is being reused.
        var gstReusable = gstFields ? gstFields.dataset.gstReusable === '1' : false;
        // True when Step 2 verified GST as the KYC document and CSB V reuses it in
        // Step 4 (auto-tick, readonly prefill, "Verified via KYC", same certificate).
        var gstFromKyc = false;
        var gstVerifiedBadge = document.getElementById('gstVerifiedBadge');

        function isCsbVSelected() {
            return csbTypeCheck ? csbTypeCheck.checked : (csbType && csbType.value === 'csb_v');
        }

        function hasCustomerType() {
            return !!(customerType && customerType.value);
        }

        function isBusinessCustomerType() {
            var selectedOption = customerType && customerType.options[customerType.selectedIndex];
            return !!(selectedOption && selectedOption.value && selectedOption.dataset.userType === 'business');
        }

        function syncKycTypeFromCustomerType() {
            if (!kycType) {
                return;
            }

            function sameOptionSet(desiredValues) {
                if (!kycType.options || kycType.options.length !== desiredValues.length) {
                    return false;
                }
                for (var i = 0; i < kycType.options.length; i++) {
                    if (kycType.options[i].value !== desiredValues[i]) {
                        return false;
                    }
                }
                return true;
            }

            function rebuildOptions(options) {
                var desiredValues = [];
                var defaultOption = null;
                options.forEach(function (o) {
                    desiredValues.push(o.value);
                    if (o.selected) {
                        defaultOption = o;
                    }
                });

                var previousValue = kycType.value;

                // Keep a valid, user-selected value when the option set already
                // matches the desired set (idempotent with the inline blade sync).
                if (sameOptionSet(desiredValues)) {
                    if (previousValue && desiredValues.indexOf(previousValue) !== -1) {
                        return;
                    }
                    if (defaultOption) {
                        kycType.value = defaultOption.value;
                    }
                    return;
                }

                kycType.innerHTML = '';
                var preserveSelection = previousValue && desiredValues.indexOf(previousValue) !== -1;
                options.forEach(function (o) {
                    var option = document.createElement('option');
                    option.value = o.value;
                    option.textContent = o.label;
                    option.selected = preserveSelection ? o.value === previousValue : !!o.selected;
                    kycType.appendChild(option);
                });
                if (kycType.value === '' && defaultOption) {
                    kycType.value = defaultOption.value;
                }
            }

            if (!hasCustomerType()) {
                // No customer type chosen yet - keep the options in a neutral
                // state so a server-rendered old() value stays selectable after a
                // validation error.
                rebuildOptions([
                    { value: '', label: 'Select KYC Type' },
                    { value: 'Aadhar Card', label: 'Aadhar Card' },
                    { value: 'GST (Normal)', label: 'GST (Normal)' }
                ]);
                return;
            }
            if (isBusinessCustomerType()) {
                // Business => "PAN Card" and "GST (Normal)" (GST stays the default).
                // The choice does not affect Step 2 - a Business customer always
                // verifies PAN + GST; it only records which document is primary.
                rebuildOptions([
                    { value: 'PAN Card', label: 'PAN Card' },
                    { value: 'GST (Normal)', label: 'GST (Normal)', selected: true }
                ]);
            } else {
                // Individual => the dropdown contains ONLY "Aadhar Card".
                rebuildOptions([
                    { value: 'Aadhar Card', label: 'Aadhar Card', selected: true }
                ]);
            }
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
            lutFields.classList.toggle('d-none', !enabled);
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

        function updateGstState() {
            // A verified Step-2 GST KYC auto-ticks GST in Step 4 and reuses the
            // GSTIN + business name + certificate (badge "Verified via KYC").
            syncGstFromKyc();

            var csbVEnabled = isCsbVSelected();
            var enabled = csbVEnabled && isGst && isGst.checked;
            gstFields.classList.toggle('d-none', !enabled);
            gstFields.querySelectorAll('input, button').forEach(function (field) {
                // In reusable mode (account session OR Step-2 GST KYC) the GSTIN +
                // business name stay read-only (never disabled) while GST is selected,
                // so their prefilled values are still submitted for the server-side
                // check against the verified session GST. Once GST is unselected they
                // are disabled like the rest of the block.
                if ((gstReusable || gstFromKyc) && enabled && (field === gstNumber || field === gstBusinessName)) {
                    field.disabled = false;
                    return;
                }
                field.disabled = !enabled;
            });
            if (gstNumber) { gstNumber.required = enabled; }
            if (gstBusinessName) { gstBusinessName.required = enabled; }
            if (gstFileInput) { gstFileInput.required = enabled; }
            if (!enabled && !gstReusable && !gstFromKyc) {
                gstVerified = false;
            }
        }

        function copyFileToInput(sourceInput, targetInput) {
            if (!sourceInput || !targetInput || !sourceInput.files || !sourceInput.files[0]) {
                return;
            }
            if (targetInput.files && targetInput.files[0]) {
                return;
            }
            try {
                var dt = new DataTransfer();
                dt.items.add(sourceInput.files[0]);
                targetInput.files = dt.files;
            } catch (e) {
                return;
            }
            var nameDisplay = document.getElementById('gstFileNameDisplay');
            var fileInfo = document.getElementById('gstFileInfo');
            if (nameDisplay && targetInput.files[0]) {
                nameDisplay.textContent = targetInput.files[0].name;
            }
            if (fileInfo) { fileInfo.style.display = 'block'; }
            var removeEl = document.querySelector('.gstRemoveFile');
            var uploadBtn = document.querySelector('.gstUploadBtn');
            if (removeEl) { removeEl.style.display = 'inline-block'; }
            if (uploadBtn) { uploadBtn.style.display = 'none'; }
        }

        // Reuse the Step-2 verified GST inside Step 4 (CSB V): auto-tick GST, prefill
        // the read-only GSTIN + business name, reuse the same certificate file and
        // mark it as verified so VERIFY GST is skipped. The user may still untick GST
        // afterwards to choose LUT instead.
        function syncGstFromKyc() {
            if (!isCsbVSelected() || !isBusinessCustomerType()) {
                return;
            }
            if (!gstKycVerified) {
                return;
            }
            if (!isGst || !gstFields) {
                return;
            }
            var wasFromKyc = gstFromKyc;
            gstFromKyc = true;
            if (!wasFromKyc && !isGst.checked) {
                isGst.checked = true;
            }
            if (gstNumber && gstKycNumber && gstKycNumber.value) {
                gstNumber.value = gstKycNumber.value.toUpperCase();
                gstNumber.setAttribute('readonly', 'readonly');
            }
            if (gstBusinessName && gstKycBusinessName && gstKycBusinessName.value) {
                gstBusinessName.value = gstKycBusinessName.value;
                gstBusinessName.setAttribute('readonly', 'readonly');
            }
            copyFileToInput(gstKycFileInput, gstFileInput);
            gstVerified = true;
            if (verifyGstBtn) { verifyGstBtn.style.display = 'none'; }
            if (gstVerifiedBadge) {
                gstVerifiedBadge.style.display = 'inline-block';
                gstVerifiedBadge.innerHTML = '<i class="fas fa-circle-check me-1"></i> Verified via KYC';
            }
            showKycVerifyStatus(gstVerifyStatus, 'GST verified via Step 2 KYC. The same GSTIN, Business Name and certificate will be reused.', 'success');
        }

        function clearStep4GstKycReuse() {
            if (!gstFromKyc) {
                return;
            }
            gstFromKyc = false;
            gstVerified = false;
            if (verifyGstBtn) {
                verifyGstBtn.style.display = '';
                verifyGstBtn.disabled = false;
            }
            if (gstVerifiedBadge) {
                gstVerifiedBadge.style.display = 'none';
                gstVerifiedBadge.innerHTML = '<i class="fas fa-circle-check me-1"></i> Verified';
            }
            if (gstVerifyStatus) { gstVerifyStatus.style.display = 'none'; }
        }

        function updateCustomerTypeState() {
            var selectedOption = customerType && customerType.options
                ? customerType.options[customerType.selectedIndex]
                : null;
            var isBusiness = selectedOption && selectedOption.dataset.userType === 'business';
            syncKycTypeFromCustomerType();

            // Both business and personal customers can freely choose CSB IV or CSB V.
            if (csbTypeCheck) {
                csbTypeCheck.disabled = false;
            }
            if (csbTypeHint) {
                csbTypeHint.textContent = isBusiness
                    ? 'Business customers can select CSB IV or CSB V.'
                    : 'Personal customers can select CSB IV or CSB V.';
            }

            // Business => PAN + GST (Aadhaar optional); Individual => Aadhaar + PAN.
            // The customer type drives the Step-2 sections, so refresh them, reset
            // any stale verification, and drop any Step-4 GST reuse from Step-2.
            clearStep4GstKycReuse();
            updateKycSectionState();
            resetVerificationState();

            updateCsbState();
        }

        function updateCsbState() {
            var enabled = isCsbVSelected();
            if (csbType) {
                csbType.value = enabled ? 'csb_v' : 'csb_iv';
            }
            if (csbVFields) {
                csbVFields.classList.toggle('d-none', !enabled);
                csbVFields.setAttribute('aria-hidden', enabled ? 'false' : 'true');
                csbVFields.querySelectorAll('input, select, textarea').forEach(function (field) {
                    field.disabled = !enabled;
                });
                csbVFields.querySelectorAll('[data-csb-v-required]').forEach(function (field) {
                    field.required = enabled;
                });
            }
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
            updateGstState();
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
        if (isGst) {
            isGst.addEventListener('change', updateGstState);
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
           KYC sections (Aadhaar / PAN / GST) - driven by customer type
        ------------------------------------------------------------------ */
        var aadharKycSection = document.getElementById('aadharKycSection');
        var panKycSection = document.getElementById('panKycSection');
        var gstKycSection = document.getElementById('gstKycSection');

        var aadharVerifyBtn = document.getElementById('aadharVerifyBtn');
        var aadharVerifiedBadge = document.getElementById('aadharVerifiedBadge');
        var aadharVerifyStatus = document.getElementById('aadharVerifyStatus');
        var aadharNumber = document.getElementById('aadharNumber');
        var aadharFrontFileInput = document.getElementById('aadharFrontFileInput');
        var aadharBackFileInput = document.getElementById('aadharBackFileInput');
        var aadharRequirementBadge = document.getElementById('aadharRequirementBadge');

        var panVerifyBtn = document.getElementById('panVerifyBtn');
        var panVerifiedBadge = document.getElementById('panVerifiedBadge');
        var panVerifyStatus = document.getElementById('panVerifyStatus');
        var panNumber = document.getElementById('panNumber');
        var panHolderName = document.getElementById('panHolderName');
        var panDob = document.getElementById('panDob');
        var panFileInput = document.getElementById('panFileInput');

        var gstKycVerifyBtn = document.getElementById('gstKycVerifyBtn');
        var gstKycVerifiedBadge = document.getElementById('gstKycVerifiedBadge');
        var gstKycVerifyStatus = document.getElementById('gstKycVerifyStatus');
        var gstKycNumber = document.getElementById('gstKycNumber');
        var gstKycBusinessName = document.getElementById('gstKycBusinessName');
        var gstKycFileInput = document.getElementById('gstKycFileInput');

        var aadharVerified = false;
        var panVerified = false;
        var gstKycVerified = false;

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
            ['companyName', 'contactPerson', 'addressLine1', 'pincode', 'city', 'state'].forEach(function (id) {
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

        // Step-2 GST verification returns gst_number/business_name at the top level
        // (no autofill object) plus an optional single combined address string, so
        // Company Name and Address Line 1 are filled here.
        function applyGstKycAutofill(data) {
            if (!data) {
                return;
            }
            setAutofillValue('companyName', data.business_name);
            if (data.address) {
                setAutofillValue('addressLine1', data.address);
            }
        }

        // Step-2 PAN verification returns { name, dob } for the PAN holder. The
        // DOB fills the PAN date-of-birth input; the holder name fills Contact
        // Person in Step 3 only when it is still empty (Aadhaar autofill keeps
        // priority for Individual customers).
        function applyPanAutofill(autofill) {
            if (!autofill) {
                return;
            }
            if (autofill.dob && panDob) {
                panDob.value = autofill.dob;
            }
            var contactEl = document.getElementById('contactPerson');
            if (!contactEl || !contactEl.value) {
                setAutofillValue('contactPerson', autofill.name);
            }
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
            if (panFileInput) { panFileInput.disabled = false; }

            gstKycVerified = false;
            if (gstKycVerifyBtn) {
                gstKycVerifyBtn.style.display = '';
                gstKycVerifyBtn.disabled = false;
                gstKycVerifyBtn.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Verify GST';
            }
            if (gstKycVerifiedBadge) { gstKycVerifiedBadge.style.display = 'none'; }
            if (gstKycVerifyStatus) {
                gstKycVerifyStatus.style.display = 'none';
                gstKycVerifyStatus.className = 'kyc-alert';
            }
            if (gstKycNumber) { gstKycNumber.removeAttribute('readonly'); }
            if (gstKycBusinessName) { gstKycBusinessName.removeAttribute('readonly'); }
            if (gstKycFileInput) { gstKycFileInput.disabled = false; }

            clearAutofill();
        }

        // Aadhaar: shown for both types but mandatory only for Individual.
        // PAN: mandatory for both. GST: mandatory for Business only.
        function updateKycSectionState() {
            var hasType = hasCustomerType();
            var isBusiness = isBusinessCustomerType();
            var showAadhar = hasType;
            var showPan = hasType;
            var showGst = isBusiness;
            var aadharRequired = hasType && !isBusiness;

            if (aadharKycSection) { aadharKycSection.classList.toggle('d-none', !showAadhar); }
            if (panKycSection) { panKycSection.classList.toggle('d-none', !showPan); }
            if (gstKycSection) { gstKycSection.classList.toggle('d-none', !showGst); }

            if (aadharNumber) {
                aadharNumber.required = aadharRequired;
                aadharNumber.placeholder = aadharRequired
                    ? 'Enter 12-digit Aadhaar Number *'
                    : 'Enter 12-digit Aadhaar Number';
            }
            if (aadharFrontFileInput) { aadharFrontFileInput.required = aadharRequired; }
            if (aadharBackFileInput) { aadharBackFileInput.required = aadharRequired; }

            if (panNumber) { panNumber.required = showPan; }
            if (panHolderName) { panHolderName.required = showPan; }
            if (panDob) { panDob.required = showPan; }
            if (panFileInput) { panFileInput.required = showPan; }

            if (gstKycNumber) { gstKycNumber.required = showGst; }
            if (gstKycBusinessName) { gstKycBusinessName.required = showGst; }
            if (gstKycFileInput) { gstKycFileInput.required = showGst; }

            // Aadhaar is mandatory for Individual but optional for Business - show a
            // visible "Optional" badge only when the Business section is displayed.
            if (aadharRequirementBadge) {
                aadharRequirementBadge.style.display = (showAadhar && isBusiness) ? '' : 'none';
            }
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
           PAN verification (Cashfree OCR) - number + holder name + DOB + image
        ------------------------------------------------------------------ */
        if (panVerifyBtn) {
            panVerifyBtn.addEventListener('click', function () {
                if (!panNumber || !panNumber.value) {
                    showAlert('Please enter the PAN number first.', 'error');
                    return;
                }
                var pan = panNumber.value.toUpperCase().replace(/\s+/g, '');
                if (!/^[A-Z]{5}[0-9]{4}[A-Z]$/.test(pan)) {
                    showAlert('Invalid PAN number. It must be 10 characters (e.g. ABCDE1234F).', 'error');
                    return;
                }
                if (!panHolderName || !panHolderName.value.trim()) {
                    showAlert('Please enter the name as on the PAN before verification.', 'error');
                    return;
                }
                if (!panDob || !panDob.value) {
                    showAlert('Please select the date of birth as on the PAN before verification.', 'error');
                    return;
                }
                if (!panFileInput || !panFileInput.files || !panFileInput.files[0]) {
                    showAlert('Please upload the PAN card document before verification.', 'error');
                    return;
                }
                if (!validateImageDocument(panFileInput, true)) {
                    return;
                }

                var verifyData = new FormData();
                verifyData.append('pan_number', pan);
                verifyData.append('pan_holder_name', panHolderName.value.trim());
                verifyData.append('pan_dob', panDob.value);
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
                            if (data.pan_number && panNumber) { panNumber.value = data.pan_number.toUpperCase(); }
                            if (panNumber) { panNumber.setAttribute('readonly', 'readonly'); }
                            var autofill = data.autofill || {};
                            if (autofill.name && panHolderName) { panHolderName.value = autofill.name; }
                            if (autofill.dob && panDob) { panDob.value = autofill.dob; }
                            if (panHolderName) { panHolderName.setAttribute('readonly', 'readonly'); }
                            if (panFileInput) { panFileInput.disabled = false; }
                            showKycVerifyStatus(panVerifyStatus, data.message || 'PAN verified successfully!', 'success');
                            applyPanAutofill(autofill);
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
           Step 2 GST KYC verification (Cashfree) - GSTIN + business name.
           Response: { success, message, gst_number, business_name, [address] }.
        ------------------------------------------------------------------ */
        if (gstKycVerifyBtn) {
            gstKycVerifyBtn.addEventListener('click', function () {
                if (!gstKycNumber || !gstKycNumber.value) {
                    showAlert('Please enter the GSTIN first.', 'error');
                    return;
                }
                var gstin = gstKycNumber.value.toUpperCase().replace(/\s+/g, '');
                if (!/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/.test(gstin)) {
                    showAlert('Invalid GSTIN format. It must be a 15-character GSTIN (e.g. 22AAAAA0000A1Z5).', 'error');
                    return;
                }
                if (!gstKycBusinessName || !gstKycBusinessName.value.trim()) {
                    showAlert('Please enter the registered business name before verification.', 'error');
                    return;
                }
                if (!gstKycFileInput || !gstKycFileInput.files || !gstKycFileInput.files[0]) {
                    showAlert('Please upload the GST certificate PDF before verification.', 'error');
                    return;
                }

                var verifyData = new FormData();
                verifyData.append('gst_number', gstin);
                verifyData.append('business_name', gstKycBusinessName.value.trim());
                verifyData.append('gst_certificate_document', gstKycFileInput.files[0]);

                gstKycVerified = false;
                gstKycVerifyBtn.disabled = true;
                gstKycVerifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Verifying...';

                fetch(form.getAttribute('data-verify-gst-url'), {
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
                            gstKycVerified = true;
                            gstKycVerifyBtn.style.display = 'none';
                            if (gstKycVerifiedBadge) { gstKycVerifiedBadge.style.display = 'inline-block'; }
                            if (data.gst_number && gstKycNumber) { gstKycNumber.value = data.gst_number.toUpperCase(); }
                            if (gstKycNumber) { gstKycNumber.setAttribute('readonly', 'readonly'); }
                            if (data.business_name && gstKycBusinessName) { gstKycBusinessName.value = data.business_name; }
                            if (gstKycBusinessName) { gstKycBusinessName.setAttribute('readonly', 'readonly'); }
                            if (gstKycFileInput) { gstKycFileInput.disabled = false; }
                            showKycVerifyStatus(gstKycVerifyStatus, data.message || 'GST verified successfully!', 'success');
                            applyGstKycAutofill(data);
                            // When CSB V is selected, reuse this verified GST in Step 4
                            // (auto-tick GST, prefill GSTIN + business name, skip VERIFY).
                            syncGstFromKyc();
                        } else {
                            var message = data.message || 'GST verification failed.';
                            if (data.errors) {
                                message = Object.values(data.errors).flat().join(' ');
                            }
                            showKycVerifyStatus(gstKycVerifyStatus, message, 'error');
                            gstKycVerifyBtn.disabled = false;
                            gstKycVerifyBtn.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Verify GST';
                        }
                    })
                    .catch(function () {
                        gstKycVerified = false;
                        gstKycVerifyBtn.disabled = false;
                        gstKycVerifyBtn.innerHTML = '<i class="fas fa-shield-halved me-1"></i> Verify GST';
                        showKycVerifyStatus(gstKycVerifyStatus, 'GST verification could not be completed. Please check your connection and try again.', 'error');
                    });
            });

            ['gstKycNumber', 'gstKycBusinessName', 'gstKycFileInput'].forEach(function (id) {
                var input = document.getElementById(id);
                if (input) {
                    input.addEventListener('change', function () {
                        if (!gstKycVerified) {
                            return;
                        }
                        gstKycVerified = false;
                        if (gstKycVerifyBtn) {
                            gstKycVerifyBtn.style.display = '';
                            gstKycVerifyBtn.disabled = false;
                        }
                        if (gstKycVerifiedBadge) { gstKycVerifiedBadge.style.display = 'none'; }
                        if (gstKycVerifyStatus) { gstKycVerifyStatus.style.display = 'none'; }
                    });
                }
            });
        }

        /* ------------------------------------------------------------------
           GST verification (Cashfree) - GSTIN + business name must match
        ------------------------------------------------------------------ */
        if (verifyGstBtn) {
            verifyGstBtn.addEventListener('click', function () {
                // Never re-verify when the previously-verified GST is being reused.
                if (gstReusable) {
                    return;
                }
                if (!gstNumber || !gstNumber.value) {
                    showAlert('Please enter the GSTIN first.', 'error');
                    return;
                }
                var gstin = gstNumber.value.toUpperCase().replace(/\s+/g, '');
                if (!/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/.test(gstin)) {
                    showAlert('Invalid GSTIN format. It must be a 15-character GSTIN (e.g. 22AAAAA0000A1Z5).', 'error');
                    return;
                }
                if (!gstBusinessName || !gstBusinessName.value.trim()) {
                    showAlert('Please enter the registered business name before verification.', 'error');
                    return;
                }
                if (!gstFileInput || !gstFileInput.files[0]) {
                    showAlert('Please upload the GST certificate PDF before verification.', 'error');
                    return;
                }

                var verifyData = new FormData();
                verifyData.append('gst_number', gstin);
                verifyData.append('business_name', gstBusinessName.value.trim());
                verifyData.append('gst_certificate_document', gstFileInput.files[0]);

                gstVerified = false;
                verifyGstBtn.disabled = true;
                verifyGstBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Verifying...';

                fetch(form.getAttribute('data-verify-gst-url'), {
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
                            gstVerified = true;
                            verifyGstBtn.style.display = 'none';
                            if (data.gst_number && gstNumber) { gstNumber.value = data.gst_number; }
                            if (data.business_name && gstBusinessName) { gstBusinessName.value = data.business_name; }
                            if (gstNumber) { gstNumber.setAttribute('readonly', 'readonly'); }
                            if (gstBusinessName) { gstBusinessName.setAttribute('readonly', 'readonly'); }
                            if (gstFileInput) { gstFileInput.disabled = false; }
                            showKycVerifyStatus(gstVerifyStatus, data.message || 'GST verified successfully!', 'success');
                        } else {
                            var message = data.message || 'GST verification failed.';
                            if (data.errors) {
                                message = Object.values(data.errors).flat().join(' ');
                            }
                            showKycVerifyStatus(gstVerifyStatus, message, 'error');
                            verifyGstBtn.disabled = false;
                            verifyGstBtn.innerHTML = '<i class="fas fa-shield-alt me-1"></i> VERIFY GST';
                        }
                    })
                    .catch(function () {
                        gstVerified = false;
                        verifyGstBtn.disabled = false;
                        verifyGstBtn.innerHTML = '<i class="fas fa-shield-alt me-1"></i> VERIFY GST';
                        showKycVerifyStatus(gstVerifyStatus, 'GST verification could not be completed. Please check your connection and try again.', 'error');
                    });
            });

            ['gstNumber', 'gstBusinessName', 'gstFileInput'].forEach(function (id) {
                var input = document.getElementById(id);
                if (input) {
                    input.addEventListener('change', function () {
                        // In reusable mode (account session OR Step-2 GST KYC) the
                        // GSTIN/business name are read-only and the certificate upload
                        // must NOT reset the previously-verified state (there is no
                        // VERIFY GST button in this mode).
                        if (gstReusable || gstFromKyc) {
                            return;
                        }
                        if (!gstVerified) {
                            return;
                        }
                        gstVerified = false;
                        if (verifyGstBtn) {
                            verifyGstBtn.style.display = '';
                            verifyGstBtn.disabled = false;
                        }
                        if (gstVerifyStatus) { gstVerifyStatus.style.display = 'none'; }
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

            var isBusiness = isBusinessCustomerType();

            if (!isBusiness) {
                // Individual => Aadhaar + PAN are mandatory.
                if (!aadharVerified) {
                    showAlert('Please verify the Aadhaar details and both Aadhaar images before saving the customer.', 'error');
                    showPanel(2);
                    return;
                }
            } else {
                // Business => PAN + GST are mandatory (Aadhaar optional).
                if (!gstKycVerified) {
                    showAlert('Please verify the GSTIN and Business Name in Step 2 before saving the customer.', 'error');
                    showPanel(2);
                    return;
                }
            }

            if (!panVerified) {
                showAlert('Please verify the PAN details and PAN card document before saving the customer.', 'error');
                showPanel(2);
                return;
            }

            if (isCsbVSelected()) {
                var gstEnabled = isGst && isGst.checked;
                var lutEnabled = isLut && isLut.checked;
                if (!gstEnabled && !lutEnabled) {
                    showAlert('Select GST, LUT, or both. At least one option is required for CSB V customers.', 'error');
                    showPanel(4);
                    return;
                }
                if (gstEnabled && !gstVerified) {
                    showAlert('Verify the GSTIN and Business Name through Cashfree before continuing.', 'error');
                    showPanel(4);
                    return;
                }
            }

            var requiredDocs = ['pan_document'];
            if (!isBusiness) {
                // Individual customers must also upload both Aadhaar images.
                requiredDocs = ['aadhar_front_document', 'aadhar_back_document', 'pan_document'];
            }
            // GST KYC uses a PDF certificate - presence is enforced during the Step 2
            // verification, so no image-only validation is applied here.

            for (var i = 0; i < requiredDocs.length; i++) {
                var imageInput = form.querySelector('[name="' + requiredDocs[i] + '"]');
                if (!validateImageDocument(imageInput, true)) {
                    showPanel(2);
                    return;
                }
            }

            var formData = new FormData(form);
            // The backend derives kyc_number from aadhar_number/gst_kyc_number - never send it directly.
            if (formData.has('kyc_number')) {
                formData.delete('kyc_number');
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

        // Reusable GST: the account's GST was already Cashfree-verified previously, so
        // present it as verified (read-only GSTIN/business name, Verified badge, no
        // VERIFY GST button). Saving is allowed because the submitted GSTIN is checked
        // server-side against the verified session value.
        if (gstReusable) {
            gstVerified = true;
            if (verifyGstBtn) { verifyGstBtn.style.display = 'none'; }
            if (gstVerifiedBadge) { gstVerifiedBadge.style.display = 'inline-block'; }
            showKycVerifyStatus(gstVerifyStatus, 'GST verified previously. Re-verify only if you change the GSTIN or Business Name.', 'success');
            if (gstNumber) { gstNumber.setAttribute('readonly', 'readonly'); }
            if (gstBusinessName) { gstBusinessName.setAttribute('readonly', 'readonly'); }
            if (gstFileInput) {
                gstFileInput.disabled = false;
                gstFileInput.required = true;
            }
        }
    });
})();
