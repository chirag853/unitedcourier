{{base_url}}/security/v1/oauth/token
username = ymhAYxZP0UnFxj8TLYORnJNSF52TDhSz2fjAMRS5h6YjmckM
Password = 2YyEab9klzMNoFMCchGPXA0ArOodVVicezrOUzkraijMbvpbxWZk7GflB1JwPjoi


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

{{base_url}}/api/track/v1/details/:inquiryNumber?locale=en_US&returnSignature=false

0auth 2.0

Response

{
    "trackResponse": {
        "shipment": [
            {
                "inquiryNumber": "1Z00160W0391111222",
                "shipmentType": "S",
                "shipperNumber": "00160W",
                "pickupDate": "20251208",
                "package": [
                    {
                        "trackingNumber": "1Z00160W0391111222",
                        "deliveryDate": [
                            {
                                "type": "RDD",
                                "date": "20251216"
                            }
                        ],
                        "deliveryTime": {
                            "type": "EOD"
                        },
                        "activity": [
                            {
                                "location": {
                                    "address": {
                                        "city": "Gedgkins",
                                        "stateProvince": "IL",
                                        "countryCode": "US",
                                        "country": "US"
                                    },
                                    "slic": "1119"
                                },
                                "status": {
                                    "type": "I",
                                    "description": "Arrived at Facility",
                                    "code": "AR",
                                    "statusCode": "005"
                                },
                                "date": "20251211",
                                "time": "034100",
                                "gmtDate": "20251211",
                                "gmtOffset": "-06:00",
                                "gmtTime": "09:41:00",
                                "logicalScan": false
                            },
                            {
                                "location": {
                                    "address": {
                                        "city": "Teketh",
                                        "stateProvince": "NY",
                                        "countryCode": "US",
                                        "country": "US"
                                    },
                                    "slic": "1119"
                                },
                                "status": {
                                    "type": "I",
                                    "description": "Departed from Facility",
                                    "code": "DP",
                                    "statusCode": "005"
                                },
                                "date": "20251209",
                                "time": "195900",
                                "gmtDate": "20251210",
                                "gmtOffset": "-05:00",
                                "gmtTime": "00:59:00",
                                "logicalScan": false
                            },
                            {
                                "location": {
                                    "address": {
                                        "city": "Teketh",
                                        "stateProvince": "NY",
                                        "countryCode": "US",
                                        "country": "US"
                                    },
                                    "slic": "1119"
                                },
                                "status": {
                                    "type": "I",
                                    "description": "Arrived at Facility",
                                    "code": "OR",
                                    "statusCode": "160"
                                },
                                "date": "20251209",
                                "time": "160847",
                                "gmtDate": "20251209",
                                "gmtOffset": "-05:00",
                                "gmtTime": "21:08:47",
                                "logicalScan": false
                            },
                            {
                                "location": {
                                    "address": {
                                        "countryCode": "US",
                                        "country": "US"
                                    }
                                },
                                "status": {
                                    "type": "M",
                                    "description": "Shipper created a label, UPS has not received the package yet. ",
                                    "code": "MP",
                                    "statusCode": "003"
                                },
                                "date": "20251208",
                                "time": "114024",
                                "gmtDate": "20251208",
                                "gmtOffset": "-05:00",
                                "gmtTime": "16:40:24",
                                "logicalScan": false
                            }
                        ],
                        "currentStatus": {
                            "description": "On the Way",
                            "code": "005"
                        },
                        "packageAddress": [
                            {
                                "type": "ORIGIN",
                                "address": {
                                    "city": "TACOKLYN",
                                    "stateProvince": "NY",
                                    "countryCode": "US",
                                    "country": "US"
                                }
                            },
                            {
                                "type": "DESTINATION",
                                "address": {
                                    "city": "NO LAKE CITY",
                                    "stateProvince": "UT",
                                    "countryCode": "US",
                                    "country": "US"
                                }
                            }
                        ],
                        "weight": {
                            "unitOfMeasurement": "LBS",
                            "weight": "0.50"
                        },
                        "service": {
                            "code": "518",
                            "levelCode": "003",
                            "description": "UPS Ground"
                        },
                        "referenceNumber": [
                            {
                                "type": "SHIPMENT",
                                "number": "1138-1",
                                "code": "01",
                                "description": "Shipper Assigned General"
                            },
                            {
                                "type": "PACKAGE",
                                "number": "1138-1",
                                "code": "01",
                                "description": "Shipper Assigned General"
                            }
                        ],
                        "deliveryInformation": {
                            "deliveryPhoto": {
                                "isNonPostalCodeCountry": false,
                                "isProximityMapViewable": false
                            }
                        },
                        "taxIndicator": "false",
                        "dimension": {
                            "unitOfDimension": "IN"
                        },
                        "isSmartPackage": false,
                        "packageCount": 1
                    }
                ]
            }
        ]
    }
}