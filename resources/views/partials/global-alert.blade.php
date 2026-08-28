{{--
====================================================================
 GLOBAL ALERT → MODAL OVERRIDE
 Replaces every native JS alert() dialog with a modal popup.
  - Loads SweetAlert2 (if not already present) and uses it.
  - Falls back to a self-contained custom modal if SweetAlert2
    cannot be loaded.
  - Exposes window.showAlert(message, type, callback) helper:
        type: 'info' | 'success' | 'warning' | 'error'
        callback: optional function executed after user closes popup
====================================================================
--}}
@once
<style>
    .uc-alert-overlay {
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(15, 23, 42, .55);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        visibility: hidden;
        transition: opacity .25s ease, visibility .25s ease;
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    }
    .uc-alert-overlay.show {
        opacity: 1;
        visibility: visible;
    }
    .uc-alert-box {
        background: #ffffff;
        border-radius: 14px;
        width: min(92vw, 430px);
        padding: 30px 26px 24px;
        box-shadow: 0 24px 70px rgba(0, 0, 0, .35);
        transform: translateY(20px) scale(.96);
        transition: transform .25s ease;
        text-align: center;
    }
    .uc-alert-overlay.show .uc-alert-box {
        transform: translateY(0) scale(1);
    }
    .uc-alert-icon {
        width: 54px;
        height: 54px;
        margin: 0 auto 14px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        font-weight: 700;
        color: #fff;
        line-height: 1;
    }
    .uc-alert-icon.info    { background: #2f6fed; }
    .uc-alert-icon.success { background: #1e9e5a; }
    .uc-alert-icon.warning { background: #f4a62a; }
    .uc-alert-icon.error   { background: #e5484d; }
    .uc-alert-title {
        font-size: 18px;
        font-weight: 700;
        color: #1c2030;
        margin-bottom: 8px;
    }
    .uc-alert-message {
        font-size: 14px;
        color: #555c6e;
        line-height: 1.6;
        max-height: 45vh;
        overflow-y: auto;
        word-break: break-word;
    }
    .uc-alert-ok {
        margin-top: 20px;
        width: 100%;
        padding: 10px 16px;
        border: 0;
        border-radius: 8px;
        background: #2f6fed;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s ease;
    }
    .uc-alert-ok:hover {
        background: #2557c4;
    }
</style>
<script>
(function () {
    if (window.__ucAlertInstalled) return;
    window.__ucAlertInstalled = true;

    function escapeHtml(t) {
        return String(t == null ? '' : t)
            .replace(/&/g, '&')
            .replace(/</g, '<')
            .replace(/>/g, '>')
            .replace(/"/g, '"');
    }

    function ensureSwal(cb) {
        if (window.Swal) { cb(); return; }
        var s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
        s.onload = function () { cb(); };
        s.onerror = function () { cb(); };
        document.head.appendChild(s);
    }

    function swalIcon(type) {
        return (type === 'success') ? 'success' :
               (type === 'error')   ? 'error'   :
               (type === 'warning') ? 'warning' : 'info';
    }

    function swalTitle(type) {
        return (type === 'success') ? 'Success' :
               (type === 'error')   ? 'Error'   :
               (type === 'warning') ? 'Warning' : '';
    }

    /* ---- Self-contained custom modal (fallback when SweetAlert2 is unavailable) ---- */
    function showCustomModal(message, type, onClose) {
        var overlay = document.getElementById('ucAlertOverlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'ucAlertOverlay';
            overlay.className = 'uc-alert-overlay';
            overlay.innerHTML =
                '<div class="uc-alert-box">' +
                    '<div class="uc-alert-icon"></div>' +
                    '<div class="uc-alert-title"></div>' +
                    '<div class="uc-alert-message"></div>' +
                    '<button type="button" class="uc-alert-ok">OK</button>' +
                '</div>';
            document.body.appendChild(overlay);
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) hideCustomModal();
            });
            overlay.querySelector('.uc-alert-ok').addEventListener('click', function () {
                hideCustomModal();
            });
        }
        var t = type || 'info';
        overlay.querySelector('.uc-alert-icon').className = 'uc-alert-icon ' + t;
        overlay.querySelector('.uc-alert-icon').textContent =
            (t === 'success') ? '✓' : (t === 'error') ? '✕' : (t === 'warning') ? '!' : 'i';
        overlay.querySelector('.uc-alert-title').textContent = swalTitle(t) || 'Notification';
        overlay.querySelector('.uc-alert-message').innerHTML = escapeHtml(message).replace(/\n/g, '<br>');
        overlay._ucOnClose = onClose || null;
        overlay.classList.add('show');
    }

    function hideCustomModal() {
        var overlay = document.getElementById('ucAlertOverlay');
        if (!overlay) return;
        overlay.classList.remove('show');
        var cb = overlay._ucOnClose;
        overlay._ucOnClose = null;
        if (typeof cb === 'function') cb();
    }

    /* ---- Global helper: showAlert(message, type, callback) ---- */
    window.showAlert = function (message, type, onClose) {
        var text = message == null ? '' : String(message);
        ensureSwal(function () {
            if (window.Swal) {
                Swal.fire({
                    icon: swalIcon(type),
                    title: swalTitle(type),
                    html: escapeHtml(text).replace(/\n/g, '<br>'),
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#405189',
                    allowOutsideClick: true
                }).then(function () {
                    if (typeof onClose === 'function') onClose();
                });
            } else {
                showCustomModal(text, type, onClose);
            }
        });
    };

    /* ---- Override native alert() so NO browser dialog can ever appear ---- */
    window.alert = function (message) {
        window.showAlert(message, 'info');
    };
})();
</script>
@endonce
