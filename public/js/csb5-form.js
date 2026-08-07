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

function validateCsb5Form(form) {
    const field = name => form.querySelector(`[name="${name}"]`);
    const value = name => (field(name) ? field(name).value.trim() : '');
    const standardDocuments = ['pdf', 'jpg', 'jpeg', 'png'];

    if (!/^\d{14}$/.test(value('ad_code'))) return showCsb5ValidationError('AD Code must be exactly 14 numeric digits.', field('ad_code'));
    if (!/^[A-Z0-9]{10}$/.test(value('iec_number').toUpperCase())) return showCsb5ValidationError('IEC Number must be exactly 10 letters or digits.', field('iec_number'));
    if (!validateCsb5File(form, 'ad_code_document', 'the AD Code Document', standardDocuments, 5)) return false;
    if (!validateCsb5File(form, 'iec_document', 'the IEC Document', standardDocuments, 5)) return false;
    if (!/^\d{9,18}$/.test(value('bank_account_number'))) return showCsb5ValidationError('Bank Account Number must contain 9 to 18 digits.', field('bank_account_number'));
    if (!['private', 'government'].includes(value('bank_type'))) return showCsb5ValidationError('Please select a valid Bank Type.', field('bank_type'));

    const lutEnabled = document.getElementById('lutType').checked;
    if (lutEnabled) {
        syncLutBondYear();
        const startYear = Number(value('lut_bond_start_year'));
        const endYear = Number(value('lut_bond_end_year'));
        if (!startYear) return showCsb5ValidationError('Please select the LUT Bond Start Year.', field('lut_bond_start_year'));
        if (!endYear) return showCsb5ValidationError('Please select the LUT Bond End Year.', field('lut_bond_end_year'));
        if (endYear < startYear + 1 || endYear > startYear + 5) return showCsb5ValidationError('LUT Bond End Year must be within five years after the Start Year.', field('lut_bond_end_year'));
        if (!value('lut_expiry_date')) return showCsb5ValidationError('Please select the LUT Expiry Date.', field('lut_expiry_date'));
        const minimumExpiryDate = `${startYear + 1}-01-01`;
        if (value('lut_expiry_date') < minimumExpiryDate) return showCsb5ValidationError(`LUT Expiry Date must be on or after ${minimumExpiryDate}.`, field('lut_expiry_date'));
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

    // Prepare form data only after every field has passed validation.
    const formData = new FormData(form);

    // CSB-V is fixed for this form; only LUT remains user-selectable.
    formData.set('is_csb_v', '1');
    formData.set('is_gst', '0');
    formData.set('is_lut', document.getElementById('lutType').checked ? '1' : '0');
    formData.set('lut_verified', '0');

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
