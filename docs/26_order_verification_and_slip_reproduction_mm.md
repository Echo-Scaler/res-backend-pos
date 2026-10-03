# Order Verification (Kitchen Pass Expediter) & Slip Reproduction System စနစ် ဗိသုကာနှင့် တည်ဆောက်မှု မှတ်တမ်း

## ၁။ နိဒါန်းနှင့် ရည်ရွယ်ချက် (Introduction & Objectives)

စားသောက်ဆိုင်ကြီးများတွင် ဧည့်သည် စားပွဲပေါ်သို့ အစားအသောက်များ မချပေးမီ **အမှားအယွင်း ကင်းစင်စေရန် စစ်ဆေးခြင်း (Expediter Kitchen Pass Verification)** နှင့် Thermal Printer စက္ကူညပ်ခြင်း၊ မှင်ပြတ်ခြင်း သို့မဟုတ် စလစ်ပျောက်ဆုံးခြင်းတို့ ဖြစ်ပေါ်ပါက **စလစ်ကို လုံခြုံစွာ ပြန်လည်ထုတ်ယူခြင်း (Slip Reproduction & Reprint Audit)** စနစ်တို့သည် မရှိမဖြစ် လိုအပ်သော အဓိက လုပ်ငန်းစဉ်များ ဖြစ်ကြပါသည်။

ဤ Module သည် မီးဖိုချောင်မှ ချက်ပြုတ်ပြီးထွက်လာသော အစားအသောက်များကို Waiter များ စားပွဲသို့ မသယ်ဆောင်မီ Tablet သို့မဟုတ် Mobile Screen ပေါ်တွင် အချက်အလက်များ တိုက်ဆိုင်စစ်ဆေးနိုင်ရန်၊ အားလုံးပြည့်စုံမှသာ စားပွဲသို့ပို့ဆောင်နိုင်ရန်နှင့် လိုအပ်ပါက အထောက်အထား ခိုင်လုံစွာဖြင့် စလစ်ကို အသစ်ပြန်လည် ပရင့်ထုတ်နိုင်ရန် တည်ဆောက်ထားသော **Production-Ready Order Verification & Slip Reproduction System** ဖြစ်ပါသည်။

---

## ၂။ အဘယ်ကြောင့် ဤသို့ ဒီဇိုင်းရေးဆွဲရသနည်း? (Architecture Decisions & "Why")

### (၁) အဘယ်ကြောင့် Waiter မသယ်မီ App တွင် အတည်ပြုစစ်ဆေးသည့် အဆင့် (Expediter Verification) ထည့်သွင်းရသနည်း?
- စားသောက်ဆိုင်များတွင် အဖြစ်အများဆုံး ပြဿနာများမှာ:
  1. စားပွဲနံပါတ် မှားပို့ခြင်း (Wrong Table Delivery)
  2. အရေအတွက် မှားယွင်းခြင်း သို့မဟုတ် ဟင်းတစ်ပွဲ ကျန်ရစ်ခဲ့ခြင်း (Missing Dishes)
  3. ဧည့်သည်၏ အထူးမှာကြားချက် (Special Notes/Allergies: ဥပမာ - ငရုတ်သီးမထည့်ရန်၊ သကြားမထည့်ရန်) မီးဖိုချောင်မှ မေ့လျော့ခဲ့ခြင်း။
- ဤစနစ်တွင် **Kitchen Pass Expediter Screen (`/admin/orders/verification`)** ကို ထည့်သွင်းထားသဖြင့် Waiter/Checker သည် ဗန်းပေါ်သို့ ဟင်းပွဲများတင်သည့်အခါ တစ်ပွဲချင်းစီကို Touch နှိပ်၍ အမှန်ခြစ် (Check-off) ပြုလုပ်နိုင်ပါသည်။
- ဟင်းပွဲအားလုံး ပြည့်စုံမှသာ **"Verify & Deliver to Table"** ကို နှိပ်ပြီး စားပွဲသို့ အစားအသောက်နှင့်အတူ **Slip 2 (Customer Bill Slip - 会計伝票)** ကို တွဲလျက် သယ်ဆောင်သွားရမည် ဖြစ်ပါသည်။ စားပွဲအခြေအနေသည်လည်း အလိုအလျောက် `OCCUPIED` (သုံးဆောင်နေဆဲ) သို့ ပြောင်းလဲသွားပါသည်။

### (၂) အဘယ်ကြောင့် စလစ်ပြန်လည်ထုတ်ယူခြင်း (Slip Reproduction) တွင် အကြောင်းပြချက်နှင့် Reprint Count ကို စိစစ်မှတ်တမ်းတင်ရသနည်း?
- စားသောက်ဆိုင်လောကတွင် Thermal Printer စက္ကူကုန်ခြင်း၊ စက္ကူညပ်ခြင်း (Paper Jam) သို့မဟုတ် ရေစိုစုတ်ပြဲခြင်းတို့ကြောင့် စလစ်အသစ် ပြန်ထုတ်ရသော အခြေအနေများ မကြာခဏ ရှိပါသည်။
- သို့သော် ကန့်သတ်ချက်မရှိ စလစ်ပြန်ထုတ်ခွင့်ပြုထားပါက ဝန်ထမ်းမသမာသူများသည် စလစ်ဟောင်းကို သုံး၍ ဧည့်သည်ထံမှ ပိုက်ဆံတောင်းပြီး POS စနစ်ထဲတွင် ငွေမသွင်းဘဲ လိမ်လည်အလွဲသုံးစားပြုလုပ်နိုင်ပါသည်။
- ထို့ကြောင့် ဤစနစ်တွင်:
  1. စလစ်ပြန်ထုတ်တိုင်း `reprint_count` ကို +1 တိုးမြှင့်ပြီး စလစ်ထိပ်တွင် **`*** REPRINT #1 (DUPLICATE) ***`** ဟု ထင်ရှားစွာ ရိုက်နှိပ်ဖော်ပြပါသည်။
  2. စလစ်ပြန်ထုတ်သည့် ဝန်ထမ်း၊ အချိန်နှင့် **အကြောင်းပြချက် (Reason)** ကို `audit_logs` ဇယားတွင် လုံခြုံစွာ Record ရေးသားသိမ်းဆည်းပါသည်။
  3. Kitchen Chit ပြန်ထုတ်မည်လား သို့မဟုတ် Customer Bill ပြန်ထုတ်မည်လားကို သီးသန့် ရွေးချယ်ခွင့် ပေးထားပါသည်။

---

## ၃။ ဒေတာဘေ့စ် ဖွဲ့စည်းပုံ အသစ်များ (Database Schema Extensions)

အောက်ပါ Migration ဖြင့် `orders` ဇယားနှင့် `order_items` ဇယားများတွင် စစ်ဆေးမှုနှင့် စလစ်ထုတ်ယူမှုဆိုင်ရာ Columns များကို ဖြည့်စွက်ထားပါသည် -

```sql
-- orders ဇယားတွင် ဖြည့်စွက်ချက်များ
ALTER TABLE orders ADD COLUMN kitchen_status VARCHAR(50) DEFAULT 'RECEIVED' AFTER status;
ALTER TABLE orders ADD COLUMN reprint_count INT UNSIGNED DEFAULT 0 AFTER kitchen_status;
ALTER TABLE orders ADD COLUMN verified_at TIMESTAMP NULL AFTER reprint_count;
ALTER TABLE orders ADD COLUMN verified_by BIGINT UNSIGNED NULL AFTER verified_at;

-- order_items ဇယားတွင် ဖြည့်စွက်ချက်များ
ALTER TABLE order_items ADD COLUMN special_notes VARCHAR(255) NULL AFTER notes;
ALTER TABLE order_items ADD COLUMN is_cooked BOOLEAN DEFAULT FALSE AFTER special_notes;
ALTER TABLE order_items ADD COLUMN is_verified BOOLEAN DEFAULT FALSE AFTER is_cooked;
```

### စစ်ဆေးမှု အဆင့်အတန်းများ (Kitchen Status Flow):
- 🟡 `RECEIVED`: အော်ဒါလက်ခံရရှိပြီး မီးဖိုချောင်သို့ စလစ်ကျထားသော အခြေအနေ။
- 🔵 `PREPARING`: မီးဖိုချောင်မှ ချက်ပြုတ်ပြင်ဆင်နေသော အခြေအနေ။
- 🟢 `READY_FOR_DELIVERY`: ဟင်းပွဲအားလုံး ချက်ပြုတ်ပြီးစီး၍ Expediter စစ်ဆေးရေးကောင်တာသို့ ရောက်ရှိနေသော အခြေအနေ။
- 🟣 `SERVED_TO_TABLE`: Waiter မှ စစ်ဆေးအတည်ပြုပြီး စားပွဲသို့ အစားအသောက်နှင့် Customer Bill Slip ပို့ဆောင်ပြီးစီးသော အခြေအနေ။

---

## ၄။ တင်းကျပ်သော စည်းမျဉ်းများနှင့်အညီ တည်ဆောက်ထားသော ဖိုင်များ (Strict Rules Compliance)

### (၁) Form Requests (Rule #4)
- [`VerifyOrderItemsRequest`](file:///Users/kyawwaiyan/Documents/my-Home-tech/resturant-pos/backendPos/app/Http/Requests/Order/VerifyOrderItemsRequest.php)
  - `action`: `TOGGLE_ITEM`, `MARK_READY`, `VERIFY_AND_DELIVER` ဟူသော ခွင့်ပြုချက် စိစစ်ခြင်း။
  - `item_id`: Item toggle အတွက် Order နှင့် သက်ဆိုင်သော Item ဟုတ်မဟုတ် စစ်ဆေးခြင်း။
- [`ReprintSlipRequest`](file:///Users/kyawwaiyan/Documents/my-Home-tech/resturant-pos/backendPos/app/Http/Requests/Order/ReprintSlipRequest.php)
  - `slip_type`: `KITCHEN_CHIT` သို့မဟုတ် `CUSTOMER_BILL` သာ ဖြစ်စေရန် ကန့်သတ်စစ်ဆေးခြင်း။
  - `reason`: စလစ်ပြန်ထုတ်ရသည့် အကြောင်းပြချက်ကို အနည်းဆုံး စာလုံး (၅) လုံး မဖြစ်မနေ ထည့်သွင်းစေခြင်း။

### (၂) API Resource (Rule #4)
- [`OrderSlipResource`](file:///Users/kyawwaiyan/Documents/my-Home-tech/resturant-pos/backendPos/app/Http/Resources/Order/OrderSlipResource.php)
  - ဈေးနှုန်းတိုင်းအတွက် `MMK` ငွေကြေးယူနစ်၊ သင်္ကေတ `Ks ` နှင့် ဓသမကင်းစင်သော Formatted String များအဖြစ် ပြောင်းလဲပေးခြင်း။
  - `is_reprint`, `reprint_count`, `watermark` စသော Thermal စလစ်ထုတ်ယူမှုဆိုင်ရာ အချက်အလက်များ ထည့်သွင်းပေးခြင်း။

### (၃) Expediter Controller & Thermal Print Routes
- [`OrderVerificationController`](file:///Users/kyawwaiyan/Documents/my-Home-tech/resturant-pos/backendPos/app/Http/Controllers/Admin/OrderVerificationController.php)
  - `GET /admin/orders/verification`: စားသောက်ဆိုင်အတွင်း စစ်ဆေးရန်လိုအပ်သော အော်ဒါများကို Real-time ကြည့်ရှုနိုင်သော Expediter Pass Screen။
  - `POST /admin/orders/{order}/verify`: ဟင်းပွဲများကို အမှန်ခြစ်ခြင်းနှင့် စားပွဲသို့ ပို့ဆောင်အတည်ပြုခြင်း။
  - `POST /admin/orders/{order}/reprint`: စလစ်ပြန်လည်ထုတ်ယူခွင့် အတည်ပြုခြင်းနှင့် Audit Log မှတ်တမ်းတင်ခြင်း။
  - `GET /admin/orders/{order}/print-kitchen-chit`: 80mm Thermal Kitchen Slip ထုတ်ပေးခြင်း။
  - `GET /admin/orders/{order}/print-customer-bill`: 80mm Thermal Customer Guest Check ထုတ်ပေးခြင်း။

### (၄) Blade Views & 80mm Thermal Printing
- [`resources/views/admin/orders/verification.blade.php`](file:///Users/kyawwaiyan/Documents/my-Home-tech/resturant-pos/backendPos/resources/views/admin/orders/verification.blade.php):
  - စားပွဲအလိုက် အော်ဒါကတ်များ၊ Progress Bar၊ Touch Checklist၊ Deliver to Table ခလုတ်နှင့် Reprint Modal ပါဝင်သော ခေတ်မီ Dark Obsidian UI။
- [`resources/views/admin/orders/print-kitchen-chit.blade.php`](file:///Users/kyawwaiyan/Documents/my-Home-tech/resturant-pos/backendPos/resources/views/admin/orders/print-kitchen-chit.blade.php):
  - မီးဖိုချောင်သုံး 80mm စလစ်ဖြစ်ပြီး ဈေးနှုန်းများ မပါဝင်ဘဲ ဟင်းအမည်၊ အရေအတွက်နှင့် Special Notes များကို ကြီးမားရှင်းလင်းစွာ ဖော်ပြထားပါသည်။
- [`resources/views/admin/orders/print-customer-bill.blade.php`](file:///Users/kyawwaiyan/Documents/my-Home-tech/resturant-pos/backendPos/resources/views/admin/orders/print-customer-bill.blade.php):
  - ဧည့်သည်စားပွဲတင် 80mm စလစ်ဖြစ်ပြီး ဈေးနှုန်း (MMK)၊ Tax၊ စုစုပေါင်းကျသင့်ငွေနှင့် ကောင်တာတွင် Scan ဖတ်ရန် Order Barcode ပါဝင်ပါသည်။
  - Reprint ပြုလုပ်ပါက `*** REPRINT #N (DUPLICATE) ***` Watermark အလိုအလျောက် ပါဝင်လာမည် ဖြစ်ပါသည်။

---

## ၅။ လက်တွေ့ စမ်းသပ်စစ်ဆေးခြင်း လမ်းညွှန် (Testing Guide)

### အဆင့် (၁) - Expediter Verification Screen သို့ ဝင်ရောက်ခြင်း
1. Browser မှတစ်ဆင့် `http://localhost:8000/admin/orders/verification` သို့ ဝင်ရောက်ပါ။
2. လက်ရှိ မီးဖိုချောင်တွင် ပြင်ဆင်နေသော အော်ဒါများ၊ စားပွဲနံပါတ်များနှင့် စစ်ဆေးရန်ကျန်ရှိသော ဟင်းပွဲများကို တွေ့မြင်ရပါမည်။

### အဆင့် (၂) - ဟင်းပွဲများကို တိုက်ဆိုင်စစ်ဆေးခြင်း (Checklist Toggle)
1. ဟင်းပွဲတစ်ခုချင်းစီ၏ အမှန်ခြစ်အကွက် (Checkbox) ကို နှိပ်လိုက်သည်နှင့် ချက်ချင်း စိမ်းသွားပြီး Item Verification အောင်မြင်သွားပါမည်။
2. အားလုံးပြည့်စုံပါက **"Verify & Deliver to Table"** ခလုတ် ပေါ်လာပါမည်။
3. ခလုတ်ကို နှိပ်လိုက်ပါက စားပွဲသို့ ပို့ဆောင်ပြီးစီးသွားပြီး Dining Table သည်လည်း `OCCUPIED` အဖြစ်သို့ အလိုအလျောက် ပြောင်းလဲသွားပါမည်။

### အဆင့် (၃) - စလစ်ကို ပြန်လည်ထုတ်ယူခြင်း (Slip Reproduction)
1. အော်ဒါကတ်ပေါ်ရှိ **"Reprint Slip"** ခလုတ်ကို နှိပ်ပါ။
2. မည်သည့်စလစ် ထုတ်ယူမည်ကို ရွေးချယ်ပါ:
   - **Kitchen Chit (Slip 1 - မီးဖိုချောင်သုံး စလစ်)**
   - **Customer Bill (Slip 2 - စားပွဲတင် ငွေတောင်းခံလွှာ စလစ်)**
3. အကြောင်းပြချက် ဖြည့်သွင်းပါ (ဥပမာ - `Paper jammed in thermal printer`).
4. **"Confirm & Reprint"** နှိပ်ပါက Thermal Printer Preview ချက်ချင်း ပွင့်လာမည်ဖြစ်ပြီး စလစ်ထိပ်တွင် `*** REPRINT #1 (DUPLICATE) ***` ဟု ထင်ရှားစွာ ပါဝင်လာပါမည်။



