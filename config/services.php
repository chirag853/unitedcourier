<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'sms' => [
        'authkey' => env('SMS_AUTHKEY'),
        'sender_id' => env('SMS_SENDER_ID', '28102'),
        'country_code' => env('SMS_COUNTRY_CODE', '91'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PostShipping (DPD/UK) API
    |--------------------------------------------------------------------------
    | Used for DDP shipments created with the method:
    |   - UNITED AIR PREMIUM DDP
    | Endpoint: https://api.postshipping.com/api2/shipments
    |
    | A single fixed ThirdPartyToken is used for all shipments.
    |
    | ServiceTypeName is weight/parcel/destination-dependent:
    |   - 0-5 Kg           → DPDUKEPND (DPD UK Mainland Express PAK)
    |   - 5-30 Kg          → DPD112   (DPD UK Mainland Next Day)
    |   - Multiple Parcels → MDPD112  (Multi DPD UK MAINLAND- NEXT DAY)
    |   - Offshore          → DPD111   (DPD OFFSHORE- TWO DAY)
    |
    */

    'postshipping' => [
        'base_url' => env('POSTSHIPPING_BASE_URL', 'https://api.postshipping.com'),
        'endpoint' => env('POSTSHIPPING_ENDPOINT', '/api2/shipments'),
        // Fixed ThirdPartyToken sent in the request BODY for all UNITED AIR PREMIUM DDP shipments.
        'third_party_token' => env('POSTSHIPPING_THIRD_PARTY_TOKEN', '32A4D3D985DA8D47020688462C48BB2C'),
        // Separate API token sent in the request HEADER (header name: "token").
        'api_token' => env('POSTSHIPPING_API_TOKEN', '24CC08DDDB5320D9EE702243F838BBE2'),
        'timeout' => (int) env('POSTSHIPPING_TIMEOUT', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Flying Tigers API
    |--------------------------------------------------------------------------
    | Used for shipments created with the method:
    |   - UNITED ECO POST
    | Endpoint: https://app.flyingtigers.in/api/Shipment/CustomerBookingAPI
    |
    | Authentication is performed via request headers:
    |   - ClientCode  (e.g. C10009)
    |   - UserCode    (e.g. C10009)
    |   - AuthToken   (e.g. VsDgjDfKqxRZHhrCt392iZ1RjCvYCMcZMH5AHWIZRDBAOZqJht)
    |
    */

    'flyingtigers' => [
        'base_url' => env('FLYINGTIGERS_BASE_URL', 'https://app.flyingtigers.in'),
        'endpoint' => env('FLYINGTIGERS_ENDPOINT', '/api/Shipment/CustomerBookingAPI'),
        'client_code' => env('FLYINGTIGERS_CLIENT_CODE', 'C10009'),
        'user_code' => env('FLYINGTIGERS_USER_CODE', 'C10009'),
        'auth_token' => env('FLYINGTIGERS_AUTH_TOKEN', 'VsDgjDfKqxRZHhrCt392iZ1RjCvYCMcZMH5AHWIZRDBAOZqJht'),
        'timeout' => (int) env('FLYINGTIGERS_TIMEOUT', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Overseas Logistic API (Canada)
    |--------------------------------------------------------------------------
    | Used for shipments created with the methods:
    |   - UNITED CANADA DDP
    |   - UNITED CANADA E-COMMERCE
    |
    | Two-step flow:
    |   1. Generate Bearer token:  POST https://api.overseaslogistic.com/token
    |      (username/password form or JSON credentials)
    |   2. Create shipment:        POST https://api.overseaslogistic.com/api/shipment/create
    |      (Authorization: Bearer <token>)
    |
    | The same endpoint/payload is used for both DDP and E-Commerce variants;
    | the Service field inside ServiceDetails differentiates the service
    | (e.g. CANADA_YVR_SELF).
    |
    */

    'overseas' => [
        'base_url'      => env('OVERSEAS_BASE_URL', 'https://api.overseaslogistic.com'),
        'token_url'     => env('OVERSEAS_TOKEN_URL', 'https://api.overseaslogistic.com/token'),
        'shipment_url'  => env('OVERSEAS_SHIPMENT_URL', 'https://api.overseaslogistic.com/api/shipment/create'),
        'username'      => env('OVERSEAS_USERNAME', 'F25064758DAE48CBABC95DE709DCC253'),
        'password'      => env('OVERSEAS_PASSWORD', 'J2YTXPW1MGMGWOGW6DS7H9RA0A7EVONWYAJGMR5FFST1HCEEVZTVQ7L19SISZIDT'),
        'account_code'  => env('OVERSEAS_ACCOUNT_CODE', 'PR-U02'),
        'timeout'       => (int) env('OVERSEAS_TIMEOUT', 60),
    ],

];
