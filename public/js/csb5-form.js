// Upload Logic
const uploadBtn = document.getElementById('uploadBtn');
const fileInput = document.getElementById('lutFileInput');
const fileInfo = document.getElementById('fileInfo');
const fileNameDisplay = document.getElementById('fileNameDisplay');
const removeFile = document.getElementById('removeFile');
const docContainer = document.getElementById('lutDocContainer');

uploadBtn.addEventListener('click', () => {
    fileInput.click();
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        const name = e.target.files[0].name;
        fileNameDisplay.textContent = name;
        fileInfo.style.display = 'block';
        removeFile.style.display = 'inline-block';
        uploadBtn.style.display = 'none';
        docContainer.classList.add('has-file');
    }
});

removeFile.addEventListener('click', () => {
    fileInput.value = '';
    fileInfo.style.display = 'none';
    removeFile.style.display = 'none';
    uploadBtn.style.display = 'inline-block';
    docContainer.classList.remove('has-file');
});

const lutBondStartYear = document.getElementById('lutBondStartYear');
const lutBondEndYear = document.getElementById('lutBondEndYear');
const lutBondYear = document.getElementById('lutBondYear');
const lutExpiryDate = document.getElementById('lutExpiryDate');

function syncLutBondYear() {
    const startYear = lutBondStartYear.value;
    const endYear = lutBondEndYear.value;
    lutBondYear.value = startYear && endYear
        ? `${startYear}-${endYear.slice(-2)}`
        : '';
}

function populateLutBondEndYear(useSavedYear = false) {
    const startYear = Number(lutBondStartYear.value);
    const savedEndYear = useSavedYear ? lutBondEndYear.dataset.savedEndYear : '';

    lutBondEndYear.innerHTML = '';
    if (!Number.isInteger(startYear) || startYear < 1000) {
        lutBondEndYear.add(new Option('Select Start Year First', ''));
        lutBondEndYear.disabled = true;
        lutExpiryDate.removeAttribute('min');
        syncLutBondYear();
        return;
    }

    lutExpiryDate.min = `${startYear + 1}-01-01`;
    if (lutExpiryDate.value && lutExpiryDate.value < lutExpiryDate.min) {
        lutExpiryDate.value = '';
    }

    lutBondEndYear.add(new Option('Select End Year', ''));
    for (let yearOffset = 1; yearOffset <= 5; yearOffset += 1) {
        const endYear = String(startYear + yearOffset);
        lutBondEndYear.add(new Option(endYear, endYear));
    }
    lutBondEndYear.disabled = false;
    lutBondEndYear.value = savedEndYear && lutBondEndYear.querySelector(`option[value="${savedEndYear}"]`)
        ? savedEndYear
        : String(startYear + 1);
    syncLutBondYear();
}

lutBondStartYear.addEventListener('change', () => populateLutBondEndYear(false));
lutBondEndYear.addEventListener('change', syncLutBondYear);
populateLutBondEndYear(true);

function showCsb5ValidationError(message, field) {
    alert(message);
    if (field) {
        if (field.type !== 'file') field.focus();
        const container = field.type === 'file' ? field.closest('.doc-item') : field.closest('.input-wrapper');
        if (container) container.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return false;
}

function validateCsb5File(form, name, label, allowedExtensions, maxMb, required = true) {
    const input = form.querySelector(`[name="${name}"]`);
    const file = input && input.files ? input.files[0] : null;
    if (!file) return required ? showCsb5ValidationError(`Please upload ${label}.`, input) : true;

    const extension = file.name.includes('.') ? file.name.split('.').pop().toLowerCase() : '';
    if (!allowedExtensions.includes(extension)) {
        return showCsb5ValidationError(`${label} must be a ${allowedExtensions.join(', ').toUpperCase()} file.`, input);
    }
    const imageOnly = allowedExtensions.every(ext => ['jpg', 'jpeg', 'png'].includes(ext));
    if (imageOnly && !['image/jpeg', 'image/png'].includes(file.type)) {
        return showCsb5ValidationError(`${label} must be a valid JPG, JPEG, or PNG image.`, input);
    }
    if (file.size > maxMb * 1024 * 1024) {
        return showCsb5ValidationError(`${label} must not exceed ${maxMb} MB.`, input);
    }
    return true;
}

const csbGstSection = document.getElementById('csbGstSection');
const csbGstNumber = document.getElementById('csbGstNumber');
const csbGstBusinessName = document.getElementById('csbGstBusinessName');
const csbGstCertificate = document.getElementById('csbGstCertificate');
const csbGstVerificationStatus = document.getElementById('csbGstVerificationStatus');
const verifyCsbGstBtn = document.getElementById('verifyCsbGstBtn');
let csbGstVerified = false;

function normalizedGst(value) {
    return value.replace(/\s+/g, '').toUpperCase();
}

function setCsbGstVerificationState(verified, message, success = false) {
    csbGstVerified = verified;
    if (!csbGstVerificationStatus) return;
    csbGstVerificationStatus.textContent = message;
    csbGstVerificationStatus.className = success ? 'small text-success' : 'small text-muted';
}

if (csbGstNumber && csbGstBusinessName) {
    [csbGstNumber, csbGstBusinessName].forEach(input => {
        input.addEventListener('input', () => {
            input.value = input === csbGstNumber ? normalizedGst(input.value) : input.value;
            setCsbGstVerificationState(false, 'GST details changed. Verify GST again before submission.');
        });
    });
}

// Same verification API call as the KYC dashboard's "Verify GST"
// (POST customer/verify-gst -> KycController@verifyGst via Cashfree).
verifyCsbGstBtn?.addEventListener('click', async () => {
    const gst = normalizedGst(csbGstNumber?.value || '');
    const businessName = (csbGstBusinessName?.value || '').trim();
    const file = csbGstCertificate?.files?.[0];
    const storedPath = (document.getElementById('csbGstCertPath')?.value || '').trim();

    if (!/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/.test(gst)) {
        return showCsb5ValidationError('Enter a valid 15-character GSTIN.', csbGstNumber);
    }
    if (!businessName) {
        return showCsb5ValidationError('Enter the registered Business Name.', csbGstBusinessName);
    }
    if (!file && !storedPath) {
        return showCsb5ValidationError('Please upload the GST Certificate PDF.', csbGstCertificate);
    }
    if (file && !validateCsb5File(document.getElementById('csbvForm'), 'gst_certificate_document', 'the GST Certificate', ['pdf'], 5)) return;

    // Same payload shape as the KYC verify GST request.
    const formData = new FormData();
    formData.append('gst_number', gst);
    formData.append('business_name', businessName);
    if (file) {
        formData.append('gst_certificate_document', file);
    } else {
        formData.append('gst_certificate_document_path', storedPath);
    }

    verifyCsbGstBtn.disabled = true;
    verifyCsbGstBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
    setCsbGstVerificationState(false, 'Verifying GST through Cashfree...');

    try {
        const response = await fetch(csbGstSection.dataset.verifyUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('#csbvForm input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        const contentType = response.headers.get('content-type');
        const data = contentType && contentType.includes('application/json')
            ? await response.json()
            : await response.text().then(text => {
                console.error('Non-JSON GST response:', text);
                throw new Error('Server error (non-JSON response). Please try again.');
            });

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'GST verification failed.');
        }

        // Success behaviour mirrors the KYC verify flow.
        csbGstVerified = true;
        const verifiedBusinessName = (data.business_name || businessName).trim();
        csbGstNumber.value = normalizedGst(data.gst_number || gst);
        csbGstBusinessName.value = verifiedBusinessName;

        if (data.address) {
            const billingAddress = document.getElementById('billingAddress');
            if (billingAddress) billingAddress.value = data.address;
        }

        setCsbGstVerificationState(true, data.message || 'GST number and Business Name verified successfully.', true);
        verifyCsbGstBtn.innerHTML = '<i class="fas fa-check"></i> Verified';
        csbGstNumber.readOnly = true;
        csbGstBusinessName.readOnly = true;
    } catch (error) {
        console.error('GST verification error:', error);
        const message = error && error.message ? error.message : 'GST verification could not be completed.';
        setCsbGstVerificationState(false, message);
        alert(message);
        verifyCsbGstBtn.disabled = false;
        verifyCsbGstBtn.innerHTML = '<i class="fas fa-shield-alt me-1"></i> VERIFY GST';
    }
});

function validateCsb5Form(form) {
    const field = name => form.querySelector(`[name="${name}"]`);
    const value = name => (field(name) ? field(name).value.trim() : '');
    const standardDocuments = ['pdf', 'jpg', 'jpeg', 'png'];

    if (!/^\d{14}$/.test(value('ad_code'))) return showCsb5ValidationError('AD Code must be exactly 14 numeric digits.', field('ad_code'));
    if (!/^[A-Z0-9]{10}$/.test(value('iec_number').toUpperCase())) return showCsb5ValidationError('IEC Number must be exactly 10 letters or digits.', field('iec_number'));

    const gstEnabled = document.getElementById('gstType').checked;
    const lutEnabled = document.getElementById('lutType').checked;
    if (!gstEnabled && !lutEnabled) return showCsb5ValidationError('Please select GST, LUT, or both before continuing.', document.getElementById('gstType'));

    if (gstEnabled) {
        const gstCertInput = form.querySelector('[name="gst_certificate_document"]');
        const hasGstCertFile = Boolean(gstCertInput && gstCertInput.files && gstCertInput.files[0]);
        const storedGstCertPath = (document.getElementById('csbGstCertPath')?.value || '').trim();
        if (!/^[0-3][0-9][A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/.test(normalizedGst(value('gst_certificate_number')))) {
            return showCsb5ValidationError('Enter a valid 15-character GSTIN.', field('gst_certificate_number'));
        }
        if (!value('gst_business_name')) return showCsb5ValidationError('Please enter the registered Business Name.', field('gst_business_name'));
        if (!hasGstCertFile && !storedGstCertPath) return showCsb5ValidationError('Please upload the GST Certificate PDF.', gstCertInput);
        if (hasGstCertFile && !validateCsb5File(form, 'gst_certificate_document', 'the GST Certificate', ['pdf'], 5)) return false;
        if (!csbGstVerified) return showCsb5ValidationError('Verify the GSTIN and Business Name through Cashfree before continuing.', verifyCsbGstBtn);
    }

    if (!validateCsb5File(form, 'ad_code_document', 'the AD Code Document', standardDocuments, 5)) return false;
    if (!validateCsb5File(form, 'iec_document', 'the IEC Document', standardDocuments, 5)) return false;
    if (!/^\d{9,18}$/.test(value('bank_account_number'))) return showCsb5ValidationError('Bank Account Number must contain 9 to 18 digits.', field('bank_account_number'));
    if (!['private', 'government'].includes(value('bank_type'))) return showCsb5ValidationError('Please select a valid Bank Type.', field('bank_type'));

    if (lutEnabled) {
        if (!value('lut_number')) return showCsb5ValidationError('Please enter the LUT Number.', field('lut_number'));
        syncLutBondYear();
        const startYear = Number(value('lut_bond_start_year'));
        const endYear = Number(value('lut_bond_end_year'));
        if (!startYear) return showCsb5ValidationError('Please select the LUT Bond Start Year.', field('lut_bond_start_year'));
        if (!endYear) return showCsb5ValidationError('Please select the LUT Bond End Year.', field('lut_bond_end_year'));
        if (endYear < startYear + 1 || endYear > startYear + 5) return showCsb5ValidationError('LUT Bond End Year must be within five years after the Start Year.', field('lut_bond_end_year'));
        if (!value('lut_expiry_date')) return showCsb5ValidationError('Please select the LUT Expiry Date.', field('lut_expiry_date'));
        if (value('lut_expiry_date') < `${startYear + 1}-01-01`) return showCsb5ValidationError(`LUT Expiry Date must be on or after ${startYear + 1}-01-01.`, field('lut_expiry_date'));
        if (!validateCsb5File(form, 'lut_document', 'the LUT Document', ['pdf'], 5)) return false;
    }

    if (value('billing_address').length < 10 || value('billing_address').length > 1000) return showCsb5ValidationError('Billing Address must contain 10 to 1000 characters.', field('billing_address'));
    if (!/^[6-9]\d{9}$/.test(value('billing_contact'))) return showCsb5ValidationError('Billing Contact Number must contain exactly 10 digits and start with 6, 7, 8, or 9.', field('billing_contact'));
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value('billing_email'))) return showCsb5ValidationError('Please enter a valid Billing Email address.', field('billing_email'));
    if (!validateCsb5File(form, 'merchant_agreement', 'the signed Merchant Agreement', ['pdf'], 10)) return false;
    if (!field('terms_accepted').checked) return showCsb5ValidationError('Please accept the declaration and terms before continuing.', field('terms_accepted'));
    return true;
}

// Form Submit
document.getElementById('csbvForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('.btn-gradient');
    const form = this;
    if (!validateCsb5Form(form)) return;

    const formData = new FormData(form);
    formData.set('is_csb_v', '1');
    formData.set('is_gst', document.getElementById('gstType').checked ? '1' : '0');
    formData.set('is_lut', document.getElementById('lutType').checked ? '1' : '0');
    formData.set('lut_verified', '0');

    btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> SUBMITTING...';
    btn.style.opacity = '0.8';
    btn.style.pointerEvents = 'none';

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            btn.innerHTML = 'SUCCESS';
            btn.style.background = '#10b981';
            btn.style.opacity = '1';
            setTimeout(() => { window.location.href = data.redirect; }, 1000);
        } else {
            btn.innerHTML = 'CONTINUE';
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
            if (data.errors) {
                let errorMessage = 'Please fix the following errors:\n';
                for (const [key, value] of Object.entries(data.errors)) errorMessage += `- ${value[0]}\n`;
                alert(errorMessage);
            } else alert(data.message || 'An error occurred. Please try again.');
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
