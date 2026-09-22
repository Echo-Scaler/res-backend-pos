# ဝန်ထမ်းတစ်ဦးချင်းအလိုက် သီးသန့်လုပ်ပိုင်ခွင့် သတ်မှတ်ပေးခြင်း (User-Level Direct Permission Overrides)

ဤမှတ်တမ်းသည် Restaurant POS စနစ်တွင် သာမန် Role-Based Access Control (RBAC) အပြင် **ဝန်ထမ်းတစ်ဦးချင်းစီအလိုက် သီးသန့်လုပ်ပိုင်ခွင့်များ (Direct Permission Overrides)** ကို စိတ်ကြိုက် ပေါင်းထည့်/နုတ်ပယ်နိုင်သော Hybrid စနစ် ထည့်သွင်းတည်ဆောက်ခြင်းဆိုင်ရာ အသေးစိတ် ရှင်းလင်းချက် ဖြစ်ပါသည်။

---

## ၁။ အဘယ်ကြောင့် ဤစနစ်ကို ထည့်သွင်းအသုံးပြုရသနည်း (Why It Is Required in Real-World POS)

လက်တွေ့ စားသောက်ဆိုင်လုပ်ငန်းခွင် (Real-World Restaurant Operations) တွင် အောက်ပါအခြေအနေများနှင့် အမြဲကြုံတွေ့ရလေ့ရှိပါသည် -

1. **ယုံကြည်ရသော ဝန်ထမ်းအား သီးသန့်လုပ်ပိုင်ခွင့် ပေးအပ်လိုခြင်း (Trust-based Delegation)**:
   - ဥပမာ - ဝန်ထမ်းတစ်ဦးသည် တရားဝင် ရာထူးအရ `STAFF` (စားပွဲထိုး/အော်ဒါယူသူ) သို့မဟုတ် `CASHIER` (ငွေကိုင်) ဖြစ်သော်လည်း လုပ်သက်ကြာပြီး စိတ်ချရသဖြင့် အထူးလျှော့စျေးများ ပေးခွင့် (`apply-discounts`) သို့မဟုတ် စတိုလက်ကျန် ပစ္စည်းစာရင်း ဖြည့်တင်းခွင့် (`manage-inventory`) ပေးအပ်လိုခြင်း။
2. **ရာထူးတစ်ခုလုံး မြှင့်တင်ရန် မလိုအပ်ခြင်း (Avoid Role Inflation)**:
   - အကယ်၍ ထိုဝန်ထမ်းကို `MANAGER` ရာထူးသို့ တိုက်ရိုက် ပြောင်းလဲလိုက်ပါက ဆိုင်၏ အခြားဝန်ထမ်းများအား အလုပ်ထုတ်ပယ်ခွင့်၊ လျှို့ဝှက်ကုဒ် PIN များ ပြင်ဆင်ခွင့်၊ ဘဏ္ဍာရေးစာရင်းအင်း အပြည့်အစုံ ကြည့်ရှုခွင့် စသည့် အန္တရာယ်ရှိသော စီမံခန့်ခွဲခွင့်များ ပါသွားမည်ဖြစ်သည်။
3. **အနည်းဆုံး လိုအပ်သော လုပ်ပိုင်ခွင့်သာ ကန့်သတ်ပေးခြင်း (Principle of Least Privilege)**:
   - လုံခြုံရေးအရ ဝန်ထမ်းတစ်ဦးအား ၎င်းလုပ်ကိုင်ရမည့် တာဝန်အတွက်သာ သီးသန့် permission ပေးပြီး အခြား မလိုအပ်သော စီမံခန့်ခွဲမှု အာဏာများကို ထိန်းချုပ်ထားနိုင်ပါသည်။
4. **Owner နှင့် Manager တို့တွင် ဤစနစ် မလိုအပ်ခြင်း (Restricted to Cashier & Staff Only)**:
   - ဆိုင်ရှင် (Owner) သည် စနစ်တစ်ခုလုံးကို အပြည့်အစုံ ချုပ်ကိုင်ထားသော Super-Admin ဖြစ်ပြီး၊ မန်နေဂျာ (Manager) သည်လည်း ဆိုင်၏ လုပ်ငန်းလည်ပတ်မှုဆိုင်ရာ Standard ခွင့်ပြုချက်များ ရရှိပြီးသား ဖြစ်သဖြင့် ၎င်းတို့အတွက် သီးသန့် အပိုပေးရန် မလိုလားအပ်ပါ။
   - ထို့ကြောင့် ဤ Direct Permission Overrides စနစ်ကို စားသောက်ဆိုင် ရှေ့တန်းဝန်ထမ်းများဖြစ်သည့် **CASHIER** (ငွေကိုင်) နှင့် **STAFF** (စားပွဲထိုး/အော်ဒါယူသူ) တို့တွင်သာ သီးသန့် သတ်မှတ်အသုံးပြုနိုင်အောင် ကန့်သတ်ထားပါသည်။
   - အကယ်၍ ဝန်ထမ်းတစ်ဦးအား `MANAGER` ရာထူးသို့ တိုးမြှင့်လိုက်ပါက ၎င်းတွင် ရှိထားသော direct overrides များကို သန့်ရှင်းစွာ အလိုအလျောက် ရှင်းလင်းပေးပါသည် (Auto-cleaned upon promotion)။
5. **ဆိုင်ရှင် (Owner) စိတ်ကြိုက် ချက်ချင်း ထိန်းချုပ်နိုင်ခြင်း (Instant Override Control)**:
   - မန်နေဂျာများ မပါဝင်ဘဲ ဆိုင်ရှင်ကိုယ်တိုင်သာ Edit Employee စာမျက်နှာတွင် ချက်ချင်း ခွင့်ပြုချက် (Grant) သို့မဟုတ် ပြန်လည်ရုပ်သိမ်းခြင်း (Revoke) ပြုလုပ်နိုင်ပါသည်။

---

## ၂။ ဗိသုကာနှင့် နည်းပညာ အကောင်အထည်ဖော်မှု (Architecture & Implementation)

### က။ Spatie Hybrid Permission Engine
- **Role Permissions (`role_has_permissions`)**: ရာထူး (Role) အလိုက် ပုံသေရရှိထားသော ခွင့်ပြုချက်များ။ (ဥပမာ - Cashier သည် `pos-checkout` အလိုအလျောက် ရရှိသည်)။
- **Direct Model Permissions (`model_has_permissions`)**: အသုံးပြုသူ User တစ်ဦးချင်းစီထံသို့ Spatie `syncPermissions()` ဖြင့် တိုက်ရိုက် ပေးအပ်ထားသော ခွင့်ပြုချက်များ။
- **Authorization Check (`$user->can('...')`)**:
  - Laravel စနစ်မှ `$user->can('apply-discounts')` ကို စစ်ဆေးသည့်အခါ User ၏ Role Permissions နှင့် Direct Permissions နှစ်မျိုးစလုံးကို ပေါင်းစပ်စစ်ဆေးပေးသဖြင့် အပို code များ ထပ်ရေးရန် မလိုဘဲ အလိုအလျောက် ခွင့်ပြုပေးပါသည်။

---

### ခ။ ဖိုင်များ ပြင်ဆင်ဖွဲ့စည်းပုံ (Modified & Created Files)

1. **Form Request (`app/Http/Requests/Admin/Employee/UpdateEmployeeRequest.php`)**:
   - `direct_permissions` (array of valid permission names) နှင့် `direct_permissions_override_submitted` (boolean) validation စည်းမျဉ်းများ ထည့်သွင်းထားပါသည်။
   - Inline validation မသုံးဘဲ Rule 4 အတိုင်း Form Request တွင် စနစ်တကျ စိစစ်ထားပါသည်။

2. **Controller (`app/Http/Controllers/Web/EmployeeController.php`)**:
   - `edit()` action တွင် Role မှ ရရှိထားသော permissions (`$rolePermissions`), User သီးသန့် ခွင့်ပြုချက်များ (`$directPermissions`), နှင့် အုပ်စုဖွဲ့ထားသော permission groups များကို view သို့ ပို့ဆောင်ပေးပါသည်။
   - `update()` action တွင် လက်ရှိ ဆိုင်ရှင် (`$currentUser->hasRole('OWNER')`) ဖြစ်မှသာလျှင် `$employee->syncPermissions(...)` ကို စိတ်ချစွာ ခေါ်ယူပါသည်။ မန်နေဂျာ သို့မဟုတ် အခြားသူများ ပြင်ဆင်ခွင့် မရှိအောင် ကာကွယ်ထားပါသည်။

3. **Blade Template (`resources/views/admin/employees/edit.blade.php`)**:
   - ဝန်ထမ်းအချက်အလက် ပြင်ဆင်သည့် Form တွင် **"🛡️ User-Level Direct Permission Overrides"** ကဏ္ဍကို ထည့်သွင်းထားပါသည်။
   - Role အလိုက် ရရှိပြီးသား ခွင့်ပြုချက်များကို `🔵 Active by Base Role` အပြာရောင် Badge ဖြင့် disabled checkbox ပြသထားပါသည်။
   - သီးသန့် အပိုပေးထားသော ခွင့်ပြုချက်များကို `🟢 Custom User Override` အစိမ်းရောင် Badge ဖြင့် အမှတ်အသား ပြုထားပါသည်။
   - ဆိုင်ရှင်အနေဖြင့် လိုအပ်သော ခွင့်ပြုချက်များကို အမှန်ခြစ် (Check/Uncheck) လုပ်ရုံဖြင့် ချက်ချင်း ပြင်ဆင်နိုင်ပါသည်။

4. **API Resources (`app/Http/Resources/EmployeeResource.php`, `UserRoleResource.php`)**:
   - Controller များတွင် raw Eloquent model အား တိုက်ရိုက် မပြသဘဲ Resource မှတစ်ဆင့် `permissions` (စုစုပေါင်း), `direct_permissions` (သီးသန့်ပေးထားချက်), `role_permissions` (ရာထူးအရ ရရှိချက်) တို့ကို ရှင်းလင်းစွာ ခွဲခြားပေးပို့ပါသည်။

5. **Automated Feature Test (`tests/Feature/Admin/UserDirectPermissionTest.php`)**:
   - ဆိုင်ရှင်မှ တိုက်ရိုက် ခွင့်ပြုချက် ပေးအပ်နိုင်ခြင်း စစ်ဆေးခြင်း။
   - ခွင့်ပြုချက်များ ပြန်လည် နုတ်ပယ်/ဖျက်သိမ်းနိုင်ခြင်း စစ်ဆေးခြင်း။
   - မန်နေဂျာမှ ဝန်ထမ်းများအား လုပ်ပိုင်ခွင့် အပိုမပေးနိုင်အောင် တားဆီးထားမှု စစ်ဆေးခြင်း။
   - မမှန်ကန်သော Permission အမည်များအား validation မှ ပယ်ချခြင်း စစ်ဆေးခြင်း။

---

## ၃။ စစ်ဆေးပြီးစီးမှု အခြေအနေ (Verification & Test Evidence)

```bash
# Automated Test Results
php artisan test --filter=UserDirectPermissionTest
# Tests: 5 passed (34 assertions)

# Total Backend POS Test Suite
php artisan test
# Tests: 72 passed (356 assertions)

# Code Style & Pint Standards
./vendor/bin/pint --test
# 100% Passed
```
