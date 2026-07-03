# Delhivery API Documentation

## Purpose

Delhivery is called from the admin delivery assignment flow to create a pickup/shipment.

Source: `app/Http/Controllers/AdminController.php`, method `callDelhiveryApi($shipmentId)`.

## Endpoint

`POST https://track.delhivery.com/api/cmu/create.json`

Headers:

```http
Accept: application/json
Authorization: Token DELHIVERY_TOKEN
Content-Type: application/x-www-form-urlencoded
```

Recommended env variables:

```env
DELHIVERY_CREATE_URL=https://track.delhivery.com/api/cmu/create.json
DELHIVERY_TOKEN=your_delhivery_token
DELHIVERY_PICKUP_LOCATION=your_pickup_location_name
```

Current code note: token and pickup location name are hardcoded in `AdminController::callDelhiveryApi()` and should be moved to `.env`.

## Form Payload

The API expects form-encoded fields:

```json
{
  "format": "json",
  "data": "JSON_STRING"
}
```

Decoded `data` JSON:

```json
{
  "shipments": [
    {
      "name": "Sender Company",
      "add": "Sender full address",
      "pin": "110037",
      "city": "Delhi",
      "state": "Delhi",
      "country": "India",
      "phone": "9999999999",
      "order": "INV001",
      "payment_mode": "prepaid",
      "quantity": 1,
      "weight": 2.5,
      "total_amount": 1000,
      "products_desc": "Product description",
      "cod_amount": 0,
      "shipping_mode": "Surface",
      "shipment_width": 10,
      "shipment_length": 10,
      "shipment_height": 10,
      "end_date": "2026-07-10 10:00:00"
    }
  ],
  "pickup_location": {
    "name": "PICKUP_LOCATION_NAME"
  }
}
```

Payload sources:

| Field | Source |
| --- | --- |
| shipment invoice | `shipment_invoice` |
| shipper details | `shipper_info` |
| consignee details | `consignee_info` |
| package dimensions | `package_dimension` |
| product description | `shipment_invoice_items.description` |

Defaults:

| Field | Default |
| --- | --- |
| `payment_mode` | `prepaid` |
| `quantity` | `1` |
| `cod_amount` | `0` unless payment mode is `COD` |
| `shipping_mode` | `Surface` |
| `end_date` | current date + 7 days |
| address | `Address not provided` if shipper address is empty |

## Success Response

HTTP 2xx plus no body-level `success: false` is treated as success.

```json
{
  "success": true,
  "packages": [
    {
      "waybill": "1234567890",
      "status": "Success",
      "remarks": []
    }
  ]
}
```

Internal wrapper:

```json
{
  "success": true,
  "message": "Delhivery pickup created successfully.",
  "data": {
    "success": true,
    "packages": [
      {
        "waybill": "1234567890",
        "status": "Success"
      }
    ]
  }
}
```

## Failure Responses And Conditions

Shipment not found:

```json
{
  "success": false,
  "message": "Shipment data not found for Delhivery API call."
}
```

HTTP 200 but body failure:

```json
{
  "success": false,
  "message": "Duplicate order id - duplicate order (Status: Fail)",
  "data": {
    "success": false,
    "rmk": "Duplicate order id",
    "packages": [
      {
        "status": "Fail",
        "remarks": ["duplicate order"]
      }
    ]
  }
}
```

HTTP error:

```json
{
  "success": false,
  "message": "Delhivery API returned error.",
  "data": {
    "error": "Invalid token"
  },
  "status_code": 401
}
```

Exception:

```json
{
  "success": false,
  "message": "Delhivery API call failed: exception message"
}
```

Error message extraction priority:

1. top-level `error`
2. top-level `message`
3. top-level `rmk`
4. package-level `remarks`
5. package-level `status: "Fail"`

