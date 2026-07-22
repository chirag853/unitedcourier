# Overseas Logistic API Documentation

## Purpose

Overseas Logistic is used for Canada shipment creation under the methods:

- `UNITED CANADA DDP`
- `UNITED CANADA E-COMMERCE`

Both variants use the **same** Overseas Logistic endpoint and payload. The exact service is differentiated by the `Service` field inside `ServiceDetails` (resolved from `courier_services.service_code`) and the `DutyTax` field (`DDP` for the DDP method, `DDU` for E-Commerce).

## Endpoints

### Step 1: Generate Bearer Token

`POST https://api.overseaslogistic.com/token`

This is an **OAuth2 token server**. It uses the `client_credentials` grant type and requires the credentials to be sent as **form-encoded** data (`application/x-www-form-urlencoded`), NOT JSON. Sending `username`/`password` as JSON returns `{"error":"invalid_client"}`.

Headers:

```http
Content-Type: application/x-www-form-urlencoded
Accept: application/json
```

Payload (form-encoded):

```
grant_type=client_credentials
&client_id=OVERSEAS_USERNAME
&client_secret=OVERSEAS_PASSWORD
```

The configured `services.overseas.username` is sent as `client_id` and `services.overseas.password` as `client_secret`.

Recommended env variables:

```env
OVERSEAS_BASE_URL=https://api.overseaslogistic.com
OVERSEAS_TOKEN_URL=https://api.overseaslogistic.com/token
OVERSEAS_SHIPMENT_URL=https://api.overseaslogistic.com/api/shipment/create
OVERSEAS_USERNAME=your_overseas_username
OVERSEAS_PASSWORD=your_overseas_password
OVERSEAS_ACCOUNT_CODE=PR-U02
OVERSEAS_TIMEOUT=60
```

Config block lives in `config/services.php` under the `overseas` key, with `env()` fallbacks for every value.

Token extraction supports these response shapes (checked in order). The confirmed OAuth2 response is the first one:

```json
{
  "access_token": "TOKEN",
  "token_type": "bearer",
  "expires_in": 3599,
  "refresh_token": "REFRESH_TOKEN",
  ".issued": "Thu, 16 Jul 2026 05:32:05 GMT",
  ".expires": "Thu, 16 Jul 2026 06:32:05 GMT"
}
```

```json
{ "data": { "access_token": "TOKEN" } }
```

```json
{ "token": "TOKEN" }
```

```json
{ "data": { "token": "TOKEN" } }
```

```json
{ "Token": "TOKEN" }
```

### Step 2: Create Shipment

`POST https://api.overseaslogistic.com/api/shipment/create`

Headers:

```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer OVERSEAS_TOKEN
```

## Request Payload

```json
{
  "AccountCode": "PR-U02",
  "Sender": {
    "SenderName": "Sender Company",
    "SenderContactPerson": "Contact Person",
    "SenderAddressLine1": "Address line 1",
    "SenderAddressLine2": "Address line 2",
    "SenderAddressLine3": "Address line 3",
    "SenderPincode": "110037",
    "SenderCity": "Delhi",
    "SenderState": "Delhi",
    "SenderTelephone": "9999999999",
    "SenderEmailId": "sender@example.com",
    "KYCType": "GSTIN (Normal)",
    "KYCNo": "00ABCDE1234F1Z5"
  },
  "Receiver": {
    "ReceiverType": "Business",
    "ReceiverName": "Receiver Name",
    "ReceiverContactPerson": "Contact Person",
    "ReceiverAddressLine1": "Address line 1",
    "ReceiverAddressLine2": "Address line 2",
    "ReceiverAddressLine3": "Address line 3",
    "ReceiverZipcode": "V5K0A1",
    "ReceiverCity": "Vancouver",
    "ReceiverState": "BC",
    "ReceiverCountry": "CA",
    "ReceiverTelephone": "8888888888",
    "ReceiverEmailid": "receiver@example.com",
    "VatId": ""
  },
  "ServiceDetails": {
    "Service": "CANADA_YVR_SELF",
    "GoodsType": "NDox",
    "PackageType": "PACKAGE"
  },
  "PackageDetails": {
    "PackageDetail": [
      {
        "Length": 10,
        "Width": 10,
        "Height": 10,
        "ActualWeight": 0.5
      }
    ]
  },
  "AdditionalDetails": {
    "ProductDetails": [
      {
        "BoxNo": "1",
        "Description": "General Merchandise",
        "HSNCode": "610910",
        "HTSCode": "610910",
        "UnitType": "PCS",
        "Qty": 1,
        "UnitRate": 10,
        "ShipPieceIGST": 0,
        "PieceWt": 0.3
      }
    ],
    "InvoiceCurrency": "INR",
    "InvoiceNo": "INV001",
    "InvoiceDate": "2026-07-16T00:00:00Z",
    "TermsOfSale": "FOB",
    "ReasonForExport": "GIFT",
    "FreightCharge": 0,
    "InsuranceCharge": 0,
    "CSB_Type": "CSB 4",
    "CustomerRefNo": "REF123",
    "DeliveryConfirmation": "No",
    "DutyTax": "DDP",
    "DutiesAccountNo": "",
    "TransactionId": "UWC123456",
    "ShipperImage": "",
    "ShipperKYC": "",
    "FileName": ""
  }
}
```

Payload data sources:

| Payload field | Source |
| --- | --- |
| `AccountCode` | `config('services.overseas.account_code')` (default `PR-U02`) |
| `Sender.*` | `shipper_info` |
| `Receiver.*` | `consignee_info` |
| `ServiceDetails.Service` | `courier_services.service_code` or `courier_services.scode` |
| `PackageDetails.PackageDetail` | `package_dimensions` (one entry per package) |
| `AdditionalDetails.ProductDetails` | `shipment_invoice_items` |
| `AdditionalDetails.Invoice*` / `TermsOfSale` / `CustomerRefNo` | `shipment_invoice` |
| `AdditionalDetails.CSB_Type` | `customers.csb_status` |
| `AdditionalDetails.DutyTax` | derived from shipping method (`DDP` vs `DDU`) |
| `AdditionalDetails.TransactionId` | `shipper_info.awb_number` (fallback `TXN-<id>`) |

Important conversions/defaults:

| Field | Rule |
| --- | --- |
| `Service` | `courier_services.service_code ?? courier_services.scode`; fallback `CANADA_YVR_SELF` |
| `GoodsType` | hardcoded `NDox` |
| `PackageType` | hardcoded `PACKAGE` |
| `ReceiverType` | `consignee_info.origin_type` title-cased; fallback `Business` |
| `ReceiverCountry` | `getOverseasCountryCode(consignee.delivery_destination)`; defaults to `CA` |
| `KYCType` | `shipper_info.kyc_type`; fallback `GSTIN (Normal)` |
| `InvoiceDate` | `shipment_invoice.invoice_date` formatted as `Y-m-d\T00:00:00Z`; fallback today |
| `InvoiceCurrency` | `shipment_invoice.invoice_currency`; fallback `INR` |
| `TermsOfSale` | `shipment_invoice.incoterms`; fallback `FOB` |
| `DutyTax` | `DDP` when method contains `DDP`, otherwise `DDU` |
| `CSB_Type` | `CSB <customers.csb_status>` (1..5); fallback `CSB 4` |
| `ReasonForExport` | hardcoded `GIFT` |
| `DeliveryConfirmation` | hardcoded `No` |
| `PieceWt` | `amount / qty` rounded to 3 decimals when both > 0, else `0` |
| default product | `General Merchandise` when no invoice items exist |

## Success Response

The code treats the order as successful when HTTP is 2xx **and** the body-level `Status`/`status` is not a failure indicator. The confirmed API returns `Status` as a **boolean** (`true` for success, `false` for failure). A `Status` of `false` (boolean) is treated as a failure even on HTTP 200. For backward compatibility, a `status`/`Status` string equal to `ERROR` (case-insensitive) is also treated as a failure. On failure, the error message is read from the `Error`/`error`/`message`/`Message` field.

### Confirmed response shape (live API)

This is the actual shape returned by `https://api.overseaslogistic.com/api/shipment/create` (verified end-to-end):

```json
{
  "Status": true,
  "Error": null,
  "Data": {
    "AwbNo": "55141977",
    "Destination": "CANADA",
    "ChargeableWeight": "1.83(KG)",
    "TotalCharges": "3840.68",
    "AdditionalCharges": [
      { "Description": "CSB5 Charge", "Amount": 1000.00 }
    ],
    "Airwaybill": {
      "AirwaybillUrl": "https://sandbox.overseaslogistic.com/PrintLabel/55141977_Airwaybill.pdf",
      "BoxlabelUrl": "https://sandbox.overseaslogistic.com/PrintLabel/55141977_Airwaybill4X6.pdf",
      "CustomInvoiceUrl": "https://sandbox.overseaslogistic.com/PrintCustomInvoice/55141977_Invoice.pdf"
    },
    "TrackingNumbers": [
      { "TrackingNo": "", "LabelUrl": "", "InvoiceUrl": "" }
    ]
  },
  "Timestamp": "2026-07-16T06:28:18.4793115Z",
  "RequestId": "1b2ca5c0-4aef-4c8e-a0a7-61c6c74b79f7"
}
```

Key extraction points from the confirmed shape:

- **Tracking number** → `Data.AwbNo` (e.g. `55141977`)
- **Label URL** → `Data.Airwaybill.AirwaybillUrl` (the airwaybill PDF); `Data.Airwaybill.BoxlabelUrl` is the 4x6 box label and `Data.Airwaybill.CustomInvoiceUrl` is the custom invoice PDF
- **Success flag** → top-level `Status` boolean (`true`)

### Other supported success shapes

Tracking/label extraction is resilient to these additional shapes as well:

```json
{
  "status": "SUCCESS",
  "data": {
    "AwbNumber": "OL123456789",
    "LabelUrl": "https://api.overseaslogistic.com/labels/OL123456789.pdf"
  }
}
```

```json
{
  "AwbNumber": "OL123456789",
  "LabelUrl": "https://api.overseaslogistic.com/labels/OL123456789.pdf"
}
```

```json
{
  "data": [
    {
      "AwbNumber": "OL123456789",
      "LabelUrl": "https://api.overseaslogistic.com/labels/OL123456789.pdf"
    }
  ]
}
```

Internal wrapper returned by `callOverseasLogisticApiFromDb()`:

```json
{
  "success": true,
  "message": "Overseas Logistic shipment created successfully.",
  "data": {},
  "request_payload": {}
}
```

Tracking number extraction checks (in order, across list / top-level / `Data`|`data` / wrapper keys). `AwbNo` is the confirmed key and is checked first:

1. `AwbNo` / `AwbNumber` / `awb_number` / `AWBNumber`
2. `TrackingNumber` / `tracking_number`
3. `WaybillNumber` / `waybill_number`
4. `ConsignmentNumber` / `consignment_number`
5. `ShipmentNumber` / `shipment_number`
6. `OrderNumber` / `order_number`
7. `ReferenceNo` / `reference_no` / `RefNo`
8. `BookingId` / `booking_id`
9. `Waybill` / `waybill`
10. `Reference` / `reference`
11. `ShipmentId` / `shipment_id`

Label URL extraction checks. The confirmed nested path `Data.Airwaybill.AirwaybillUrl` is checked first (Case 0), then the same four cases as tracking:

1. `AirwaybillUrl` / `BoxlabelUrl` (confirmed keys, checked under `Data.Airwaybill` first, then top-level / `Data` / list)
2. `LabelUrl` / `label_url` / `LabelURL`
3. `LabelLink` / `label_link`
4. `PdfUrl` / `pdf_url` / `PdfLink` / `pdf_link`
5. `Label` / `label`
6. `LabelData` / `label_data`
7. `PdfBase64` / `pdf_base64`
8. `LabelBase64` / `label_base64`

## Failure Responses And Conditions

Missing config / credentials:

```json
{
  "success": false,
  "message": "Overseas Logistic credentials are not configured."
}
```

Token HTTP error:

```json
{
  "success": false,
  "message": "Overseas Logistic token generation failed."
}
```

Token body missing:

```json
{
  "success": false,
  "message": "No bearer token found in Overseas Logistic authentication response."
}
```

Missing shipment data:

```json
{
  "success": false,
  "message": "No consignee information found for this shipment."
}
```

```json
{
  "success": false,
  "message": "No package dimensions found for this shipment."
}
```

HTTP error from shipment/create:

```json
{
  "success": false,
  "message": "Overseas Logistic API returned error.",
  "data": {
    "errors": {
      "ReceiverZipcode": ["Invalid zipcode"]
    }
  },
  "request_payload": {},
  "status_code": 422
}
```

HTTP 200 but body `Status: false` (confirmed boolean failure shape):

```json
{
  "success": false,
  "message": "Service not available for the requested destination.",
  "data": {
    "Status": false,
    "Error": "Service not available for the requested destination."
  },
  "request_payload": {}
}
```

HTTP 200 but body `status: "ERROR"` (string failure shape, legacy/alternate):

```json
{
  "success": false,
  "message": "Overseas Logistic API returned an error status.",
  "data": {
    "status": "ERROR",
    "message": "Service not available for the requested destination."
  },
  "request_payload": {}
}
```

Exception:

```json
{
  "success": false,
  "message": "Overseas Logistic API call failed: exception message"
}
```

## Routing

Carrier selection happens in `manifestShipment()` (single) and `bulkManifestShipments()` (bulk). The Overseas Logistic check runs at **Priority 0**, before the PostShipping (Priority 1), Flying Tigers, Ship Global, and UPS routes.

Routing helper `isCanadaOverseasMethod($shippingMethod)` matches when the upper-cased method contains **both**:

- `UNITED CANADA`
- one of `DDP`, `E-COMMERCE`, `ECOMMERCE`, or `E COMMERCE`

On success the flow:

1. Calls `callOverseasLogisticApiFromDb($shipper)`.
2. Extracts the tracking number via `extractOverseasTrackingNumber()`.
3. Stores the tracking record in `shipment_trackings` (`ShipmentTracking::updateOrCreate`).
4. Updates the shipper status to `manifested`.
5. Creates a `Tracking` status record.
6. Logs the status change via `ShipmentLog::logStatus`.
7. (Bulk only) appends the result to the `success` list returned to the caller.

## Country Code Helper

`getOverseasCountryCode($destination, $fallback = 'CA')` normalizes the `consignee_info.delivery_destination` string into an ISO-3166 alpha-2 code:

| Destination contains | Returned code |
| --- | --- |
| `CANADA` / `CA` | `CA` |
| `UK` / `GB` / `UNITED KINGDOM` / `GREAT BRITAIN` | `GB` |
| `US` / `USA` / `UNITED STATE` | `US` |
| (anything else / empty) | `fallback` (`CA` by default) |
