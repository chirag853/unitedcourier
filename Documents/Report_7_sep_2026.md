# United Courier — Daily Work Report
**Date:** 7 September 2026 (07-Sep-2026)

---

## 1. Wallet Payment Refactor — Shipment Charge Flow

### Problem
Payment (wallet debit) pehle **Pay Now** button par ho jata tha — manifest se pehle hi. Agar manifest fail hota tha, toh paisa pehle hi kat chuka hota tha aur user ko refund ka wait karna padta tha.

### Changes Made
- [`CustomerController.php`](app/Http/Controllers/CustomerController.php) — `payNow()` se **direct deduction hata diya**; ab sirf manifest ke **success** hone par charge hota hai.
- **Naya helper:** `chargeShipmentIfNotPaid(ShipperInfo, customerId)` — manifest success ke baad wallet se amount cut karta hai, aur `new_balance` return karta hai.
- **Naya helper:** `getShipmentChargeInfo(ShipperInfo, customerId)` — charge se pehle amount/shipping method resolve karta hai.
- `manifestShipment()` ke **saare success branches** mai charge call + `new_balance` add kiya.
- `bulkManifestShipments()` ke success entries mai bhi charge + `new_balance` add kiya.
- `executeShipGlobalFallback()` — unpaid case mai full classic total charge karta hai.
- `getFlyingTigersAddressErrorFallbackInfo()` — unpaid case ka preview bhi update kiya.
- `cancelShipment()` + `cancelShipmentByShipperId()` — refunds ab **actual WalletTransaction** se guard hote hain (duplicate refund nahi hota).

### Failure Handling (Revert to Draft)
- **Naya helper:** `revertReadyToDraftOnManifestFailure(ShipperInfo, customerId, previousStatus)`.
- `manifestShipment()` ke **saare failure returns** par call kiya — agar shipment `ready` status se manifest hote time fail ho, toh wapas **Draft** revert ho jata hai (paisa bhi charge nahi hota).
- `bulkManifestShipments()` ke failed/address-error items par bhi revert handling.
- Blade JS mai rows ab manifest failure par **Draft** revert ho jati hain (`ready` shipments ke liye).

### Bug Fix — Duplicate Debits (Root Cause Analysis)
- **Diagnosis:** Aaj manifest success ke baad wallet **cut nahi** ho raha tha.
- **Root cause:** Shipper `id=8` (AWB `UWC26090700001`) ke liye **14 duplicate wallet debits** already exist kar rahe the (₹2,425 × 14) — pichhle already_charged checks ki wajah se `already_charged` flag miss ho raha tha.
- **Fix (code):**
  - `getShipmentChargeInfo()` se `already_charged` WalletTransaction check **hata diya**.
  - `chargeShipmentIfNotPaid()` se `already_charged` early-return **hata diya**.
- **Data:** User ne kaha — sirf explain karo, abhi data change mat karo. Cleanup pending hai (user request par).

### Verified
- `php -l` (syntax check) — pass
- `php artisan view:cache` — pass

---

## 2. Tracking Number Column — View All Shipments

### Requirement
User ne kaha: "view-all-shipments mai packed or manifest mai tracking number bhi show krwana hai" → phir "mujhey tracking number ka alag column chahiye".

### Changes Made
- [`resources/views/customer/view-all-shipments.blade.php`](resources/views/customer/view-all-shipments.blade.php):
  - Header mai Order Date ke baad **`<th class="tracking-col">Tracking Number</th>`** add kiya.
  - Row mai **`<td class="tracking-col">`** cell add kiya — `shipperInfo.shipmentTracking.shipment_identification_number` se value dikhati hai; nahi hai toh `-`.
  - **CSS:** `.tracking-col` (width 180px, min-width 160px, max-width 220px, word-break wrap) add kiya.
  - Pehle wala sub-row approach remove kar diya (user ne column mangwaya).
- [`CustomerController.php`](app/Http/Controllers/CustomerController.php) — `viewAllShipments()` mai `shipperInfo.shipmentTracking` eager-load (pehle se exist karta tha, verify kiya).

### Verified
- `php artisan view:cache` — pass

---

## 3. Migration Status Check

### Question
"Koi migration kiya h database m?" / "migrations mai kya kya changes kiye hai?"

### Answer
- **Koi nayi migration create nahi hui** — aaj aur is saare kaam mai.
- `git diff HEAD -- database/migrations/` → **empty** (content diff ZERO).
- PowerShell blob-hash compare (har migration file HEAD vs working tree) → **"NO CONTENT DIFF IN ANY MIGRATION"**.
- Jo columns chahiye the (tracking number, wallet_transactions, status, awb_number) — sab pehle se maujood the.
- **Database mai koi naya migration chalaane ki zaroorat nahi hai.**

---

## 4. Git / OneDrive Investigation

### Question
"Aaj ke jitne bhi file mai changes kiye hai vo btao" / "admin, customer wale folder mai kon kon si files mai changes hue hai?"

### Findings
- Last commit aaj: `6e25512` "commited by chirag" (10:26 AM, KYC document uploads).
- `git status` mai **bahut si files** `M` dikh rahi thi — but content hash-compare par **1 byte bhi change nahi** tha.
- **Root cause:** Project OneDrive folder mai hai; OneDrive file timestamps (mtime) chhedta rehta hai → git ko false "modified" dikhta hai.
- **Real content-diff files (only 6):**
  1. `app/Http/Controllers/AdminController.php`
  2. `app/Http/Controllers/CustomerController.php`
  3. `resources/views/admin/customer-profile.blade.php`
  4. `resources/views/customer/dashboard.blade.php`
  5. `resources/views/customer/my-profile.blade.php`
  6. `resources/views/customer/view-all-shipments.blade.php`
- **Backup views** (`views_backup_13_august_2026`) — content diff ZERO (sirf timestamp false positive).
- **Untracked files aaj:**
  - `app/Http/Middleware/RedirectPendingKyc.php`
  - `inspect_charge.php` (temporary debug script — deletion pending)
  - 7 custom-label PDFs `public/uploads/custom_labels/uwc26090700001-*.pdf`
  - `Documents/Shipment Lifecycle Changes_7_sep_2026.docx`

### Recommendation
- Ye `M` false alarms harmless hain — `git diff HEAD` khali hai, wahi sach hai.
- **Long-term:** Project ko OneDrive se bahar move karo (e.g. `C:\dev\unitedcourier`) taaki sync/timestamp issues na aaye.

---

## 5. Pending / On-Request Items
- [ ] **Data cleanup:** Shipper `id=8` (AWB `UWC26090700001`) ke **14 duplicate wallet debits** (₹2,425 × 14) cleanup — user ke go-ahead ka wait.
- [ ] **Delete** temporary [`inspect_charge.php`](inspect_charge.php) script.
- [ ] (Optional) `network` field add karna 7 manifest failure returns + per-provider JS manifest messages — user ke go-ahead ka wait.

---

*Report generated: 07-Sep-2026*
