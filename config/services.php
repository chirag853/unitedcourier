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

    'cashfree' => [
        'verification_base_url' => env(
            'CASHFREE_VERIFICATION_BASE_URL',
            'https://api.cashfree.com/verification'
        ),
        'verification_client_id' => env('CASHFREE_VERIFICATION_CLIENT_ID'),
        'verification_client_secret' => env('CASHFREE_VERIFICATION_CLIENT_SECRET'),
        'verification_timeout' => (int) env('CASHFREE_VERIFICATION_TIMEOUT', 30),
        'pg' => [
            'base_url' => env('CASHFREE_PG_BASE_URL', 'https://sandbox.cashfree.com/pg'),
            'mode' => env('CASHFREE_PG_MODE', 'sandbox'),
            'orders_endpoint' => env('CASHFREE_PG_ORDERS_ENDPOINT', '/orders'),
            'client_id' => env('CASHFREE_PG_CLIENT_ID'),
            'client_secret' => env('CASHFREE_PG_CLIENT_SECRET'),
            'api_version' => env('CASHFREE_PG_API_VERSION', '2025-01-01'),
            'return_url' => env('CASHFREE_PG_RETURN_URL'),
            'webhook_secret' => env('CASHFREE_PG_WEBHOOK_SECRET'),
            'timeout' => (int) env('CASHFREE_PG_TIMEOUT', 30),
        ],
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
        'tracking_url' => env('POSTSHIPPING_TRACKING_URL'),
        'tracking_token' => env('POSTSHIPPING_TRACKING_TOKEN', env('POSTSHIPPING_API_TOKEN')),
        'tracking_auth_header' => env('POSTSHIPPING_TRACKING_AUTH_HEADER', 'token'),
        'tracking_auth_prefix' => env('POSTSHIPPING_TRACKING_AUTH_PREFIX', ''),
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
        'tracking_url' => env('FLYINGTIGERS_TRACKING_URL'),
        'tracking_token' => env('FLYINGTIGERS_TRACKING_TOKEN', env('FLYINGTIGERS_AUTH_TOKEN')),
        'tracking_auth_header' => env('FLYINGTIGERS_TRACKING_AUTH_HEADER', 'AuthToken'),
        'tracking_auth_prefix' => env('FLYINGTIGERS_TRACKING_AUTH_PREFIX', ''),
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
        'base_url' => env('OVERSEAS_BASE_URL', 'https://api.overseaslogistic.com'),
        'token_url' => env('OVERSEAS_TOKEN_URL', 'https://api.overseaslogistic.com/token'),
        'shipment_url' => env('OVERSEAS_SHIPMENT_URL', 'https://api.overseaslogistic.com/api/shipment/create'),
        'tracking_url' => env('OVERSEAS_TRACKING_URL', 'https://api.overseaslogistic.com/api/tracking/{tracking_number}'),
        'username' => env('OVERSEAS_USERNAME', 'F25064758DAE48CBABC95DE709DCC253'),
        'password' => env('OVERSEAS_PASSWORD', 'J2YTXPW1MGMGWOGW6DS7H9RA0A7EVONWYAJGMR5FFST1HCEEVZTVQ7L19SISZIDT'),
        'account_code' => env('OVERSEAS_ACCOUNT_CODE', 'PR-U02'),
        'timeout' => (int) env('OVERSEAS_TIMEOUT', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Adomantra CMS API
    |--------------------------------------------------------------------------
    | Customer master sync endpoint called when a customer's KYC is approved.
    | Endpoint: https://adomantra.united.cmswebservice.in/api/shipment/customer
    */
    'adomantra' => [
        'base_url' => env('ADOMANTRA_BASE_URL', 'https://adomantra.united.cmswebservice.in'),
        'endpoint' => env('ADOMANTRA_ENDPOINT', '/api/shipment/customer'),
        'order_endpoint' => env('ADOMANTRA_ORDER_ENDPOINT', '/api/shipment/order_create'),
        'timeout' => (int) env('ADOMANTRA_TIMEOUT', 30),
        'connect_timeout' => (int) env('ADOMANTRA_CONNECT_TIMEOUT', 10),
        'retries' => (int) env('ADOMANTRA_RETRIES', 2),
        'retry_delay' => (int) env('ADOMANTRA_RETRY_DELAY', 500),
    ],

    /*
    |--------------------------------------------------------------------------
    | Primus Logistics API
    |--------------------------------------------------------------------------
    | Authentication uses the OAuth password grant with form-encoded
    | credentials. The returned access token is used as a Bearer token.
    */
    'primus' => [
        'base_url' => env('PRIMUS_BASE_URL', 'http://api.primuslogistics.in'),
        'token_url' => env('PRIMUS_TOKEN_URL', 'http://api.primuslogistics.in/token'),
        'tracking_url' => env(
            'PRIMUS_TRACKING_URL',
            'http://api.primuslogistics.in/api/Track/GetTrackings'
        ),
        'tracking_token' => env('PRIMUS_TRACKING_TOKEN'),
        'tracking_authorization' => env('PRIMUS_TRACKING_AUTHORIZATION', ''),
        'tracking_auth_header' => env('PRIMUS_TRACKING_AUTH_HEADER', 'Authorization'),
        'tracking_auth_prefix' => env('PRIMUS_TRACKING_AUTH_PREFIX', 'Bearer'),
        'shipment_url' => env(
            'PRIMUS_SHIPMENT_URL',
            'http://api.primuslogistics.in/api/ShippingFedEx/AddShipment'
        ),
        'username' => env('PRIMUS_USERNAME'),
        'password' => env('PRIMUS_PASSWORD'),
        'grant_type' => env('PRIMUS_GRANT_TYPE', 'password'),
        'account_code' => env('PRIMUS_ACCOUNT_CODE', env('PRIMUS_USERNAME')),
        'access_key' => env('PRIMUS_ACCESS_KEY', env('PRIMUS_PASSWORD')),
        'customer_name' => env('PRIMUS_CUSTOMER_NAME', 'UNITED WORLDWIDE COURIERS PVT LTD'),
        'timeout' => (int) env('PRIMUS_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | ShipUniversal API
    |--------------------------------------------------------------------------
    | Shipment creation uses HTTP Basic Auth to obtain a Bearer token. Tracking
    | uses a separately configured direct Bearer token.
    */
    'shipuniversal' => [
        'base_url' => env('SHIPUNIVERSAL_BASE_URL', 'https://apiv2.shipuniversal.com'),
        'token_url' => env('SHIPUNIVERSAL_TOKEN_URL', 'https://apiv2.shipuniversal.com/token'),
        'shipment_url' => env('SHIPUNIVERSAL_SHIPMENT_URL', 'https://apiv2.shipuniversal.com/api/shipment/create'),
        'tracking_url' => env('SHIPUNIVERSAL_TRACKING_URL', 'https://apiv2.shipuniversal.com/api/tracking/{tracking_number}'),
        'tracking_token' => env('SHIPUNIVERSAL_TRACKING_TOKEN'),
        'username' => env('SHIPUNIVERSAL_USERNAME'),
        'password' => env('SHIPUNIVERSAL_PASSWORD'),
        'grant_type' => env('SHIPUNIVERSAL_GRANT_TYPE', 'client_credentials'),
        'account_code' => env('SHIPUNIVERSAL_ACCOUNT_CODE', 'SU0119'),
        'timeout' => (int) env('SHIPUNIVERSAL_TIMEOUT', 60),
    ],

    'shipglobal' => [
        'tracking_url' => env('SHIPGLOBAL_TRACKING_URL'),
        'tracking_token' => env('SHIPGLOBAL_TRACKING_TOKEN'),
        'tracking_auth_header' => env('SHIPGLOBAL_TRACKING_AUTH_HEADER', 'Authorization'),
        'tracking_auth_prefix' => env('SHIPGLOBAL_TRACKING_AUTH_PREFIX', 'Bearer'),
        'timeout' => (int) env('SHIPGLOBAL_TIMEOUT', 60),
    ],

    'ups' => [
        'client_id' => env('UPS_CLIENT_ID'),
        'client_secret' => env('UPS_CLIENT_SECRET'),
        'token_url' => env('UPS_TOKEN_URL', 'https://onlinetools.ups.com/security/v1/oauth/token'),
        'tracking_url' => env('UPS_TRACKING_URL', 'https://onlinetools.ups.com/api/track/v1/details/{tracking_number}'),
        'tracking_token' => env('UPS_TRACKING_TOKEN'),
        'transaction_id' => env('UPS_TRACKING_TRANSACTION_ID'),
        'transaction_src' => env('UPS_TRANSACTION_SOURCE', 'Production'),
        'timeout' => (int) env('UPS_TRACKING_TIMEOUT', 30),
    ],

    'tracking' => [
        'timeout' => (int) env('SHIPMENT_TRACKING_TIMEOUT', 30),
        'retries' => (int) env('SHIPMENT_TRACKING_RETRIES', 2),
        'retry_delay' => (int) env('SHIPMENT_TRACKING_RETRY_DELAY', 500),
        'chunk_size' => (int) env('SHIPMENT_TRACKING_CHUNK_SIZE', 100),
    ],

];
