# Discounts & Promotional Coupons မော်ဂျူးအား ဖယ်ရှားခြင်းနှင့် Inventory လုပ်ငန်းစဉ် အပြည့်အစုံ ဆောင်ရွက်ပြီးစီးမှု မှတ်တမ်း (Documentation on Removing Promotions Module & Completing Inventory Requirements)

---

## ၁။ အကျဉ်းချုပ် (Executive Summary)

စနစ်အသုံးပြုသူ (Admin / Restaurant Owner) ၏ ညွှန်ကြားချက်အရ Restaurant POS စနစ်တွင် မလိုအပ်တော့သော **"Discounts & Promotional Coupons"** Sidebar Menu နှင့် ၎င်းနှင့်သက်ဆိုင်သော စာမျက်နှာ (Web Routes, Controllers, Views) များကို ဘေးကင်းလုံခြုံစွာ အပြီးတိုင် ဖယ်ရှားခဲ့ပါသည်။ ထို့အပြင် **Inventory & Stock Management (`/admin/inventory`)** မော်ဂျူး၏ လိုအပ်ချက်များ (Requirements) အားလုံးအား သတ်မှတ်ထားသော စည်းမျဉ်းများနှင့်အညီ အပြည့်အစုံ အောင်မြင်စွာ အကောင်အထည်ဖော် ဆောင်ရွက်ပြီးစီးခဲ့ပါသည်။

---

## ၂။ အဘယ်ကြောင့် ဖယ်ရှားရသနည်း (Why Remove Discounts & Coupons Module?)

မြန်မာနိုင်ငံရှိ စားသောက်ဆိုင်နှင့် ကော်ဖီဆိုင်များ (Restaurants & Cafes in Myanmar) ၏ လက်တွေ့ POS ငွေရှင်းကောင်တာ လုပ်ငန်းစဉ်အရ:

1. **စနစ်ရိုးရှင်းလွယ်ကူစေရန် (Operational Simplicity & Cashier Speed)**:
   - စားသောက်ဆိုင်များတွင် စားသုံးသူများသည် ရှုပ်ထွေးသော Online Promo Voucher Code များ ရိုက်ထည့်ခြင်းထက် ငွေရှင်းချိန်တွင် တိုက်ရိုက် ရာခိုင်နှုန်း လျှော့စျေး (Item or Bill Direct Discount) ပေးခြင်းကိုသာ ပိုမို ဦးစားပေး အသုံးပြုကြပါသည်။
   - မလိုအပ်သော Menu များနှင့် Page များကို ဖယ်ရှားလိုက်ခြင်းဖြင့် Admin Sidebar သည် ပိုမို ရှင်းလင်းကျစ်လျစ်သွားပြီး မရှိမဖြစ်လိုအပ်သော Stock၊ Tables၊ Orders နှင့် Reports များကို လျင်မြန်စွာ ရွေးချယ်နိုင်စေပါသည်။
2. **စနစ်ဝန်ပေါ့စေရန် (Reduced Maintenance & Code Cleanliness)**:
   - အသုံးမပြုသော Controller (`PromotionController`)၊ Form Requests နှင့် Blade Views များကို ဖယ်ရှားလိုက်ခြင်းဖြင့် codebase အရှုပ်အထွေးကင်းပြီး system security မျက်နှာပြင် ပိုမို သန့်ရှင်းသွားပါသည်။
3. **Admin Dashboard ရှင်းလင်းမှု (Decluttered Dashboard)**:
   - Admin Dashboard ရှိ မော်ဂျူးကတ်များတွင်လည်း Discounts & Promos ကတ်ကို ဖယ်ရှားပေးခဲ့သဖြင့် အဓိက အရောင်း၊ ကုန်ပစ္စည်းစာရင်းနှင့် ငွေပေးချေမှုများကိုသာ အာရုံစိုက် စီမံနိုင်ပါသည်။

---

## ၃။ အကောင်အထည်ဖော် ဖယ်ရှားခဲ့သော အဆင့်များ (Implementation Details)

### ၃.၁။ Navigation & UI ဖယ်ရှားခြင်း
- **Sidebar Navigation (`resources/views/admin/layouts/app.blade.php`)**:
  - `Discounts & Promos` menu item (`ti ti-discount-2`) အား Sidebar မှ ဖယ်ရှားခဲ့ပါသည်။
- **Admin Dashboard (`resources/views/admin/dashboard.blade.php`)**:
  - Module shortcut card များထဲမှ `Discounts & Promos` ကတ်ပြားအား ဖယ်ရှားခဲ့ပါသည်။
- **Views**:
  - `resources/views/admin/promotions/index.blade.php` စာမျက်နှာအား စနစ်မှ ဖယ်ရှားရှင်းလင်းခဲ့ပါသည်။

### ၃.၂။ Backend Controller, Requests & Routes ဖယ်ရှားခြင်း
- **Routes (`routes/web.php`)**:
  - အသုံးမပြုတော့သော `/promotions` web routes (Index, Store, Update, Toggle, Destroy) အား ဖယ်ရှားခဲ့ပါသည်။
  - မလိုအပ်တော့သော `use App\Http\Controllers\Web\PromotionController;` import အား ရှင်းလင်းခဲ့ပါသည်။
- **Controllers & Form Requests**:
  - `app/Http/Controllers/Web/PromotionController.php`
  - `app/Http/Requests/Admin/Promotions/StorePromotionRequest.php`
  - `app/Http/Requests/Admin/Promotions/UpdatePromotionRequest.php`

### ၃.၃။ Automated Test Suites များ ပြန်လည်မွမ်းမံခြင်း
- `tests/Feature/Admin/OwnerDashboardAnalyticsTest.php`:
  - မော်ဂျူးလမ်းကြောင်းများ စစ်ဆေးရာမှ ဖယ်ရှားလိုက်သော `'admin.promotions.index'` ကို ဖြုတ်ပယ်ခဲ့ပါသည်။
- `tests/Feature/Admin/MenuInventoryPromotionPermissionTest.php`:
  - ဖယ်ရှားလိုက်သော promotion route စစ်ဆေးချက်များကို ဖြုတ်ပယ်ပြီး Menu နှင့် Inventory RBAC စစ်ဆေးချက်များအဖြစ် ပြင်ဆင်ဖွဲ့စည်းခဲ့ပါသည်။
- **စစ်ဆေးပြီးစီးမှု အခြေအနေ**:
  - `./vendor/bin/pint --test` : 100% Passed.
  - `php artisan test` : **165 tests passed, 0 failures, 917 assertions**.

---

## ၄။ ပြီးစီးသွားသော Inventory & Stock Management စနစ်၏ ထူးခြားချက်များ (Completed Inventory Highlights)

1. **Form Request & API Resource (Rule 4)**:
   - `RestockInventoryItemRequest` နှင့် `InventoryItemResource` အပြည့်အစုံ ချိတ်ဆက်ထားခြင်း။
2. **ငွေကြေးစနစ် MMK (Rule 6)**:
   - ကုန်ပစ္စည်းတန်ဖိုး စုစုပေါင်း (Valuation) နှင့် ဈေးနှုန်းအားလုံးကို ဒသမကိန်းမပါသော မြန်မာကျပ်ငွေ (`MMK`) သာ အသုံးပြုထားခြင်း။
3. **မြန်မာတိုင်းတာမှုစနစ် (Burmese Units)**:
   - စားသောက်ဆိုင်သုံး ပိဿာ (`viss`) ယူနစ်အား kg, g, pcs, bottle, can, liter တို့နှင့်အတူ ထည့်သွင်းပေးထားခြင်း။
4. **Interactive Admin Help Hub**:
   - Stock Alert အရောင်သင်္ကေတများ၊ Stock Value တွက်ချက်ပုံများနှင့် Reorder Level သတ်မှတ်ပုံများကို ချက်ချင်းဖတ်ရှုနိုင်သော Drawer ထည့်သွင်းထားခြင်း။
5. **Quick Restock Modal & Audit Logs**:
   - Invoice နံပါတ်၊ ပေးသွင်းသူနှင့် အသစ်ထည့်သွင်းမည့် အရေအတွက်ကို ကလစ်တစ်ချက်ဖြင့် ဖြည့်သွင်းနိုင်ပြီး Audit Trail တွင် အလိုအလျောက် မှတ်တမ်းတင်ပေးခြင်း။
