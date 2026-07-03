# Delivery / Flying Tigers API Documentation

## Purpose

The Delivery/Flying Tigers API is used for shipments with shipping method `UNITED ECO POST`. The payload sent to Flying Tigers uses `Service: "Uniuni"`, so UniUni-specific payload details are also documented in `Uni_API.md`.

## Endpoint

`POST https://app.flyingtigers.in/api/Shipment/CustomerBookingAPI`

Config:

```env
FLYINGTIGERS_BASE_URL=https://app.flyingtigers.in
FLYINGTIGERS_ENDPOINT=/api/Shipment/CustomerBookingAPI
FLYINGTIGERS_CLIENT_CODE=your_client_code
FLYINGTIGERS_USER_CODE=your_user_code
FLYINGTIGERS_AUTH_TOKEN=your_auth_token
FLYINGTIGERS_TIMEOUT=60
```

Headers:

```http
Content-Type: application/json
Accept: application/json
ClientCode: FLYINGTIGERS_CLIENT_CODE
UserCode: FLYINGTIGERS_USER_CODE
AuthToken: FLYINGTIGERS_AUTH_TOKEN
```

## Routing Condition

This API is called only when the resolved shipping method contains:

```text
UNITED ECO POST
```

## Request Payload

```json
{
  "shipmentType": "Forward",
  "consigneeCountry": "US",
  "RefNo": "INV-REF-001",
  "BookingDate": "03-Jul-2026",
  "Consignee": "Receiver Name",
  "ConsigneePhoneNo": "9999999999",
  "ConsigneeAddress1": "Receiver address line 1",
  "ConsigneePinCode": "10001",
  "ConsigneeState": "NY",
  "ConsigneeCity": "New York",
  "BusinessType": "B2C",
  "Vendor": "USPS Work",
  "Service": "Uniuni",
  "PickupPoint": "2",
  "addPacketDetailList": [
    {
      "BoxWeight": "0.500",
      "BoxLength": "10.00",
      "BoxWidth": "10.00",
      "BoxHeight": "10.00",
      "InvoiceNo": "INV001",
      "InvoiceDate": "03-Jul-2026",
      "boxInvoiceDetails": [
        {
          "ProductName": "T-shirt",
          "UnitPrice": "10.00",
          "Quantity": "1"
        }
      ]
    }
  ],
  "PackageType": "NONDOC",
  "currencyCode": "INR"
}
```

Important code behavior:

| Field | Rule |
| --- | --- |
| `consigneeCountry` | currently hardcoded to `US` |
| `BookingDate` | current date in `d-M-Y` format |
| `RefNo` | invoice reference, else shipper AWB, else `FT-{shipper_id}` |
| `Consignee` | `consignee_name`, else `contact_person` |
| `ConsigneeAddress1` | `address_line1`; if empty, line2 + line3 |
| `InvoiceNo` | invoice number, else AWB, else `INV-{shipper_id}` |
| `InvoiceDate` | invoice date in `d-M-Y`, else current date |
| `currencyCode` | invoice currency, else `INR` |
| `PackageType` | `NONDOC` |

Package defaults:

| Field | Fallback |
| --- | --- |
| `BoxWeight` | `0.500` |
| `BoxLength` / `BoxWidth` / `BoxHeight` | `1.00` |
| Item `UnitPrice` | `unit_rate`, else `amount / qty`, else `5.00` |
| Item `Quantity` | `1` |
| Item `ProductName` | `General Merchandise` |

Invoice item mapping:

- Items are matched to packages by `box_no` using 1-based package index.
- If no item matches a box and it is the first package, all invoice items are used.
- If no invoice items exist, one fallback item is generated.

## Success Response

The integration accepts several possible success shapes. Example:

```json
{
  "status": "SUCCESS",
  "message": "Shipment created",
  "TrackingNumber": "FT123456789",
  "LabelURL": "https://app.flyingtigers.in/label/FT123456789.pdf"
}
```

Internal wrapper:

```json
{
  "success": true,
  "message": "Flying Tigers shipment created successfully.",
  "data": {
    "status": "SUCCESS",
    "TrackingNumber": "FT123456789",
    "LabelURL": "https://app.flyingtigers.in/label/FT123456789.pdf"
  }
}
```

Tracking extraction checks these keys at top level, first array item, under `data`, or under wrappers `Shipments`, `shipments`, `Shipment`, `shipment`, `Result`, `result`:

```text
TrackingNumber, tracking_number, WaybillNumber, waybill_number,
AwbNumber, awb_number, ConsignmentNumber, consignment_number,
OrderNumber, order_number, ShipmentNumber, shipment_number,
ReferenceNo, reference_no, RefNo, BookingId, booking_id,
Waybill, waybill, Reference, reference, ShipmentId, shipment_id
```

Label extraction checks:

```text
LabelURL, label_url, LabelUrl, labelurl, Label, label,
PdfUrl, pdf_url, LabelLink, label_link
```

## Failure Responses And Conditions

Missing DB data:

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

HTTP error:

```json
{
  "success": false,
  "message": "Flying Tigers API returned error.",
  "data": {
    "message": "Validation failed"
  },
  "status_code": 400,
  "is_address_error": false
}
```

HTTP 200 but body status is `ERROR`:

```json
{
  "success": false,
  "message": "Ref no already exists.",
  "data": {
    "status": "ERROR",
    "message": "Ref no already exists."
  },
  "is_address_error": false
}
```

Address-related failure:

```json
{
  "success": false,
  "message": "Cannot create order: Provided address appears to be incorrect or incomplete.",
  "data": {},
  "is_address_error": true
}
```

Address error detection searches error message, JSON response, and raw body for:

```text
address appears to be incorrect
address appears to be incomplete
address is incorrect
address is incomplete
incorrect or incomplete address
address incorrect or incomplete
provided address appears to be incorrect
provided address appears to be incomplete
```

When `is_address_error` is true, the system can offer UNITED CLASSIC / Ship Global fallback.

