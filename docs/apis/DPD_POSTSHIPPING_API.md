# DPD / PostShipping API Documentation

## Purpose

DPD UK shipments are created through the PostShipping API for `UNITED AIR PREMIUM DDP` shipments. DPD services are shown only for UK destinations in the rate flow.

## Endpoint

`POST https://api.postshipping.com/api2/shipments`

Config:

```env
POSTSHIPPING_BASE_URL=https://api.postshipping.com
POSTSHIPPING_ENDPOINT=/api2/shipments
POSTSHIPPING_THIRD_PARTY_TOKEN=your_body_third_party_token
POSTSHIPPING_API_TOKEN=your_header_token
POSTSHIPPING_TIMEOUT=60
```

Headers:

```http
Content-Type: application/json
Accept: application/json
Connection: keep-alive
token: POSTSHIPPING_API_TOKEN
```

Body authentication:

```json
{
  "ThirdPartyToken": "POSTSHIPPING_THIRD_PARTY_TOKEN"
}
```

## Routing Conditions

PostShipping is used only when shipping method:

- contains `DDP`
- contains `UNITED AIR PREMIUM`

Rate filtering:

| Destination | Available services |
| --- | --- |
| `UK - United Kingdom` | DPD/PostShipping services only |
| Any non-UK destination | DPD/PostShipping hidden |

## Service Type Conditions

`ServiceTypeName` is selected in this order:

| Condition | ServiceTypeName | NetworkCode |
| --- | --- | --- |
| Offshore UK destination | `DPD111` | `7` |
| More than 1 parcel | `MDPD112` | `1` |
| Single parcel, total weight <= 5 kg | `DPDUKEPND` | `1` |
| Single parcel, total weight > 5 kg | `DPD112` | `1` |

Offshore detection:

- Postcode starts with `BT`
- Postcode starts with `IV`, `HS`, `KA`, `KW`, `PA`, `PH`, `ZE`, `IM`, `JE`, `GY`
- City/state contains offshore keywords such as `NORTHERN IRELAND`, `HIGHLAND`, `ISLAND`, `ISLE OF MAN`, `JERSEY`, `GUERNSEY`

## Request Payload

The API expects an array of shipment objects.

```json
[
  {
    "ThirdPartyToken": "POSTSHIPPING_THIRD_PARTY_TOKEN",
    "SenderDetails": {
      "SenderName": "Ved",
      "SenderCompanyName": "United Worldwide Couriers Pvt Ltd",
      "SenderCountryCode": "IN",
      "SenderAdd1": "BUILDING NO 1 BYPASS ROAD",
      "SenderAdd2": "MAHIPALPUR",
      "SenderAdd3": "",
      "SenderAddCity": "NEW DELHI",
      "SenderAddState": "DELHI",
      "SenderAddPostcode": "110037",
      "SenderPhone": "01146122222",
      "SenderEmail": "abc@abc.com",
      "SenderFax": "",
      "SenderKycType": "Passport",
      "SenderKycNumber": "P00001",
      "SenderReceivingCountryTaxID": ""
    },
    "ReceiverDetails": {
      "ReceiverName": "Receiver Name",
      "ReceiverCompanyName": "Receiver Name",
      "ReceiverCountryCode": "GB",
      "ReceiverAdd1": "Address line 1",
      "ReceiverAdd2": "Address line 2",
      "ReceiverAdd3": "",
      "ReceiverAddCity": "London",
      "ReceiverAddState": "London",
      "ReceiverAddPostcode": "SW1A1AA",
      "ReceiverMobile": "9999999999",
      "ReceiverPhone": "9999999999",
      "ReceiverEmail": "receiver@example.com",
      "ReceiverAddResidential": "N",
      "ReceiverFax": "",
      "ReceiverKycType": "Passport",
      "ReceiverKycNumber": ""
    },
    "PackageDetails": {
      "GoodsDescription": "General Merchandise",
      "CustomValue": 30,
      "CustomCurrencyCode": "INR",
      "InsuranceValue": 0,
      "InsuranceCurrencyCode": "INR",
      "ShipmentTerm": "",
      "GoodsOriginCountryCode": "IN",
      "Weight": 2.5,
      "WeightMeasurement": "KG",
      "NoOfItems": 1,
      "CubicL": 10,
      "CubicW": 10,
      "CubicH": 10,
      "CubicWeight": 0,
      "ServiceTypeName": "DPDUKEPND",
      "NetworkCode": "1",
      "BookPickUP": false,
      "SenderRef1": "UWC123456",
      "BusinessType": "B2B",
      "ShipmentResponseItem": [
        {
          "ItemNoOfPcs": 1,
          "ItemCubicL": 10,
          "ItemCubicW": 10,
          "ItemCubicH": 10,
          "ItemWeight": 2.5,
          "ItemDescription": "General Merchandise",
          "ItemCustomValue": 30,
          "ItemCustomCurrencyCode": "INR",
          "Notes": "Commercial shipment",
          "Pieces": [
            {
              "HarmonisedCode": "610910",
              "GoodsDescription": "T-shirt",
              "Content": "T-shirt",
              "Quantity": 1,
              "Weight": 2.5,
              "ManufactureCountryCode": "IN",
              "OriginCountryCode": "IN",
              "CurrencyCode": "INR",
              "CustomsValue": 30
            }
          ]
        }
      ],
      "CODAmount": 0,
      "CODCurrencyCode": "INR",
      "DeadWeight": 2.5,
      "ReasonExport": "Sale",
      "OrderNumber": "INV001",
      "Incoterms": "CIF"
    },
    "PickupDetails": {
      "ReadyTime": "2026/07/03 12:30:00",
      "CloseTime": "2026/07/03 15:30:00",
      "SpecialInstructions": "Call before pickup",
      "Address1": "Pickup address 1",
      "Address2": "Pickup address 2",
      "Address3": "",
      "AddressCity": "NEW DELHI",
      "AddressState": "DELHI",
      "AddressPostalCode": "110037",
      "AddressCountryCode": "IN"
    }
  }
]
```

Payload defaults and fallbacks:

| Field | Fallback |
| --- | --- |
| Package weight | `0.5` kg if missing/zero |
| Package dimensions | `10 x 10 x 10` if missing |
| Goods description | first invoice item description, else `General Merchandise` |
| Custom value | invoice amount, else `30.00` |
| Currency | invoice currency, else `INR` |
| Incoterms | invoice incoterms, else `CIF` |
| Pieces | invoice items mapped by `box_no`; first package gets all items if no box mapping |

## Success Response

Documented shape:

```json
[
  {
    "ShipmentNumber": "15501234567890",
    "AlternateRef": "UWC123456",
    "LabelURL": "https://api.postshipping.com/labels/label.pdf",
    "ErrMessage": "",
    "AcccountCode": "ACCOUNT_CODE"
  }
]
```

Internal success wrapper:

```json
{
  "success": true,
  "message": "PostShipping shipment created successfully.",
  "data": [
    {
      "ShipmentNumber": "15501234567890",
      "LabelURL": "https://api.postshipping.com/labels/label.pdf",
      "ErrMessage": ""
    }
  ],
  "request_payload": []
}
```

Tracking extraction priority:

1. `ShipmentNumber`
2. `AlternateRef`
3. common fallback keys: `tracking_number`, `WaybillNumber`, `awb_number`, `ConsignmentNumber`, `OrderNumber`, `waybill`, `reference`, `ShipmentId`

Label extraction priority:

1. `LabelURL`
2. `label_url`
3. `LabelUrl`
4. `label`

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
  "message": "PostShipping API returned error.",
  "data": {
    "error": "Validation failed"
  },
  "request_payload": [],
  "status_code": 400
}
```

HTTP 200 with embedded rejection:

```json
{
  "success": false,
  "message": "PostShipping API rejected shipment: Service Denied (1021) - network code invalid",
  "data": [
    {
      "ShipmentNumber": "",
      "ErrMessage": "Service Denied (1021) - network code invalid"
    }
  ],
  "request_payload": [],
  "status_code": 200
}
```

Embedded error detection scans `ErrMessage` at top level, inside indexed arrays, and under `data`, `shipments`, `Shipment`, `Shipments`, `consignment`, or `consignments`.

