# Ship Global API Documentation

## Purpose

Ship Global is used for `UNITED CLASSIC` shipment creation and as fallback when `UNITED ECO POST` / Flying Tigers returns an address-related error.

## Endpoints

### Step 1: Generate Bearer Token

`POST https://labels.shipglobal.in/api/v1/customers.php`

Headers:

```http
Content-Type: application/json
```

Payload:

```json
{
  "email": "SHIPGLOBAL_EMAIL",
  "password": "SHIPGLOBAL_PASSWORD"
}
```

Recommended env variables:

```env
SHIPGLOBAL_EMAIL=your_shipglobal_email
SHIPGLOBAL_PASSWORD=your_shipglobal_password
SHIPGLOBAL_TOKEN_URL=https://labels.shipglobal.in/api/v1/customers.php
SHIPGLOBAL_ORDER_URL=https://labels.shipglobal.in/api/v1/addOrder.php
```

Current code note: email/password and URLs are hardcoded in `callShipGlobalApiFromDb()` and should be moved to `.env`.

Token extraction supports these response shapes:

```json
{ "token": "TOKEN" }
```

```json
{ "data": { "token": "TOKEN" } }
```

```json
{ "access_token": "TOKEN" }
```

```json
{ "data": { "access_token": "TOKEN" } }
```

### Step 2: Add Order

`POST https://labels.shipglobal.in/api/v1/addOrder.php`

Headers:

```http
Content-Type: application/json
Authorization: Bearer SHIPGLOBAL_TOKEN
```

## Request Payload

```json
{
  "invoice_no": "INV001",
  "invoice_date": "2026-07-03",
  "order_reference": "UWC123456",
  "service": "SERVICE_CODE",
  "package_weight": 500,
  "package_length": 10,
  "package_breadth": 10,
  "package_height": 10,
  "currency_code": "USD",
  "csb5_status": 0,
  "seller_nickname": "UnitedW",
  "seller_firstname": "Sender",
  "seller_lastname": "Name",
  "seller_mobile": "9999999999",
  "seller_email": "sender@example.com",
  "seller_company": "Sender Company",
  "seller_address": "Sender full address",
  "seller_address_2": "Sender full address",
  "seller_city": "Delhi",
  "seller_postcode": "110037",
  "seller_country_code": "IN",
  "seller_state": "Delhi",
  "customer_shipping_firstname": "Receiver",
  "customer_shipping_lastname": "Name",
  "customer_shipping_mobile": "8888888888",
  "customer_shipping_email": "receiver@example.com",
  "customer_shipping_company": "Receiver Name",
  "customer_shipping_address": "Receiver full address",
  "customer_shipping_address_2": "Receiver full address",
  "customer_shipping_city": "New York",
  "customer_shipping_postcode": "10001",
  "customer_shipping_country_code": "US",
  "customer_shipping_state": "NY",
  "vendor_order_items": [
    {
      "vendor_order_item_name": "T-shirt",
      "vendor_order_item_sku": "610910",
      "vendor_order_item_quantity": 1,
      "vendor_order_item_unit_price": 10,
      "vendor_order_item_hsn": "610910",
      "vendor_order_item_tax_rate": 0
    }
  ],
  "tracking": "UWC123456"
}
```

Payload data sources:

| Payload field | Source |
| --- | --- |
| Invoice fields | `shipment_invoice` |
| Seller fields | `shipper_info` |
| Customer shipping fields | `consignee_info` |
| Package fields | first `package_dimension` row |
| `service` | `courier_services.service_code` or `courier_services.scode` |
| `csb5_status` | `customers.csb_status` |
| `vendor_order_items` | `shipment_invoice_items` |

Important conversions/defaults:

| Field | Rule |
| --- | --- |
| `package_weight` | first package `actual_weight_kg * 1000`; fallback currently `0.5` |
| dimensions | first package dimensions; fallback `10` |
| sender/receiver names | split into first and last; if no last name, first name is reused |
| currency | invoice currency, else `USD` |
| order reference | shipper AWB, else invoice reference |
| default item | `General Merchandise` if no invoice items |

## Success Response

The code treats the order as successful when HTTP is 2xx and an `order_number` exists, even if a body-level `success` flag is false.

Supported success shapes:

```json
{
  "success": true,
  "data": {
    "order_number": "SG123456789",
    "waybill_number": "WB123456789",
    "pdf_base64": "BASE64_LABEL"
  }
}
```

```json
{
  "order_number": "SG123456789",
  "waybill_number": "WB123456789"
}
```

Internal wrapper:

```json
{
  "success": true,
  "message": "Ship Global order created successfully. Order#: SG123456789",
  "data": {
    "data": {
      "order_number": "SG123456789",
      "waybill_number": "WB123456789"
    }
  }
}
```

Tracking extraction checks:

1. `data.waybill_number`
2. `waybill_number`
3. `tracking_number`
4. `data.tracking_number`
5. `awb_number`
6. `data.awb_number`
7. `waybill`
8. `data.waybill`
9. `data.order_number`
10. `order_number`

## Failure Responses And Conditions

Token HTTP error:

```json
{
  "success": false,
  "message": "Ship Global token generation failed."
}
```

Token body missing:

```json
{
  "success": false,
  "message": "No bearer token found in Ship Global authentication response."
}
```

Missing shipment data:

```json
{
  "success": false,
  "message": "No consignee information found for this shipment."
}
```

HTTP 200 but no order number:

```json
{
  "success": false,
  "message": "Ship Global API returned no order number.",
  "data": {
    "success": false,
    "message": "Validation failed"
  }
}
```

HTTP error:

```json
{
  "success": false,
  "message": "Ship Global API returned error.",
  "data": {
    "errors": {
      "customer_shipping_postcode": ["Invalid postcode"]
    }
  },
  "status_code": 422
}
```

Exception:

```json
{
  "success": false,
  "message": "Ship Global API call failed: exception message"
}
```

## Flying Tigers Address Fallback

When Flying Tigers detects an address error, the customer can fallback to UNITED CLASSIC / Ship Global. The fallback:

1. Finds a `courier_services.method` containing `UNITED CLASSIC`.
2. Calculates the classic rate.
3. Updates `shipper_info.shipping_method`.
4. Updates `package_dimension.shipping_method`.
5. Calls Ship Global.
6. Stores tracking.
7. Deducts or refunds wallet difference.
8. Updates invoice `total_amount`.

Fallback success response:

```json
{
  "success": true,
  "message": "Shipment manifested successfully via UNITED CLASSIC (Ship Global).",
  "tracking_number": "WB123456789",
  "network": "Ship Global (Fallback)",
  "is_address_error": true,
  "classic_rate": 1000,
  "paid_amount": 900,
  "wallet_action": "deducted",
  "wallet_amount": 100,
  "new_balance": 5000,
  "ship_global_response": {}
}
```

