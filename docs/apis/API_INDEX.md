# Courier API Documentation Index

This folder documents the carrier integrations used by the shipment manifest flow.

Source code references:

- Main customer manifest logic: `app/Http/Controllers/customerController.php`
- Admin Delhivery pickup logic: `app/Http/Controllers/AdminController.php`
- Config defaults: `config/services.php`
- Environment examples: `.env.example`

## Files

- `UPS_API.md` - UPS OAuth and Ship API.
- `DPD_POSTSHIPPING_API.md` - DPD UK via PostShipping API.
- `ShipGlobal_API.md` - Ship Global token and add-order API.
- `Delivery_API.md` - Flying Tigers / Delivery API used for UNITED ECO POST.
- `Uni_API.md` - UniUni service payload as sent through Flying Tigers.
- `Overseas_Logistic_API.md` - Overseas Logistic token and shipment-create API used for UNITED CANADA DDP / E-COMMERCE.
- `Delhivery_API.md` - Admin Delhivery pickup creation API.

## Routing Rules

Carrier selection happens during manifest. Routes are evaluated in priority order; the first match wins.

| Priority | Shipping method condition | API used | Notes |
| --- | --- | --- | --- |
| 0 | Contains `UNITED CANADA` with `DDP` or `E-COMMERCE` | Overseas Logistic | Canada-only DDP/E-Commerce flow. Same endpoint for both; `DutyTax` (`DDP`/`DDU`) differentiates. |
| 1 | Contains `UNITED AIR PREMIUM` and `DDP` | DPD/PostShipping | UK-only DPD flow. |
| 2 | Contains `UNITED ECO POST` | Flying Tigers / UniUni | Uses Flying Tigers endpoint; payload service is `Uniuni`. |
| 3 | Contains `UNITED CLASSIC` or Flying Tigers address fallback | Ship Global | Also used as fallback for UNITED ECO POST address errors. |
| 4 | Any other UPS-backed service | UPS Ship API | Default manifest route. |

## Credential Handling

For safety, these docs show credential variable names and placeholder values only. Do not paste live tokens into documentation. Live values should be kept in `.env` or a secure secret manager.

