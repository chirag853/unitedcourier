// ============================================================
// CSB5 Form - Upload & Submit Logic
// ============================================================

/**
 * Generic helper to wire up a document upload/remove widget.
 *
 * @param {Object} cfg
 * @param {string} cfg.container   Selector for the .doc-item container
 * @param {string} cfg.input       Selector for the hidden <input type="file">
 * @param {string} cfg.uploadBtn   Selector for the upload trigger button
 * @param {string} cfg.removeBtn   Selector for the remove button
 * @param {string} cfg.fileInfo    Selector for the file-status wrapper
 * @param {string} cfg.nameDisplay Selector for the filename display span
 * @param {Function} [cfg.onUpload]  Optional callback after a file is selected
 * @param {Function} [cfg.onRemove]  Optional callback after a file is removed
 */
function initDocUploader(cfg) {
    const container = document.querySelector(cfg.container);
    if (!container) {
        console.warn('[CSB5] initDocUploader: container not found for', cfg.container);
        return;
    }

    // Scope all queries to the container for robustness
    const input = container.querySelector(cfg.input);
    const uploadBtn = container.querySelector(cfg.uploadBtn);
    const removeBtn = container.querySelector(cfg.removeBtn);
    const fileInfo = container.querySelector(cfg.fileInfo);
    const nameDisplay = container.querySelector(cfg.nameDisplay);

    if (!input || !uploadBtn) {
        console.warn('[CSB5] initDocUploader: input or uploadBtn not found in', cfg.container, { hasInput: !!input, hasUploadBtn: !!uploadBtn });
        return;
    }

    uploadBtn.addEventListener('click', () => {
        input.click();
    });

    input.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            const name = e.target.files[0].name;
            if (nameDisplay) {
                nameDisplay.textContent = name;
            }
            if (fileInfo) {
                fileInfo.style.display = 'block';
            }
            if (removeBtn) {
                removeBtn.style.display = 'inline-block';
            }
            uploadBtn.style.display = 'none';
            container.classList.add('has-file');
            if (typeof cfg.onUpload === 'function') {
                cfg.onUpload(e.target.files[0]);
            }
        }
    });

    if (removeBtn) {
        removeBtn.addEventListener('click', () => {
            input.value = '';
            if (fileInfo) {
                fileInfo.style.display = 'none';
            }
            removeBtn.style.display = 'none';
            uploadBtn.style.display = 'inline-block';
            container.classList.remove('has-file');
            if (typeof cfg.onRemove === 'function') {
                cfg.onRemove();
            }
        });
    }
}

// ------------------------------------------------------------
// LUT document uploader
// ------------------------------------------------------------
initDocUploader({
    container: '#lutDocContainer',
    input: '#lutFileInput',
    uploadBtn: '#uploadBtn',
    removeBtn: '#removeFile',
    fileInfo: '#fileInfo',
    nameDisplay: '#fileNameDisplay',
});

// ------------------------------------------------------------
// GST document uploader
// ------------------------------------------------------------
initDocUploader({
    container: '#gstDocContainer',
    input: '#gstFileInput',
    uploadBtn: '.gstUploadBtn',
    removeBtn: '.gstRemoveFile',
    fileInfo: '#gstFileInfo',
    nameDisplay: '#gstFileNameDisplay',
});

// ------------------------------------------------------------
// GST certificate document uploader
// ------------------------------------------------------------
initDocUploader({
    container: '#gstCertDocContainer',
    input: '#gstCertFileInput',
    uploadBtn: '.gstCertUploadBtn',
    removeBtn: '.gstCertRemoveFile',
    fileInfo: '#gstCertFileInfo',
    nameDisplay: '#gstCertFileNameDisplay',
});

// ------------------------------------------------------------
// IEC document uploader
// ------------------------------------------------------------
initDocUploader({
    container: '#iecDocContainer',
    input: '#iecFileInput',
    uploadBtn: '.iecUploadBtn',
    removeBtn: '.iecRemoveFile',
    fileInfo: '#iecFileInfo',
    nameDisplay: '#iecFileNameDisplay',
});

// ------------------------------------------------------------
// Aadhaar input formatting (digits only, max 12)
// ------------------------------------------------------------
const businessAadharNumber = document.getElementById('businessAadharNumber');
if (businessAadharNumber) {
    businessAadharNumber.addEventListener('input', function () {
        // Strip non-digits and cap at 12 characters
        this.value = this.value.replace(/\D/g, '').slice(0, 12);
    });
}

// ------------------------------------------------------------
// Aadhaar Verify button logic (Business KYC)
// ------------------------------------------------------------
const businessAadharVerifyBtn = document.getElementById('businessAadharVerifyBtn');
if (businessAadharVerifyBtn) {
    businessAadharVerifyBtn.addEventListener('click', function () {
        const aadharInput = document.getElementById('businessAadharNumber');
        const verifiedBadge = document.getElementById('businessAadharVerifiedBadge');

        if (!aadharInput) {
            return;
        }

        const value = aadharInput.value.trim();

        // Aadhaar format: 12 digits, must not start with 0 or 1
        const aadharRegex = /^[2-9][0-9]{11}$/;
        if (!aadharRegex.test(value)) {
            showAlert('Please enter a valid 12-digit Aadhaar number. It must not start with 0 or 1.', 'warning');
            return;
        }

        const originalHTML = this.innerHTML;
        this.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> VERIFYING...';
        this.disabled = true;

        // Simulate a verification request (no real Aadhaar API integrated)
        setTimeout(() => {
            this.style.display = 'none';
            if (verifiedBadge) {
                verifiedBadge.style.display = 'inline-flex';
            }
            this.innerHTML = originalHTML;
        }, 1200);
    });
}

// ------------------------------------------------------------
// Form Submit
// ------------------------------------------------------------
document.getElementById('csbvForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = this.querySelector('.btn-gradient');
    const form = this;

    // Prepare form data
    const formData = new FormData(form);

    // CSB-V is fixed for this form; GST has been removed.
    formData.set('is_csb_v', '1');
    formData.set('is_gst', '0');
    formData.set('is_lut', document.getElementById('lutType').checked ? '1' : '0');
    formData.set('lut_verified', '0');

    // Basic validation checks
    // If LUT tax type is selected, require the document.
    if (document.getElementById('lutType').checked) {
        const lutFileInput = document.getElementById('lutFileInput');
        if (!lutFileInput || lutFileInput.files.length === 0) {
            showAlert('Please upload the LUT document before continuing.', 'warning');
            return;
        }
    }

    // ------------------------------------------------------------
    // New Business KYC field validations
    // ------------------------------------------------------------

    // Signature document - required
    const businessSignatureFileInput = document.getElementById('businessSignatureFileInput');
    if (!businessSignatureFileInput || businessSignatureFileInput.files.length === 0) {
        showAlert('Please upload the authorized signature document before continuing.', 'warning');
        return;
    }

    // Billing address - required
    const billingAddressEl = form.querySelector('textarea[name="billing_address"]');
    if (billingAddressEl && billingAddressEl.value.trim() === '') {
        showAlert('Please enter the billing address.', 'warning');
        return;
    }

    // Billing contact - required
    const billingContactEl = form.querySelector('input[name="billing_contact"]');
    if (billingContactEl && billingContactEl.value.trim() === '') {
        showAlert('Please enter the billing contact number.', 'warning');
        return;
    }

    // Billing email - required + valid format
    const billingEmailEl = form.querySelector('input[name="billing_email"]');
    if (billingEmailEl) {
        const emailValue = billingEmailEl.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailValue === '' || !emailRegex.test(emailValue)) {
            showAlert('Please enter a valid billing email address.', 'warning');
            return;
        }
    }

    // Merchant agreement - required PDF
    const businessMerchantAgreementFileInput = document.getElementById('businessMerchantAgreementFileInput');
    if (!businessMerchantAgreementFileInput || businessMerchantAgreementFileInput.files.length === 0) {
        showAlert('Please upload the signed merchant agreement before continuing.', 'warning');
        return;
    }

    // Terms accepted - must be checked
    const businessTermsAccepted = document.getElementById('businessTermsAccepted');
    if (!businessTermsAccepted || !businessTermsAccepted.checked) {
        showAlert('Please accept the terms and conditions before continuing.', 'warning');
        return;
    }

    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> SUBMITTING...';
    btn.style.opacity = '0.8';
    btn.style.pointerEvents = 'none';

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = 'SUCCESS';
            btn.style.background = '#10b981';
            btn.style.opacity = '1';

            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            btn.innerHTML = 'CONTINUE';
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';

            if (data.errors) {
                let errorMessage = 'Please fix the following errors:\n';
                for (const [key, value] of Object.entries(data.errors)) {
                    errorMessage += `- ${value[0]}\n`;
                }
                showAlert(errorMessage, 'error');
            } else {
                showAlert(data.message || 'An error occurred. Please try again.', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.innerHTML = 'CONTINUE';
        btn.style.opacity = '1';
        btn.style.pointerEvents = 'auto';
        showAlert('An error occurred. Please try again.', 'error');
    });
});
