# Menu, Product, Inventory, Coupon & RBAC Permissions Architecture (မြန်မာဘာသာဖြင့် မှတ်တမ်း)

ဤမှတ်တမ်းသည် Restaurant POS စနစ်၏ အဓိက လုပ်ငန်းဆောင်တာများဖြစ်သော **Menu & Product Management**၊ **Inventory Management**၊ **Coupons & Promotional Discounts** များကို ဖန်တီးတည်ဆောက်ခြင်းနှင့် **Spatie RBAC Permissions များကို တိကျမှန်ကန်စွာ ပြုပြင်သတ်မှတ်ခြင်း** နှင့် ပတ်သက်၍ အသေးစိတ်နှင့် "အဘယ်ကြောင့် ဤသို့ ပြုလုပ်ရသနည်း (Why)" ကို ရှင်းလင်းတင်ပြထားပါသည်။

---

## ၁။ အဘယ်ကြောင့် ဤ Module များနှင့် Permissions များကို ပြုပြင်သတ်မှတ်ရသနည်း (Why)

စားသောက်ဆိုင်တစ်ဆိုင်၏ နေ့စဉ်လည်ပတ်မှုတွင် အောက်ပါကဏ္ဍများသည် အပြန်အလှန် ချိတ်ဆက်နေပါသည်-
1. **Menu & Product Management**:
   - အစားအသောက်နှင့် အဖျော်ယမကာများကို Category (ဥပမာ- ဟင်းလျာများ၊ အအေးနှင့်ဖျော်ရည်များ၊ အချိုပွဲများ) အလိုက် စနစ်တကျ ခွဲခြားထားနိုင်ရန်။
   - နေ့စဉ် ပစ္စည်းကုန်သွားပါက စားဖိုဆောင်မှ ကုန်သွားသော ဟင်းလျာများကို "86 Out of Stock" အဖြစ် ချက်ချင်းသတ်မှတ်နိုင်ပြီး စားပွဲထိုးများ မှားယွင်းမှာယူခြင်းမရှိစေရန်။
2. **Inventory Management**:
   - မီးဖိုချောင်သုံး ကုန်ကြမ်းပစ္စည်းများ (ဆန်၊ ဆီ၊ အသား၊ ဟင်းသီးဟင်းရွက်) ၏ လက်ကျန်ပမာဏ (Current Stock) ကို တိုင်းတာမှုယူနစ် (kg, g, liter, pcs) များဖြင့် မှတ်တမ်းတင်ထားနိုင်ရန်။
   - ပစ္စည်းလက်ကျန်နည်းပါးသွားချိန်တွင် သတိပေးနိုင်ရန် အနိမ့်ဆုံးသတ်မှတ်ချက် (Low Stock Safety Threshold / Alert Level) ထည့်သွင်းထားသဖြင့် ကုန်ကြမ်းပြတ်လပ်မှုကြောင့် ဆိုင်ရောင်းအား မထိခိုက်စေရန်။
3. **Coupons & Promotional Discounts**:
   - ဆိုင်ရှင် သို့မဟုတ် မန်နေဂျာမှ စားသုံးသူများအတွက် Promotion Coupon Code များ (ရာခိုင်နှုန်း % လျှော့စျေး သို့မဟုတ် တိကျသောငွေပမာဏ လျှော့စျေး) ပြုလုပ်ပေးနိုင်ရန်။
   - အနည်းဆုံး သုံးစွဲရမည့်ဘေလ်ပမာဏ (Minimum Order Amount)၊ အသုံးပြုနိုင်သည့် ကာလ (Start/End Date) နှင့် အကြိမ်ရေကန့်သတ်ချက် (Usage Limit) တို့ကို ထိန်းချုပ်နိုင်ရန်။
4. **Permissions Fix (လုပ်ပိုင်ခွင့်အာဏာ တိကျစွာ ပြင်ဆင်ခြင်း)**:
   - မူလက Menu၊ Inventory နှင့် Promotions များကို `role:OWNER` သာ ဝင်ရောက်နိုင်ခဲ့သဖြင့် ဆိုင်ခွဲမန်နေဂျာ (Operations Manager) များအနေဖြင့် စားဖိုဆောင်စာရင်း သို့မဟုတ် ဟင်းလျာစျေးနှုန်းများကို ဝင်ရောက်စီမံခန့်ခွဲခွင့် မရရှိခဲ့ပါ။
   - ယခုအခါ Spatie Permission စနစ်တွင် `manage-inventory`, `manage-promotions`, `apply-discounts` ခွင့်ပြုချက်အသစ်များကို ထည့်သွင်းပေးပြီး **MANAGER** အား စားဖိုဆောင်၊ Menu၊ ပစ္စည်းလက်ကျန်နှင့် Promotion စီမံခန့်ခွဲခွင့်များကို ပေးအပ်ထားပါသည်။
   - **CASHIER** အား ကောင်တာတွင် ဘေလ်ရှင်းချိန်၌ အထူးလျှော့စျေးများ ထည့်သွင်းနိုင်ရန် `apply-discounts` လုပ်ပိုင်ခွင့် ပေးအပ်ထားပြီး၊ ဆိုင်၏ အဓိက စာရင်းဇယားပြင်ဆင်ခွင့်များကို မပါဝင်စေဘဲ လုံခြုံစွာ တားဆီးထားပါသည်။

---

## ၂။ Spatie RBAC Permissions Matrix အသစ် ပြင်ဆင်ချက်များ

| Role | Menu (`manage-menu`) | Tables (`manage-tables`) | Inventory (`manage-inventory`) | Promotions (`manage-promotions`) | POS Checkout (`pos-checkout`) | Take Orders (`take-orders`) | Apply Discounts (`apply-discounts`) | Staff HR (`manage-staff`) | Financials & Settings |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **👑 OWNER** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ (Full Unrestricted) |
| **💼 MANAGER** | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ (Executive Protected) |
| **💳 CASHIER** | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ | ❌ | ❌ |
| **🍽️ STAFF** | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ |

---

## ၃။ Database Schema & Architecture

### (က) `categories` နှင့် `products` Tables (`2026_09_22_000004_create_categories_and_products_tables.php`)
- `categories`: `id`, `restaurant_id`, `name`, `slug`, `description`, `is_active`, `sort_order`
- `products`: `id`, `restaurant_id`, `category_id`, `name`, `code`, `description`, `price`, `cost_price`, `image_url`, `is_available`, `preparation_time`

### (ခ) `inventory_items` Table (`2026_09_22_000005_create_inventory_items_table.php`)
- `inventory_items`: `id`, `restaurant_id`, `name`, `sku`, `unit` (`kg`, `liter`, `pcs`, etc.), `current_stock`, `min_stock_alert`, `unit_cost`, `supplier_name`, `is_active`

### (ဂ) `promotions` Table (`2026_09_22_000006_create_promotions_table.php`)
- `promotions`: `id`, `restaurant_id`, `code`, `name`, `description`, `type` (`PERCENTAGE`, `FIXED`), `value`, `min_order_amount`, `max_discount_amount`, `start_date`, `end_date`, `usage_limit`, `used_count`, `is_active`

---

## ၄။ Form Requests & API Resources စည်းမျဉ်း လိုက်နာမှု

စီမံကိန်း၏ Mandatory Rules အရ Controller ထဲတွင် validation logic များ တိုက်ရိုက်မရေးဘဲ သီးသန့် Form Request classes များနှင့် လုံခြုံသော API Resources များကို အောက်ပါအတိုင်း ဖွဲ့စည်းထားပါသည်-

1. **Form Requests**:
   - `StoreCategoryRequest`: Category နာမည်နှင့် ဖော်ပြချက် စစ်ဆေးခြင်း။
   - `StoreProductRequest` / `UpdateProductRequest`: ဟင်းလျာစျေးနှုန်း၊ ကုဒ်နှင့် မိမိဆိုင်ခွဲအတွင်းရှိ category ဖြစ်ကြောင်း စစ်ဆေးခြင်း။
   - `StoreInventoryItemRequest` / `UpdateInventoryItemRequest`: ကုန်ကြမ်းပစ္စည်းယူနစ်၊ လက်ကျန်ပမာဏနှင့် အနိမ့်ဆုံးသတိပေးချက် စစ်ဆေးခြင်း။
   - `StorePromotionRequest` / `UpdatePromotionRequest`: ဆိုင်ခွဲအလိုက် ထပ်တူမကျသော Coupon code၊ လျှော့စျေးပမာဏနှင့် သက်တမ်းကုန်ဆုံးရက် စစ်ဆေးခြင်း။
2. **API Resources**:
   - `CategoryResource`, `ProductResource`, `InventoryItemResource`, `PromotionResource` (အချက်အလက်များကို သန့်ရှင်းလုံခြုံစွာ format ပြုလုပ်ပေးခြင်း)။

---

## ၅။ စစ်ဆေးပြီးစီးမှုဆိုင်ရာ အထောက်အထားများ (Verification Evidence)

အောက်ပါ Automated Tests အားလုံး အောင်မြင်စွာ အတည်ပြုပြီးဖြစ်ပါသည်-

```bash
php artisan test --filter=MenuInventoryPromotionPermissionTest
# [PASS] 7 tests, 29 assertions

php artisan test
# [PASS] 61 tests, 300 assertions (100% Passed)

./vendor/bin/pint --test
# [PASS] Style check passed with 0 errors
```
