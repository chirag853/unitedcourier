# UniUni API Documentation

## Purpose

UniUni is the service selected inside the Flying Tigers booking payload for `UNITED ECO POST` shipments. There is no separate UniUni endpoint in the current code; UniUni is invoked by sending `Service: "Uniuni"` to the Flying Tigers CustomerBookingAPI.

Related doc: `Delivery_API.md`

## Endpoint

Same as Flying Tigers:

`POST https://app.flyingtigers.in/api/Shipment/CustomerBookingAPI`

Headers:

```http
Content-Type: application/json
Accept: application/json
ClientCode: FLYINGTIGERS_CLIENT_CODE
UserCode: FLYINGTIGERS_USER_CODE
AuthToken: FLYINGTIGERS_AUTH_TOKEN
```

## Credentials

Use Flying Tigers credentials:

```env
FLYINGTIGERS_CLIENT_CODE=your_client_code
FLYINGTIGERS_USER_CODE=your_user_code
FLYINGTIGERS_AUTH_TOKEN=your_auth_token
```

## UniUni-Specific Payload Fields

```json
{
  "BusinessType": "B2C",
  "Vendor": "USPS Work",
  "Service": "Uniuni",
  "PickupPoint": "2",
  "PackageType": "NONDOC"
}
```

Full request payload:

```json
{
  "shipmentType": "Forward",
  "consigneeCountry": "US",
  "RefNo": "UWC123456",
  "BookingDate": "03-Jul-2026",
  "Consignee": "Receiver Name",
  "ConsigneePhoneNo": "9999999999",
  "ConsigneeAddress1": "Receiver address",
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
          "ProductName": "General Merchandise",
          "UnitPrice": "5.00",
          "Quantity": "1"
        }
      ]
    }
  ],
  "PackageType": "NONDOC",
  "currencyCode": "INR"
}
```

## Conditions

| Condition | Behavior |
| --- | --- |
| Shipping method contains `UNITED ECO POST` | Build Flying Tigers payload with `Service: "Uniuni"`. |
| Missing consignee | Return build failure. |
| Missing packages | Return build failure. |
| API returns address error | Offer Ship Global / UNITED CLASSIC fallback. |
| API returns `status: "ERROR"` | Treat as failure even if HTTP status is 200. |

## Success Response

Accepted response can be top-level, array item, or nested under `data`, `Shipment`, `Shipments`, `Result`.

```json
{
  "status": "SUCCESS",
  "message": "Order created",
  "OrderNumber": "UNI123456",
  "TrackingNumber": "UNI_TRACK_123456",
  "LabelURL": "https://label-url.example/label.pdf"
}
```

Internal manifest success:

```json
{
  "success": true,
  "message": "Shipment manifested successfully via Flying Tigers!",
  "tracking_number": "UNI_TRACK_123456",
  "network": "Flying Tigers",
  "api_response": {}
}
```

## Failure Response

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

Address fallback preview response:

```json
{
  "success": true,
  "is_address_error": true,
  "shipper_id": 123,
  "classic_service": "UNITED CLASSIC",
  "classic_rate": 1000,
  "paid_amount": 900,
  "difference": 100,
  "wallet_action": "deduct",
  "wallet_amount": 100,
  "wallet_balance": 5000,
  "total_weight": 2.5
}
```

