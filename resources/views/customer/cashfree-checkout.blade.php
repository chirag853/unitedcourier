<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment | United Courier</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

    <style>
        body {
            background: #f2f5ff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .loading-box {
            text-align: center;
            color: #243b63;
        }

        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #dbe3ff;
            border-top-color: #2f66f3;
            border-radius: 50%;
            margin: 0 auto 16px;
            animation: spin 0.9s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div class="loading-box">
        <div class="spinner"></div>
        <h5 class="fw-bold mb-1">Redirecting to secure payment…</h5>
        <p class="text-muted mb-0 small">Please do not close this window.</p>
    </div>

    <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
    <script>
        (function () {
            var sessionId = @json($paymentSessionId ?? '');
            var mode = @json($paymentMode ?? 'sandbox');
            var loadingBox = document.querySelector('.loading-box');

            function showError(message) {
                loadingBox.innerHTML = '<h5 class="fw-bold text-danger mb-3">' + message + '</h5>' +
                    '<a class="btn btn-primary mt-2" href="{{ route('customer.wallet-history') }}">Go to Wallet</a>';
            }

            if (!sessionId) {
                showError('Invalid payment session.');
                return;
            }

            function startCheckout() {
                try {
                    // The SDK must be initialized with the environment (sandbox/
                    // production); initializing without a mode leaves checkout
                    // stuck on a loading screen.
                    var cashfree = Cashfree({ mode: mode });

                    cashfree.checkout({
                        paymentSessionId: sessionId,
                        redirectTarget: '_self'
                    }).then(function (result) {
                        if (result && result.error) {
                            console.error('Cashfree checkout error:', result.error);
                            showError('Payment could not be completed. Please try again.');
                        }
                        if (result && result.paymentDetails) {
                            console.log('Cashfree payment finished:', result.paymentDetails);
                        }
                    }).catch(function (err) {
                        console.error('Cashfree checkout failed:', err);
                        showError('Payment could not be started. Please try again.');
                    });
                } catch (e) {
                    console.error('Cashfree init error:', e);
                    showError('Payment could not be started. Please try again.');
                }
            }

            if (window.Cashfree) {
                startCheckout();
            } else {
                var s = document.createElement('script');
                s.src = 'https://sdk.cashfree.com/js/v3/cashfree.js';
                s.onload = startCheckout;
                s.onerror = function () {
                    showError('Payment gateway could not be loaded. Please try again.');
                };
                document.head.appendChild(s);
            }
        })();
    </script>
</body>

</html>
