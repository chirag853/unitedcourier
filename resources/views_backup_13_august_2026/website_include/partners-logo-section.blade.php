<!-- Partners Logo Section (Common Partial) -->
<!-- Displays partner logos from the database in a seamless looping slider -->
<section class="partners-section">
    <div class="logo-slider">
        <div class="logo-track">
            {{-- Original Logos --}}
            @foreach($partnerLogos as $logo)
            <div class="logo-item" class="logo-item-partners">
                <img src="{{ $logo->logo_image }}" alt="{{ $logo->alt_text ?? 'Partner Logo' }}" class="partners-logo-img">
            </div>
            @endforeach

            {{-- Cloned Logos (for seamless infinite loop) --}}
            @foreach($partnerLogos as $logo)
            <div class="logo-item" class="logo-item-partners">
                <img src="{{ $logo->logo_image }}" alt="{{ $logo->alt_text ?? 'Partner Logo' }}" class="partners-logo-img">
            </div>
            @endforeach
        </div>
    </div>
</section>