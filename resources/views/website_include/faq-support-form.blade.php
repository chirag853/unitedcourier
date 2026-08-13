<div class="faq-illustration">
    <div class="mb-4">
        <h4 class="h4-title">Any Queries? <span class="gradient-text">Get Support</span></h4>
    </div>
    <form id="faqQueryForm">
        @csrf
        <input type="hidden" name="page_name" value="{{ basename(request()->path()) }}">
        <div class="row g-3 mb-3">

            <!-- name -->
            <div class="col-12">
                <div class="input-group-custom">
                    <input type="text" name="full_name" class="form-control input-custom" placeholder="Full Name" required>
                    <i class="fas fa-user"></i>
                </div>
            </div>

            <!-- email -->
            <div class="col-12">
                <div class="input-group-custom">
                    <input type="email" name="email" class="form-control input-custom" placeholder="Email" required>
                    <i class="fas fa-envelope"></i>
                </div>
            </div>

            <!-- phone -->
            <div class="col-12">
                <div class="input-group-custom">
                    <input type="tel" name="phone" class="form-control input-custom" placeholder="Phone" required>
                    <i class="fas fa-phone"></i>
                </div>
            </div>

            <!-- message -->
            <div class="col-12">
                <div class="input-group-custom">
                    <textarea name="message" class="form-control input-custom" rows="2" placeholder="Message" required></textarea>
                    <i style="top: 18px;" class="fa-solid fa-comment-dots"></i>
                </div>
            </div>

        </div>

        <div id="faqQueryMessage" class="subscribe-message" style="margin-top: 8px; font-size: 14px; display: none;"></div>

        <button type="submit" class="btn moving-gradient-bg btn-primary-custom" id="faqQuerySubmitBtn">
            Get Support <i class="fa-solid fa-paper-plane"></i>
        </button>
    </form>
</div>

<script>
    document.getElementById('faqQueryForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var form = this;
        var formData = new FormData(form);
        var button = document.getElementById('faqQuerySubmitBtn');
        var msgDiv = document.getElementById('faqQueryMessage');
        var originalText = button.innerHTML;

        button.innerHTML = 'Submitting...';
        button.disabled = true;
        msgDiv.style.display = 'none';

        fetch('{{ url("/faq-query") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(function(response) {
            return response.json().then(function(data) {
                return {
                    ok: response.ok,
                    data: data
                };
            });
        })
        .then(function(result) {
            var data = result.data;
            msgDiv.style.display = 'block';
            if (result.ok && data.success) {
                form.reset();
                msgDiv.style.color = '#28a745';
                msgDiv.innerText = data.message;
            } else {
                msgDiv.style.color = '#dc3545';
                if (data.errors) {
                    var errorMessages = [];
                    for (var key in data.errors) {
                        if (data.errors.hasOwnProperty(key)) {
                            errorMessages.push(data.errors[key][0]);
                        }
                    }
                    msgDiv.innerText = errorMessages.join(', ');
                } else {
                    msgDiv.innerText = data.message || 'Something went wrong. Please try again.';
                }
            }
        })
        .catch(function() {
            msgDiv.style.display = 'block';
            msgDiv.style.color = '#dc3545';
            msgDiv.innerText = 'Unable to contact the server. Please try again.';
        })
        .finally(function() {
            button.innerHTML = originalText;
            button.disabled = false;
        });
    });
</script>