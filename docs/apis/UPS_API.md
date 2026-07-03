# UPS API Documentation

## Purpose

UPS is the default shipment creation API for non-DPD, non-UNITED ECO POST, and non-ShipGlobal routes. It creates a UPS shipment and returns a tracking number plus label data.

## Endpoints

### OAuth Token

`POST https://onlinetools.ups.com/security/v1/oauth/token`

Headers/auth:

```http
Authorization: Basic base64(UPS_CLIENT_ID:UPS_CLIENT_SECRET)
Content-Type: application/x-www-form-urlencoded
```

Payload:

```json
{
  "grant_type": "client_credentials"
}
```

Success response:

```json
{
  "access_token": "UPS_ACCESS_TOKEN",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

Error conditions:

```json
{
  "success": false,
  "message": "Unable to retrieve UPS Ship access token"
}
```

The token is cached under `ups_ship_access_token` until about 60 seconds before expiry.

### Create Shipment

`POST https://onlinetools.ups.com/api/shipments/v2403/ship`

Headers:

```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer UPS_ACCESS_TOKEN
transId: ship_UNIQUE_ID
transactionSrc: unitedcourier
```

## Credentials

Expected secure source:

```env
UPS_CLIENT_ID=your_ups_client_id
UPS_CLIENT_SECRET=your_ups_client_secret
UPS_SHIPPER_NUMBER=your_ups_shipper_number
```

Current code note: `getUpsAccessToken()` reads `UPS_CLIENT_ID` and `UPS_CLIENT_SECRET`; `getUpsShipAccessToken()` currently contains hardcoded ship credentials and should be moved to `.env`.

## Service Mapping

| Service/method contains | UPS description |
| --- | --- |
| `UNITED MY DELIVERY` | Ground |
| `UNITED AIR PREMIUM` | Next Day Air |
| `UNITED GRD PREMIUM` | 2nd Day Air |
| `UNITED AIR EXPRESS` | Worldwide Express |
| `UNITED PRIOR POST` | Standard |
| `UNITED ECO POST` | Saver |
| `UNITED MY PICKUP` | Ground |
| `DDP AIREXPRESS` / `DDU AIREXPRESS` | Worldwide Express |

Weight/package rules:

| Courier service weight column | UPS weight unit | Service code logic |
| --- | --- | --- |
| `LBS` | `LBS` | Use courier service `scode`. |
| `OZS` | `OZS` | Use courier service `scode`. |
| `OZS/LBS` | Dynamic | If max package weight is under 1 lb: code `92`, unit `OZS`; otherwise code `93`, unit `LBS`. |

Shipper account selection:

| Condition | Shipper number |
| --- | --- |
| Service weight contains `OZS` | `X19700` |
| Otherwise | `1255AK` |

## Request Payload

Example shipment payload:

```json
{
  "ShipmentRequest": {
    "Request": {
      "RequestOption": "validate",
      "TransactionReference": {
        "CustomerContext": "ORDER-12345"
      }
    },
    "Shipment": {
      "Shipper": {
        "Name": "SANDEEP KAPUR",
        "AttentionName": "United",
        "CompanyDisplayableName": "UWC",
        "Phone": { "Number": "6466741258" },
        "ShipperNumber": "1255AK",
        "Address": {
          "AddressLine": "218 WEST 37 STREET 6TH FLOOR",
          "City": "NEW YORK",
          "StateProvinceCode": "NY",
          "PostalCode": "10018",
          "CountryCode": "US"
        }
      },
      "ShipFrom": {
        "Name": "SANDEEP KAPUR",
        "AttentionName": "United",
        "Phone": { "Number": "6466741258" },
        "Address": {
          "AddressLine": ["218 WEST 37 STREET 6TH FLOOR"],
          "City": "NEW YORK",
          "StateProvinceCode": "NY",
          "PostalCode": "10018",
          "CountryCode": "US"
        }
      },
      "ShipTo": {
        "Name": "Receiver Name",
        "AttentionName": "Receiver Name",
        "Phone": { "Number": "9999999999" },
        "Address": {
          "AddressLine": ["Address line 1", "Address line 2"],
          "City": "Los Angeles",
          "StateProvinceCode": "CA",
          "PostalCode": "90001",
          "CountryCode": "US"
        }
      },
      "PaymentInformation": {
        "ShipmentCharge": {
          "Type": "01",
          "BillShipper": {
            "AccountNumber": "1255AK"
          }
        }
      },
      "Service": {
        "Code": "03",
        "Description": "Ground"
      },
      "Package": [
        {
          "Description": "Documents",
          "Packaging": { "Code": "02" },
          "ReferenceNumber": [
            { "Code": "9S", "Value": "ORDER12345" }
          ],
          "PackageWeight": {
            "UnitOfMeasurement": { "Code": "LBS" },
            "Weight": "2.20"
          },
          "Dimensions": {
            "UnitOfMeasurement": { "Code": "IN" },
            "Length": "10",
            "Width": "8",
            "Height": "4"
          }
        }
      ]
    },
    "LabelSpecification": {
      "LabelImageFormat": { "Code": "PDF" }
    }
  }
}
```

Important data sources:

| Payload field | Source |
| --- | --- |
| `ShipTo.*` | `consignee_info` |
| `Package[]` | `package_dimension` |
| `Service.Code` | `courier_services.scode`, with OZS/LBS override |
| `Service.Description` | Method mapping above |
| `CountryCode` | `getCountryCodeFromDestination()` |

## Success Response

Code treats success as HTTP 2xx plus `ShipmentResponse`.

```json
{
  "success": true,
  "shipmentResponse": {
    "Response": {
      "ResponseStatus": {
        "Code": "1",
        "Description": "success"
      }
    },
    "ShipmentResults": {
      "ShipmentIdentificationNumber": "1Z9999999999999999",
      "ShipmentCharges": {
        "TotalCharges": {
          "CurrencyCode": "USD",
          "MonetaryValue": "10.00"
        }
      },
      "BillingWeight": {
        "UnitOfMeasurement": { "Code": "LBS" },
        "Weight": "2.20"
      },
      "PackageResults": [
        {
          "TrackingNumber": "1Z9999999999999999",
          "ShippingLabel": {
            "ImageFormat": { "Code": "PDF" },
            "GraphicImage": "BASE64_LABEL"
          }
        }
      ]
    }
  }
}
```

Stored tracking fields:

| DB field | Value |
| --- | --- |
| `response_status_code` | `ShipmentResponse.Response.ResponseStatus.Code` |
| `response_status_description` | `ShipmentResponse.Response.ResponseStatus.Description` |
| `shipment_identification_number` | `ShipmentResults.ShipmentIdentificationNumber` |
| `total_charges_currency` | `ShipmentCharges.TotalCharges.CurrencyCode` |
| `total_charges_amount` | `ShipmentCharges.TotalCharges.MonetaryValue` |
| `billing_weight_uom` | `BillingWeight.UnitOfMeasurement.Code` |
| `billing_weight` | `BillingWeight.Weight` |
| `package_results` | `ShipmentResults.PackageResults` |
| `raw_response` | full UPS response |

## Failure Responses And Conditions

Token failure:

```json
{
  "success": false,
  "message": "Failed to obtain UPS Ship access token: Unable to retrieve UPS Ship access token"
}
```

cURL/network failure:

```json
{
  "success": false,
  "message": "UPS Ship API connection error: connection timeout"
}
```

Non-JSON response:

```json
{
  "success": false,
  "message": "UPS Ship returned non-JSON response",
  "rawResponse": "raw response body"
}
```

UPS validation/business error:

```json
{
  "success": false,
  "message": "UPS validation error message",
  "rawResponse": {
    "response": {
      "errors": [
        { "code": "ERROR_CODE", "message": "UPS validation error message" }
      ]
    }
  }
}
```

Error message extraction priority:

1. `response.errors[0].message`
2. `ShipmentResponse.Response.Error[0].ErrorDescription`
3. `Fault.detail.Errors.ErrorDetail.PrimaryErrorCode.ErrorDescription`
4. `Failed to create UPS shipment`

