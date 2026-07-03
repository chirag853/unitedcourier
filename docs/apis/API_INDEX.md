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
- `Delhivery_API.md` - Admin Delhivery pickup creation API.

## Routing Rules

Carrier selection happens during manifest:

| Shipping method condition | API used | Notes |
| --- | --- | --- |
| Contains `UNITED AIR PREMIUM` and `DDP` | DPD/PostShipping | UK-only DPD flow. |
| Contains `UNITED ECO POST` | Flying Tigers / UniUni | Uses Flying Tigers endpoint; payload service is `Uniuni`. |
| Contains `UNITED CLASSIC` or Flying Tigers address fallback | Ship Global | Also used as fallback for UNITED ECO POST address errors. |
| Any other UPS-backed service | UPS Ship API | Default manifest route. |

## Credential Handling

For safety, these docs show credential variable names and placeholder values only. Do not paste live tokens into documentation. Live values should be kept in `.env` or a secure secret manager.

