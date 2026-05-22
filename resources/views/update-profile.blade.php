@include('customer.partials.header')

    <div class="hero-gradient-container" style="margin-top: 70px;">
        <div class="floating-blob bg-warning opacity-25" style="width: 250px; height: 250px; top: 10%; left: -125px;"></div>
        <div class="floating-blob bg-primary opacity-10" style="width: 200px; height: 200px; bottom: 10%; right: -100px;"></div>
        <div class="container d-flex justify-content-center">
            <div class="profile-form-shadow animate__animated animate__fadeInUp">
                
                <div class="profile-header">
                    <div>
                        <h3 class="h4-title mb-0">Update Your <span class="gradient-text">Business Profile</span></h3>
                        <p class="text-muted small mb-0">Manage your organization and account details</p>
                    </div>
                </div>

                <form id="profileUpdateForm">
                    <!-- Read-Only Personal Section -->
                    <div class="section-divider">
                        <span class="section-label">Account Details (Verified)</span>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">First Name</label>
                            <div class="input-group-custom">
                                <input type="text" class="form-control input-custom" value="Rahul" readonly>
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Last Name</label>
                            <div class="input-group-custom">
                                <input type="text" class="form-control input-custom" value="Kumar" readonly>
                                <i class="fas fa-user-tag"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Email Address</label>
                            <div class="input-group-custom">
                                <input type="email" class="form-control input-custom" value="rahul@unitedcouriers.biz" readonly>
                                <i class="fas fa-envelope"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Phone Number</label>
                            <div class="input-group-custom">
                                <input type="text" class="form-control input-custom" value="+919876543210" readonly>
                                <i class="fas fa-phone"></i>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Aadhar Number</label>
                            <div class="input-group-custom">
                                <input type="text" class="form-control input-custom" value="XXXX-XXXX-1234" readonly>
                                <i class="fas fa-id-card"></i>                        
                            </div>
                        </div>
                    </div>

                    <!-- Editable Business Section -->
                    <div class="section-divider">
                        <span class="section-label">Business Information</span>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label-custom">Business Type</label>
                            <div class="input-group-custom">
                                <select class="form-select input-custom" required>
                                    <option value="" selected disabled>Select Type</option>
                                    <option value="sole">Sole Proprietorship</option>
                                    <option value="pvt">Pvt Ltd Company</option>
                                    <option value="llp">LLP / Partnership</option>
                                    <option value="individual">Individual / Freelancer</option>
                                </select>
                                <i class="fas fa-briefcase"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Organisation Name</label>
                            <div class="input-group-custom">
                                <input type="text" class="form-control input-custom" placeholder="e.g. United Worldwide Courier" required>
                                <i class="fas fa-building"></i>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label-custom">Shipping Type</label>
                            <div class="input-group-custom">
                                <select class="form-select input-custom" required>
                                    <option value="" selected disabled>Select Type</option>
                                    <option value="sole">CSB IV</option>
                                    <option value="pvt">CSB V</option>
                                </select>
                                <i class="fas fa-briefcase"></i>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label-custom">GST Number</label>
                            <div class="input-group-custom">
                                <input type="text" class="form-control input-custom" placeholder="22AAAAA0000A1Z5" maxlength="15" required>
                                <i class="fas fa-file-invoice"></i>
                                <button type="button" class="btn-otp">Verify GSTIN</button>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label-custom">Corporate Address</label>
                            <div class="input-group-custom">
                                <textarea class="form-control input-custom" placeholder="Full Registered Address..." required></textarea>
                                <i class="fas fa-map-marker-alt" style="top: 18px;"></i>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 text-end">
                        <button type="submit" class="btn moving-gradient-bg btn-primary-custom w-100 w-md-auto">
                            Update Profile
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <script>
        // Initialize Lenis
        const lenis = new Lenis();
        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        // Form Submission Logic
        document.getElementById('profileUpdateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i> Saving Changes...';
            btn.style.opacity = '0.8';
            btn.style.pointerEvents = 'none';

            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check-circle me-2"></i> Profile Updated Successfully';
                btn.style.backgroundColor = '#10b981';
                btn.classList.remove('moving-gradient-bg');
                btn.style.opacity = '1';
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.style.backgroundColor = '';
                    btn.classList.add('moving-gradient-bg');
                    btn.style.pointerEvents = 'auto';
                }, 3000);
            }, 1500);
        });
    </script>

@include('customer.partials.footer')