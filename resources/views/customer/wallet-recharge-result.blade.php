<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Wallet Recharge | United Courier</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="app-style">

    <style>
        body {
            background: linear-gradient(135deg, #eef2ff 0%, #f8f9ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .result-card {
            background: #fff;
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(36, 59, 99, 0.12);
            max-width: 480px;
            width: 100%;
            padding: 40px 32px;
            text-align: center;
        }

        .status-icon {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-bottom: 20px;
        }

        .icon-success { background: #e7f8ee; color: #1f9d55; }
        .icon-failed { background: #fdeaea; color: #e0474a; }
        .icon-pending { background: #fff4e5; color: #e59a13; }
        .icon-error { background: #fdeaea; color: #e0474a; }

        .amount-box {
            background: #f5f7ff;
            border-radius: 14px;
            padding: 18px;
            margin: 22px 0;
        }

        .amount-box .label {
            font-size: 12px;
            color: #6b7a99;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .amount-box .amount {
            font-size: 30px;
            font-weight: 700;
            color: #243b63;
        }

        .btn-primary {
            background: #2f66f3;
            border: none;
            font-weight: 600;
            border-radius: 10px;
            padding: 12px 24px;
        }

        .btn-primary:hover { background: #2452d0; }

        .btn-light {
            background: #f5f6f8;
            border: none;
            color: #243b63;
            font-weight: 600;
            border-radius: 10px;
            padding: 12px 24px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="result-card">

                @php
                    $iconClass = 'icon-success';
                    $icon = 'bi-check-circle-fill';
                    $heading = 'Payment Successful';
                    if ($status === 'failed') {
                        $iconClass = 'icon-failed';
                        $icon = 'bi-x-circle-fill';
                        $heading = 'Payment Failed';
                    } elseif ($status === 'pending') {
                        $iconClass = 'icon-pending';
                        $icon = 'bi-hourglass-split';
                        $heading = 'Payment Pending';
                    } elseif ($status === 'error') {
                        $iconClass = 'icon-error';
                        $icon = 'bi-exclamation-triangle-fill';
                        $heading = 'Something Went Wrong';
                    }
                @endphp

                <div class="status-icon {{ $iconClass }}">
                    <i class="bi {{ $icon }}"></i>
                </div>

                <h4 class="fw-bold text-dark mb-2">{{ $heading }}</h4>
                <p class="text-muted mb-0">{{ $message }}</p>

                @if (isset($amount) && $status === 'success')
                    <div class="amount-box">
                        <div class="label">Amount Credited</div>
                        <div class="amount">₹{{ number_format((float) $amount, 2) }}</div>
                    </div>
                @endif

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('customer.wallet-history') }}" class="btn btn-primary flex-fill">
                        <i class="bi bi-wallet2 me-1"></i>Go to Wallet
                    </a>
                    @if ($status === 'success')
                        <a href="{{ route('customer.dashboard') }}" class="btn btn-light flex-fill">Dashboard</a>
                    @else
                        <a href="{{ route('customer.wallet-history') }}" class="btn btn-light flex-fill">Try Again</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Close the payment popup and redirect the main customer window to the
        // wallet history page. The notice (success/error/warning) is stored in
        // the session by the callback and shown on the wallet history page.
        (function () {
            var redirectUrl = @json($redirect_url ?? route('customer.wallet-history'));
            var delay = {{ $status === 'success' ? 1200 : 2500 }};

            setTimeout(function () {
                if (window.opener && !window.opener.closed) {
                    try {
                        window.opener.location.href = redirectUrl;
                        window.close();
                        return;
                    } catch (e) {
                        // Fall through to same-tab redirect.
                    }
                }
                window.location.href = redirectUrl;
            }, delay);
        })();
    </script>
</body>

</html>
