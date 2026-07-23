// ============================================================
// CSB5 Form - Upload, Verify & Submit Logic
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
// LUT document uploader (original widget, kept for reference)
// ------------------------------------------------------------
initDocUploader({
    container: '#lutDocContainer',
    input: '#lutFileInput',
    uploadBtn: '#uploadBtn',
    removeBtn: '#removeFile',
    fileInfo: '#fileInfo',
    nameDisplay: '#fileNameDisplay',
    onUpload: function () {
        // Enable the verify button once a file is uploaded
        const verifyBtn = document.getElementById('lutVerifyBtn');
        if (verifyBtn) {
            verifyBtn.disabled = false;
        }
    },
    onRemove: function () {
        // Disable verify + reset verified state when file removed
        const verifyBtn = document.getElementById('lutVerifyBtn');
        const verifiedBadge = document.getElementById('lutVerifiedBadge');
        const verifiedInput = document.getElementById('lutVerifiedInput');
        if (verifyBtn) {
            verifyBtn.disabled = true;
            verifyBtn.style.display = 'inline-block';
        }
        if (verifiedBadge) {
            verifiedBadge.style.display = 'none';
        }
        if (verifiedInput) {
            verifiedInput.value = '0';
        }
    },
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
// LUT Verify button logic
// ------------------------------------------------------------
const lutVerifyBtn = document.getElementById('lutVerifyBtn');
if (lutVerifyBtn) {
    lutVerifyBtn.addEventListener('click', function () {
        const lutFileInput = document.getElementById('lutFileInput');
        const verifiedBadge = document.getElementById('lutVerifiedBadge');
        const verifiedInput = document.getElementById('lutVerifiedInput');

        // Require a file before verifying
        if (!lutFileInput || lutFileInput.files.length === 0) {
            alert('Please upload the LUT document before verifying.');
            return;
        }

        const originalHTML = this.innerHTML;
        this.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> VERIFYING...';
        this.disabled = true;

        // Simulate a verification request (replace with real API call if available)
        setTimeout(() => {
            this.style.display = 'none';
            if (verifiedBadge) {
                verifiedBadge.style.display = 'inline-flex';
            }
            if (verifiedInput) {
                verifiedInput.value = '1';
            }
            this.innerHTML = originalHTML;
        }, 1200);
    });
}

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
            alert('Please enter a valid 12-digit Aadhaar number. It must not start with 0 or 1.');
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

    // Handle checkboxes - send as boolean
    formData.set('is_csb_v', document.getElementById('csbvToggle').checked ? '1' : '0');
    formData.set('is_gst', document.getElementById('gstType').checked ? '1' : '0');
    formData.set('is_lut', document.getElementById('lutType').checked ? '1' : '0');

    // LUT verified flag
    const lutVerifiedInput = document.getElementById('lutVerifiedInput');
    formData.set('lut_verified', lutVerifiedInput ? lutVerifiedInput.value : '0');

    // Basic validation checks
    // If LUT tax type is selected, require the document + verification
    if (document.getElementById('lutType').checked) {
        const lutFileInput = document.getElementById('lutFileInput');
        if (!lutFileInput || lutFileInput.files.length === 0) {
            alert('Please upload the LUT document before continuing.');
            return;
        }
        if (lutVerifiedInput && lutVerifiedInput.value !== '1') {
            alert('Please verify the LUT document before continuing.');
            return;
        }
    }

    // If GST tax type is selected, require the GST document
    if (document.getElementById('gstType').checked) {
        const gstFileInput = document.getElementById('gstFileInput');
        if (!gstFileInput || gstFileInput.files.length === 0) {
            alert('Please upload the GST document before continuing.');
            return;
        }
    }

    // ------------------------------------------------------------
    // New Business KYC field validations
    // ------------------------------------------------------------

    // Aadhaar number - required, 12 digits, valid format
    const businessAadharNumberEl = document.getElementById('businessAadharNumber');
    if (businessAadharNumberEl) {
        const aadharValue = businessAadharNumberEl.value.trim();
        const aadharRegex = /^[2-9][0-9]{11}$/;
        if (!aadharRegex.test(aadharValue)) {
            alert('Please enter a valid 12-digit Aadhaar number (must not start with 0 or 1).');
            return;
        }
    }

    // Aadhaar document - required
    const businessAadharFileInput = document.getElementById('businessAadharFileInput');
    if (!businessAadharFileInput || businessAadharFileInput.files.length === 0) {
        alert('Please upload the Aadhaar document before continuing.');
        return;
    }

    // Signature document - required
    const businessSignatureFileInput = document.getElementById('businessSignatureFileInput');
    if (!businessSignatureFileInput || businessSignatureFileInput.files.length === 0) {
        alert('Please upload the authorized signature document before continuing.');
        return;
    }

    // Billing address - required
    const billingAddressEl = form.querySelector('textarea[name="billing_address"]');
    if (billingAddressEl && billingAddressEl.value.trim() === '') {
        alert('Please enter the billing address.');
        return;
    }

    // Billing contact - required
    const billingContactEl = form.querySelector('input[name="billing_contact"]');
    if (billingContactEl && billingContactEl.value.trim() === '') {
        alert('Please enter the billing contact number.');
        return;
    }

    // Billing email - required + valid format
    const billingEmailEl = form.querySelector('input[name="billing_email"]');
    if (billingEmailEl) {
        const emailValue = billingEmailEl.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailValue === '' || !emailRegex.test(emailValue)) {
            alert('Please enter a valid billing email address.');
            return;
        }
    }

    // Merchant agreement - required PDF
    const businessMerchantAgreementFileInput = document.getElementById('businessMerchantAgreementFileInput');
    if (!businessMerchantAgreementFileInput || businessMerchantAgreementFileInput.files.length === 0) {
        alert('Please upload the signed merchant agreement before continuing.');
        return;
    }

    // Terms accepted - must be checked
    const businessTermsAccepted = document.getElementById('businessTermsAccepted');
    if (!businessTermsAccepted || !businessTermsAccepted.checked) {
        alert('Please accept the terms and conditions before continuing.');
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
                alert(errorMessage);
            } else {
                alert(data.message || 'An error occurred. Please try again.');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.innerHTML = 'CONTINUE';
        btn.style.opacity = '1';
        btn.style.pointerEvents = 'auto';
        alert('An error occurred. Please try again.');
    });
});
