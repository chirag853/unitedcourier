URL: https://onlinetools.ups.com/api/shipments/v2403/ship
username = GSTEuQdy5XHnWalxGQECH4yhSqJAiydVNjho6AkPGn1ZwMYX
Password = fVuQ8CMYIzxpABWkZFcOM3AyW0x4i1zo7mwiZk7gyLjpD1IWawoCXa3OXWNfVjao


response
{
    "token_type": "Bearer",
    "issued_at": "1778494530186",
    "client_id": "ymhAYxZP0UnFxj8TLYORnJNSF52TDhSz2fjAMRS5h6YjmckM",
    "access_token": "eyJraWQiOiIzOTNjOTE2Yi02OTdhLTRiMmMtYmUxMy0xNWU4ZjM5ZDUxZDQiLCJ0eXAiOiJKV1QiLCJhbGciOiJSUzM4NCJ9.eyJzdWIiOiJ1bml0ZWR3b3JsZHdpZGU3NjlAZ21haWwuY29tIiwiY2xpZW50aWQiOiJ5bWhBWXhaUDBVbkZ4ajhUTFlPUm5KTlNGNTJURGhTejJmakFNUlM1aDZZam1ja00iLCJpc3MiOiJodHRwczovL2FwaXMudXBzLmNvbSIsInV1aWQiOiI1RUVDQzgxNi1FRDg2LTEwMjMtODVFMC00MkE4RkUzMDg1NTEiLCJzaWQiOiIzOTNjOTE2Yi02OTdhLTRiMmMtYmUxMy0xNWU4ZjM5ZDUxZDQiLCJhdWQiOiJVV1dDIiwiYXQiOiJBcU5qeHhXdFVicjVQalh0RHh3anFyMW9oVFlWIiwibmJmIjoxNzc4NDk0NTMwLCJEaXNwbGF5TmFtZSI6IlVXV0MiLCJleHAiOjE3Nzg1MDg5MzAsImlhdCI6MTc3ODQ5NDUzM CwianRpIjoiZjhjMWViMzktNzliNS00NTdhLWE4ZWMtMmI5OTBhM2JiYmExIn0.JUigqnsLyxuBUJpP0GADGtCRu_KOYet1VVS1LXnHOPdzallL7AA59ARVhkILmOuJuOtSN6UW6bj-4UrQ56AlrXqmAxGjzOtro39IQUyQG4EDNQgpdLMUAiGM5ns0vhrnFyT3dbzdOH5ngHjB_ZaCTOiCPAe88Es80KCZVWHYenfD4WpkR7qetu1Uc_vbmWPMmbh9cPZPrGX-G1PZhl9szZDJVukkWxAhe446luhiJWkAC5vAdf83yft7DUaFsQ-WEUNvaBJGZKeLNt-g7LMJSblWEHJyZjVlCynGpAJaZBEN5tgmqi4XJF0yR5VMGKCN09zkll2KvAOCGnF-k2DsHuopcwZ1i8Jg8_xzGzh90oTz2X0FdtTGlce9NKlZ-VsS___OY864_0CmhII5KHo5X9qpmb4RW0STplZz9bQn1uEVgXM01NoJcf3atXHx8wkY5dqfn5qOZ6o79G1y41Ujnv9Q7nRntaqGkxZ4DegYja0ILXgF5zDDhU9K-WbmHUMoX-4WHOhubfofPoserIk0fny7-57cCIdvXSjtkGo3o6g9tYwcRs4bDFxtuka0aLSczostzTnn5TsPgzpL2fS_bUHlvhoWeakMzxp6FlDDIWCz_mqziJsUhQX4EDFeS1S43awlmr1zZec2MyWqLz80PXbeZZynhQlpHmANziEdwU0",
    "expires_in": "14399",
    "status": "approved"
}

==========================================================================================================================================

[{{base_url}}/api/track/v1/details/:inquiryNumber?locale=en_US&returnSignature=false](https://onlinetools.ups.com/api/shipments/v2403/ship)

0auth 2.0

Payload

{
  "ShipmentRequest": {
    "Request": {
      "RequestOption": "nonvalidate",
      "TransactionReference": {
        "CustomerContext": "Shipment Test"
      }
    },
    "Shipment": {
      "Description": "Test Shipment",

      "Shipper": {
        "Name": "SANDEEP KAPUR",
        "AttentionName": "SANDEEP KAPUR",
        "CompanyDisplayableName": "SANDEEP KAPUR",

        "Phone": {
          "Number": "6466741258"
        },

        "ShipperNumber": "1255AK",

        "Address": {
          "AddressLine": [
            "218 WEST 37 STREET",
            "6TH FLOOR"
          ],
          "City": "NEW YORK",
          "StateProvinceCode": "NY",
          "PostalCode": "10018",
          "CountryCode": "US"
        }
      },

      "ShipTo": {
        "Name": "Receiver Name",
        "AttentionName": "Receiver Name",

        "Phone": {
          "Number": "9999999999"
        },

        "Address": {
          "AddressLine": [
            "Receiver Address"
          ],
          "City": "NEW YORK",
          "StateProvinceCode": "NY",
          "PostalCode": "10001",
          "CountryCode": "US"
        }
      },

      "ShipFrom": {
        "Name": "SANDEEP KAPUR",
        "AttentionName": "SANDEEP KAPUR",

        "Phone": {
          "Number": "6466741258"
        },

        "Address": {
          "AddressLine": [
            "218 WEST 37 STREET",
            "6TH FLOOR"
          ],
          "City": "NEW YORK",
          "StateProvinceCode": "NY",
          "PostalCode": "10018",
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

      "Package": {
        "Description": "Documents",

        "Packaging": {
          "Code": "02"
        },

        "Dimensions": {
          "UnitOfMeasurement": {
            "Code": "IN"
          },
          "Length": "10",
          "Width": "8",
          "Height": "4"
        },

        "PackageWeight": {
          "UnitOfMeasurement": {
            "Code": "LBS"
          },
          "Weight": "5"
        }
      }
    },

    "LabelSpecification": {
      "LabelImageFormat": {
        "Code": "GIF"
      }
    }
  }
}



Response
--------------------
{
    "ShipmentResponse": {
        "Response": {
            "ResponseStatus": {
                "Code": "1",
                "Description": "Success"
            },
            "TransactionReference": {
                "CustomerContext": "Shipment Test"
            }
        },
        "ShipmentResults": {
            "ShipmentCharges": {
                "TransportationCharges": {
                    "CurrencyCode": "USD",
                    "MonetaryValue": "23.83"
                },
                "ServiceOptionsCharges": {
                    "CurrencyCode": "USD",
                    "MonetaryValue": "0.00"
                },
                "TotalCharges": {
                    "CurrencyCode": "USD",
                    "MonetaryValue": "23.83"
                }
            },
            "BillingWeight": {
                "UnitOfMeasurement": {
                    "Code": "LBS",
                    "Description": "Pounds"
                },
                "Weight": "5.0"
            },
            "ShipmentIdentificationNumber": "1Z1255AK0306383166",
            "PackageResults": [
                {
                    "TrackingNumber": "1Z1255AK0306383166",
                    "BaseServiceCharge": {
                        "CurrencyCode": "USD",
                        "MonetaryValue": "14.19"
                    },
                    "ServiceOptionsCharges": {
                        "CurrencyCode": "USD",
                        "MonetaryValue": "0.00"
                    },
                    "ShippingLabel": {
                        "ImageFormat": {
                            "Code": "GIF",
                            "Description": "GIF"
                        },
                        "GraphicImage": "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",
                        "HTMLImage": "PCFET0NUWVBFIEhUTUwgUFVCTElDICItLy9JRVRGLy9EVEQgSFRNTCAzLjIvL0VOIj4KPGh0bWw+PGhlYWQ+PHRpdGxlPgpWaWV3L1ByaW50IExhYmVsPC90aXRsZT48bWV0YSBjaGFyc2V0PSJVVEYtOCI+PC9oZWFkPjxzdHlsZT4KICAgIC5zbWFsbF90ZXh0IHtmb250LXNpemU6IDgwJTt9CiAgICAubGFyZ2VfdGV4dCB7Zm9udC1zaXplOiAxMTUlO30KPC9zdHlsZT4KPGJvZHkgYmdjb2xvcj0iI0ZGRkZGRiI+CjxkaXYgY2xhc3M9Imluc3RydWN0aW9ucy1kaXYiPgo8dGFibGUgY2xhc3M9Imluc3RydWN0aW9ucy10YWJsZSIgbmFtZWJvcmRlcj0iMCIgY2VsbHBhZGRpbmc9IjAiIGNlbGxzcGFjaW5nPSIwIiB3aWR0aD0iNjAwIj48dHI+Cjx0ZCBoZWlnaHQ9IjQxMCIgYWxpZ249ImxlZnQiIHZhbGlnbj0idG9wIj4KPEIgY2xhc3M9ImxhcmdlX3RleHQiPlZpZXcvUHJpbnQgTGFiZWw8L0I+CiZuYnNwOzxicj4KJm5ic3A7PGJyPgo8b2wgY2xhc3M9InNtYWxsX3RleHQiPiA8bGk+PGI+UHJpbnQgdGhlIGxhYmVsOjwvYj4gJm5ic3A7ClNlbGVjdCBQcmludCBmcm9tIHRoZSBGaWxlIG1lbnUgaW4gdGhpcyBicm93c2VyIHdpbmRvdyB0byBwcmludCB0aGUgbGFiZWwgYmVsb3cuPGJyPjxicj48bGk+PGI+CkZvbGQgdGhlIHByaW50ZWQgbGFiZWwgYXQgdGhlIGRvdHRlZCBsaW5lLjwvYj4gJm5ic3A7ClBsYWNlIHRoZSBsYWJlbCBpbiBhIFVQUyBTaGlwcGluZyBQb3VjaC4gSWYgeW91IGRvIG5vdCBoYXZlIGEgcG91Y2gsIGFmZml4IHRoZSBmb2xkZWQgbGFiZWwgdXNpbmcgY2xlYXIgcGxhc3RpYyBzaGlwcGluZyB0YXBlIG92ZXIgdGhlIGVudGlyZSBsYWJlbC48YnI+PGJyPjxsaT48Yj5HRVRUSU5HIFlPVVIgU0hJUE1FTlQgVE8gVVBTPC9iPjxicj4KPGI+Q3VzdG9tZXJzIHdpdGggYSBEYWlseSBQaWNrdXA8L2I+PHVsPjxsaT4KWW91ciBkcml2ZXIgd2lsbCBwaWNrdXAgeW91ciBzaGlwbWVudChzKSBhcyB1c3VhbC4gPC91bD4KIDxicj4gCjxiPkN1c3RvbWVycyB3aXRob3V0IGEgRGFpbHkgUGlja3VwPC9iPjx1bD48bGk+VGFrZSB0aGlzIHBhY2thZ2UgdG8gYW55IGxvY2F0aW9uIG9mIFRoZSBVUFMgU3RvcmXCriwgVVBTIERyb3AgQm94LCBVUFMgQ3VzdG9tZXIgQ2VudGVyLCBVUFMgQWxsaWFuY2VzIChPZmZpY2UgRGVwb3TCriBvciBTdGFwbGVzwq4pIG9yIEF1dGhvcml6ZWQgU2hpcHBpbmcgT3V0bGV0IG5lYXIgeW91IG9yIHZpc2l0IDxhIGhyZWY9Imh0dHA6Ly93d3cudXBzLmNvbS9jb250ZW50L3VzL2VuL2luZGV4LmpzeCI+d3d3LnVwcy5jb20vY29udGVudC91cy9lbi9pbmRleC5qc3g8L2E+IGFuZCBzZWxlY3QgRHJvcCBPZmYuPGxpPgpBaXIgc2hpcG1lbnRzIChpbmNsdWRpbmcgV29ybGR3aWRlIEV4cHJlc3MgYW5kIEV4cGVkaXRlZCkgY2FuIGJlIHBpY2tlZCB1cCBvciBkcm9wcGVkIG9mZi4gVG8gc2NoZWR1bGUgYSBwaWNrdXAsIG9yIHRvIGZpbmQgYSBkcm9wLW9mZiBsb2NhdGlvbiwgc2VsZWN0IHRoZSBQaWNrdXAgb3IgRHJvcC1vZmYgaWNvbiBmcm9tIHRoZSBVUFMgdG9vbCBiYXIuICA8L3VsPjwvb2w+PC90ZD48L3RyPjwvdGFibGU+PHRhYmxlIGJvcmRlcj0iMCIgY2VsbHBhZGRpbmc9IjAiIGNlbGxzcGFjaW5nPSIwIiB3aWR0aD0iNjAwIj4KPHRyPgo8dGQgY2xhc3M9InNtYWxsX3RleHQiIGFsaWduPSJsZWZ0IiB2YWxpZ249InRvcCI+CiZuYnNwOyZuYnNwOyZuYnNwOwo8YSBuYW1lPSJmb2xkSGVyZSI+Rk9MRCBIRVJFPC9hPjwvdGQ+CjwvdHI+Cjx0cj4KPHRkIGFsaWduPSJsZWZ0IiB2YWxpZ249InRvcCI+PGhyPgo8L3RkPgo8L3RyPgo8L3RhYmxlPgoKPHRhYmxlPgo8dHI+Cjx0ZCBoZWlnaHQ9IjEwIj4mbmJzcDsKPC90ZD4KPC90cj4KPC90YWJsZT4KCjwvZGl2Pgo8dGFibGUgYm9yZGVyPSIwIiBjZWxscGFkZGluZz0iMCIgY2VsbHNwYWNpbmc9IjAiIHdpZHRoPSI2NTAiID48dHI+Cjx0ZCBhbGlnbj0ibGVmdCIgdmFsaWduPSJ0b3AiPgo8SU1HIFNSQz0iLi9sYWJlbDFaMTI1NUFLMDMwNjM4MzE2Ni5naWYiIGhlaWdodD0iMzkyIiB3aWR0aD0iNjUxIj4KPC90ZD4KPC90cj48L3RhYmxlPgo8L2JvZHk+CjwvaHRtbD4K"
                    },
                    "ItemizedCharges": [
                        {
                            "Code": "376",
                            "CurrencyCode": "USD",
                            "MonetaryValue": "4.50",
                            "SubType": "Rural"
                        },
                        {
                            "Code": 41475157005",
                    "ItemizedCharges": [
                        {
                            "Code": "376",
                            "CurrencyCode": "USD",
                            "Mone


<!-- UPS Ground Saver less then 1 LBS (United Air express) (5-8 tat) -->
https://onlinetools.ups.com/api/shipments/v2403/ship
{
  "ShipmentRequest": {
    "Request": {
      "RequestOption": "validate"
    },
    "Shipment": {
      "Description": "Documents",
      "Shipper": {
        "Name": "Shipper Company Name",
        "AttentionName": "Shipper Contact Person",
        "CompanyDisplayableName": "Shipper Company Name",
        "Phone": {
          "Number": "6466741258"
        },
        "ShipperNumber": "X19700",
        "Address": {
          "AddressLine": [
            "218 WEST 37 STREET 6TH FLOOR"
          ],
          "City": "NEW YORK",
          "StateProvinceCode": "NY",
          "PostalCode": "10018",
          "CountryCode": "US"
        }
      },
      "ShipFrom": {
        "Name": "SANDEEP KAPUR",
        "AttentionName": "United",
        "Phone": {
          "Number": "6466741258"
        },
        "Address": {
          "AddressLine": [
            "218 WEST 37 STREET 6TH FLOOR"
          ],
          "City": "NEW YORK",
          "StateProvinceCode": "NY",
          "PostalCode": "10018",
          "CountryCode": "US"
        }
      },
      "ShipTo": {
        "Name": "Consignee Name",
        "AttentionName": "Consignee Name",
        "Phone": {
          "Number": "2125551234"
        },
        "Address": {
          "AddressLine": [
            "123 Main Street",
            "Suite 210"
          ],
          "City": "NEW YORK",
          "StateProvinceCode": "NY",
          "PostalCode": "10001",
          "CountryCode": "US"
        }
      },
      "PaymentInformation": {
        "ShipmentCharge": {
          "Type": "01",
          "BillShipper": {
            "AccountNumber": "X19700"
          }
        }
      },
      "Service": {
        "Code": "92",
        "Description": "Ground Saver Less than 1 lb"
      },
      "Package": [
        {
          "Description": "Documents",
          "Packaging": {
            "Code": "02"
          },
          "ReferenceNumber": [
            {
              "Code": "PO",
              "Value": "REF123456"
            }
          ],
          "PackageWeight": {
            "UnitOfMeasurement": {
              "Code": "OZS"
            },
            "Weight": "6.4"
          }
        }
      ]
    },
    "LabelSpecification": {
      "LabelImageFormat": {
        "Code": "PDF"
      }
    }
  }
}


<!-- Response -->
{
    "ShipmentResponse": {
        "Response": {
            "ResponseStatus": {
                "Code": "1",
                "Description": "Success"
            },
            "Alert": [
                {
                    "Code": "120058",
                    "Description": "A Delivery Area surcharge has been added to the service cost."
                }
            ],
            "TransactionReference": ""
        },
        "ShipmentResults": {
            "ShipmentCharges": {
                "TransportationCharges": {
                    "CurrencyCode": "USD",
                    "MonetaryValue": "24.83"
                },
                "ServiceOptionsCharges": {
                    "CurrencyCode": "USD",
                    "MonetaryValue": "0.00"
                },
                "TotalCharges": {
                    "CurrencyCode": "USD",
                    "MonetaryValue": "24.83"
                }
            },
            "BillingWeight": {
                "UnitOfMeasurement": {
                    "Code": "OZS",
                    "Description": "Ounces"
                },
                "Weight": "7.0"
            },
            "ShipmentIdentificationNumber": "1ZX19700YN93865497",
            "PackageResults": [
                {
                    "TrackingNumber": "1ZX19700YN93865497",
                    "BaseServiceCharge": {
                        "CurrencyCode": "USD",
                        "MonetaryValue": "13.00"
                    },
                    "ServiceOptionsCharges": {
                        "CurrencyCode": "USD",
                        "MonetaryValue": "0.00"
                    },
                    "SurePostDasCharges": {
                        "CurrencyCode": "USD",
                        "MonetaryValue": "6.55"
                    },
                    "ShippingLabel": {
                        "ImageFormat": {
                            "Code": "PDF",
                            "Description": "PDF"
                        },
                        "GraphicImage": "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Ci9HMyBncwovWDQgRG8KUQoKZW5kc3RyZWFtCmVuZG9iagoyIDAgb2JqCjw8L1R5cGUgL1BhZ2UKL1Jlc291cmNlcyA8PC9Qcm9jU2V0IFsvUERGIC9UZXh0IC9JbWFnZUIgL0ltYWdlQyAvSW1hZ2VJXQovRXh0R1N0YXRlIDw8L0czIDMgMCBSPj4KL1hPYmplY3QgPDwvWDQgNCAwIFI+Pj4+Ci9NZWRpYUJveCBbMCAwIDYxMiA3OTJdCi9Db250ZW50cyA1IDAgUgovU3RydWN0UGFyZW50cyAwCi9QYXJlbnQgNiAwIFI+PgplbmRvYmoKNiAwIG9iago8PC9UeXBlIC9QYWdlcwovQ291bnQgMQovS2lkcyBbMiAwIFJdPj4KZW5kb2JqCjcgMCBvYmoKPDwvVHlwZSAvQ2F0YWxvZwovUGFnZXMgNiAwIFI+PgplbmRvYmoKeHJlZgowIDgKMDAwMDAwMDAwMCA2NTUzNSBmIAowMDAwMDAwMDE1IDAwMDAwIG4gCjAwMDAwMjI1MzUgMDAwMDAgbiAKMDAwMDAwMDA4OCAwMDAwMCBuIAowMDAwMDAwMTI1IDAwMDAwIG4gCjAwMDAwMjIzOTAgMDAwMDAgbiAKMDAwMDAyMjc0NiAwMDAwMCBuIAowMDAwMDIyODAxIDAwMDAwIG4gCnRyYWlsZXIKPDwvU2l6ZSA4Ci9Sb290IDcgMCBSCi9JbmZvIDEgMCBSPj4Kc3RhcnR4cmVmCjIyODQ4CiUlRU9GCg=="
                    },
                    "USPSPICNumber": "92612909839251541400163965",
                    "ItemizedCharges": [
                        {
                            "Code": "376",
                            "CurrencyCode": "USD",
                            "MonetaryValue": "6.55",
                            "SubType": "Rural"
                        },
                        {
                            "Code": "375",
                            "CurrencyCode": "USD",
                            "MonetaryValue": "5.28"
                        }
                    ]
                }
            ]
        }
    }
}



<!-- UPS ground saver 1 LBS and greater (UNited air express ( tat 5-8 days )) -->
https://wwwcie.ups.com/api/shipments/:version/ship

<!-- payloadys -->
{
  "ShipmentRequest": {
    "Request": {
      "RequestOption": "validate"
    },
    "Shipment": {
      "Description": "Documents",
      "Shipper": {
        "Name": "Shipper Company Name",
        "AttentionName": "Shipper Contact Person",
        "CompanyDisplayableName": "Shipper Company Name",
        "Phone": {
          "Number": "6466741258"
        },
        "ShipperNumber": "X19700",
        "Address": {
          "AddressLine": [
            "218 WEST 37 STREET 6TH FLOOR"
          ],
          "City": "NEW YORK",
          "StateProvinceCode": "NY",
          "PostalCode": "10018",
          "CountryCode": "US"
        }
      },
      "ShipFrom": {
        "Name": "SANDEEP KAPUR",
        "AttentionName": "United",
        "Phone": {
          "Number": "6466741258"
        },
        "Address": {
          "AddressLine": [
            "218 WEST 37 STREET 6TH FLOOR"
          ],
          "City": "NEW YORK",
          "StateProvinceCode": "NY",
          "PostalCode": "10018",
          "CountryCode": "US"
        }
      },
      "ShipTo": {
        "Name": "Consignee Name",
        "AttentionName": "Consignee Name",
        "Phone": {
          "Number": "2125551234"
        },
        "Address": {
          "AddressLine": [
            "123 Main Street",
            "Suite 210"
          ],
          "City": "NEW YORK",
          "StateProvinceCode": "NY",
          "PostalCode": "10001",
          "CountryCode": "US"
        }
      },
      "PaymentInformation": {
        "ShipmentCharge": {
          "Type": "01",
          "BillShipper": {
            "AccountNumber": "X19700"
          }
        }
      },
      "Service": {
        "Code": "93",
        "Description": "Ground Saver 1 lbs or grater"
      },
      "Package": [
        {
          "Description": "Documents",
          "Packaging": {
            "Code": "02"
          },
          "ReferenceNumber": [
            {
              "Code": "PO",
              "Value": "REF123456"
            }
          ],
          "PackageWeight": {
            "UnitOfMeasurement": {
              "Code": "LBS"
            },
            "Weight": "1.00"
          }
        }
      ]
    },
    "LabelSpecification": {
      "LabelImageFormat": {
        "Code": "PDF"
      }
    }
  }
}


<!-- response -->
{
    "ShipmentResponse": {
        "Response": {
            "ResponseStatus": {
                "Code": "1",
                "Description": "Success"
            },
            "Alert": [
                {
                    "Code": "120058",
                    "Description": "A Delivery Area surcharge has been added to the service cost."
                }
            ],
            "TransactionReference": ""
        },
        "ShipmentResults": {
            "ShipmentCharges": {
                "TransportationCharges": {
                    "CurrencyCode": "USD",
                    "MonetaryValue": "24.83"
                },
                "ServiceOptionsCharges": {
                    "CurrencyCode": "USD",
                    "MonetaryValue": "0.00"
                },
                "TotalCharges": {
                    "CurrencyCode": "USD",
                    "MonetaryValue": "24.83"
                }
            },
            "BillingWeight": {
                "UnitOfMeasurement": {
                    "Code": "LBS",
                    "Description": "Pounds"
                },
                "Weight": "1.0"
            },
            "ShipmentIdentificationNumber": "1ZX19700YW98258636",
            "PackageResults": [
                {
                    "TrackingNumber": "1ZX19700YW98258636",
                    "BaseServiceCharge": {
                        "CurrencyCode": "USD",
                        "MonetaryValue": "13.00"
                    },
                    "ServiceOptionsCharges": {
                        "CurrencyCode": "USD",
                        "MonetaryValue": "0.00"
                    },
                    "SurePostDasCharges": {
                        "CurrencyCode": "USD",
                        "MonetaryValue": "6.55"
                    },
                    "ShippingLabel": {
                        "ImageFormat": {
                            "Code": "PDF",
                            "Description": "PDF"
                        },
                        "GraphicImage": "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LjAwMDAzIDAgNDQ0LjYwMDA0IDc3OS40MDAwMiBjbQowIDAgMCBSRyAwIDAgMCByZwovRzMgZ3MKL1g0IERvClEKCmVuZHN0cmVhbQplbmRvYmoKMiAwIG9iago8PC9UeXBlIC9QYWdlCi9SZXNvdXJjZXMgPDwvUHJvY1NldCBbL1BERiAvVGV4dCAvSW1hZ2VCIC9JbWFnZUMgL0ltYWdlSV0KL0V4dEdTdGF0ZSA8PC9HMyAzIDAgUj4+Ci9YT2JqZWN0IDw8L1g0IDQgMCBSPj4+PgovTWVkaWFCb3ggWzAgMCA2MTIgNzkyXQovQ29udGVudHMgNSAwIFIKL1N0cnVjdFBhcmVudHMgMAovUGFyZW50IDYgMCBSPj4KZW5kb2JqCjYgMCBvYmoKPDwvVHlwZSAvUGFnZXMKL0NvdW50IDEKL0tpZHMgWzIgMCBSXT4+CmVuZG9iago3IDAgb2JqCjw8L1R5cGUgL0NhdGFsb2cKL1BhZ2VzIDYgMCBSPj4KZW5kb2JqCnhyZWYKMCA4CjAwMDAwMDAwMDAgNjU1MzUgZiAKMDAwMDAwMDAxNSAwMDAwMCBuIAowMDAwMDIxNjg0IDAwMDAwIG4gCjAwMDAwMDAwODggMDAwMDAgbiAKMDAwMDAwMDEyNSAwMDAwMCBuIAowMDAwMDIxNTM5IDAwMDAwIG4gCjAwMDAwMjE4OTUgMDAwMDAgbiAKMDAwMDAyMTk1MCAwMDAwMCBuIAp0cmFpbGVyCjw8L1NpemUgOAovUm9vdCA3IDAgUgovSW5mbyAxIDAgUj4+CnN0YXJ0eHJlZgoyMTk5NwolJUVPRgo="
                    },
                    "USPSPICNumber": "92612909839251541475157005",
                    "ItemizedCharges": [
                        {
                            "Code": "376",
                            "CurrencyCode": "USD",
                            "MonetaryValue": "6.55",
                            "SubType": "Rural"
                        },
                        {
                            "Code": "375",
                            "CurrencyCode": "USD",
                            "MonetaryValue": "5.28"
                        }
                    ]
                }
            ]
        }
    }
      }taryValue": "6.55",
        "SubType": "Rural"
    },
    {
        "Code": "375",
        "CurrencyCode": "USD",
        "MonetaryValue": "5.28"
    }
                    ]
                }
            ]
        }
    }
}