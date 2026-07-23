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

// Form Submit
document.getElementById('csbvForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('.btn-gradient');
    const form = this;
    
    // Prepare form data
    const formData = new FormData(form);
    
    // Handle checkboxes - send as boolean
    formData.set('is_csb_v', document.getElementById('csbvToggle').checked ? '1' : '0');
    formData.set('is_gst', document.getElementById('gstType').checked ? '1' : '0');
    formData.set('is_lut', document.getElementById('lutType').checked ? '1' : '0');
    
    // Basic validation check for file
    if (document.getElementById('lutType').checked && fileInput.files.length === 0) {
        alert('Please upload the LUT document before continuing.');
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
