<?php
// barcode-generator.php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Barcode Generator</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- JsBarcode -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

    <!-- QR Code -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
    body {
        background: #f4f7fb;
        font-family: Arial, sans-serif;
    }

    .main-box {
        max-width: 1000px;
        margin: 40px auto;
    }

    .card-custom {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .left-box {
        background: #ffffff;
        padding: 30px;
    }

    .right-box {
        background: linear-gradient(135deg, #0b1a2b, #163c70);
        color: #fff;
        padding: 30px;
    }

    .title {
        font-size: 32px;
        font-weight: bold;
    }

    .sub-title {
        color: #666;
        margin-bottom: 25px;
    }

    .form-control,
    .form-select {
        height: 50px;
        border-radius: 12px;
    }

    .btn-generate {
        background: #0d6efd;
        border: none;
        height: 50px;
        border-radius: 12px;
        width: 100%;
        font-size: 18px;
        font-weight: bold;
    }

    .preview-box {
        background: #fff;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        min-height: 300px;
    }

    svg {
        max-width: 100%;
    }

    .download-btns a {
        margin: 5px;
    }

    #qrcode img {
        margin: auto;
    }

    .info-box {
        margin-top: 20px;
        background: rgba(255, 255, 255, 0.1);
        padding: 15px;
        border-radius: 12px;
    }
    </style>
</head>

<body>

    <div class="container">

        <div class="main-box">
            <div class="card card-custom">

                <div class="row g-0">

                    <!-- LEFT -->
                    <div class="col-md-5 left-box">

                        <h1 class="title">Barcode Generator</h1>

                        <p class="sub-title">
                            Generate shipment and product barcodes instantly.
                        </p>

                        <div class="mb-3">
                            <label class="form-label">Barcode Text</label>
                            <input type="text" id="barcodeText" class="form-control" placeholder="Enter tracking number"
                                value="SHIP123456">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Barcode Format</label>

                            <select id="barcodeFormat" class="form-select">

                                <option value="CODE128">CODE128</option>
                                <option value="CODE39">CODE39</option>
                                <option value="EAN13">EAN13</option>
                                <option value="EAN8">EAN8</option>
                                <option value="UPC">UPC</option>
                                <option value="ITF14">ITF14</option>
                                <option value="MSI">MSI</option>
                                <option value="QR">QR CODE</option>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Label Title</label>

                            <input type="text" id="labelTitle" class="form-control" placeholder="Optional"
                                value="ShipGlobal Demo">
                        </div>

                        <button class="btn btn-primary btn-generate" onclick="generateBarcode()">

                            Generate Barcode

                        </button>

                    </div>

                    <!-- RIGHT -->
                    <div class="col-md-7 right-box">

                        <h3>Barcode Preview</h3>

                        <div class="preview-box mt-4">

                            <h5 id="previewTitle"></h5>

                            <svg id="barcode"></svg>

                            <div id="qrcode"></div>

                        </div>

                        <div class="download-btns mt-4 text-center">

                            <a href="#" id="downloadPNG" class="btn btn-light">

                                Download PNG

                            </a>

                            <a href="#" id="downloadSVG" class="btn btn-warning">

                                Download SVG

                            </a>

                        </div>

                        <div class="info-box">

                            <p><strong>Format:</strong> <span id="formatText"></span></p>

                            <p><strong>Characters:</strong> <span id="charCount"></span></p>

                        </div>

                    </div>

                </div>

            </div>
        </div>

    </div>

    <script>
    generateBarcode();

    function generateBarcode() {

        let text = document.getElementById('barcodeText').value;
        let format = document.getElementById('barcodeFormat').value;
        let title = document.getElementById('labelTitle').value;

        document.getElementById('previewTitle').innerHTML = title;
        document.getElementById('formatText').innerHTML = format;
        document.getElementById('charCount').innerHTML = text.length;

        document.getElementById('barcode').style.display = "block";
        document.getElementById('qrcode').innerHTML = "";

        if (format === "QR") {

            document.getElementById('barcode').style.display = "none";

            new QRCode(document.getElementById("qrcode"), {
                text: text,
                width: 220,
                height: 220
            });

        } else {

            try {

                JsBarcode("#barcode", text, {

                    format: format,
                    lineColor: "#000",
                    width: 2,
                    height: 100,
                    displayValue: true,
                    fontSize: 18

                });

            } catch (err) {

                alert("Invalid data for selected barcode format");

            }
        }

    }
    </script>

</body>

</html>