# စားသောက်ဆိုင် POS စနစ် - ဝန်ထမ်းစီမံခန့်ခွဲမှုနှင့် POS Terminal PIN စနစ် (Employee Management & Quick PIN System)

ဤမှတ်တမ်းသည် Restaurant POS စနစ်တွင် ဆိုင်ပိုင်ရှင် (`OWNER`) နှင့် မန်နေဂျာ (`MANAGER`) များအနေဖြင့် ဝန်ထမ်းများ (မန်နေဂျာ၊ ငွေကိုင်၊ စားပွဲထိုး) ကို အဆင်ပြေချောမွေ့စွာ စီမံခန့်ခွဲနိုင်ရန်၊ ရာထူးအလိုက် လုပ်ပိုင်ခွင့်စည်းမျဉ်းများ သတ်မှတ်နိုင်ရန်နှင့် POS Touchscreen စက်များတွင် စက္ကန့်ပိုင်းအတွင်း လျင်မြန်စွာ Unlock ဖွင့်နိုင်မည့် **POS Quick PIN (၄-၆ လုံး ဂဏန်း PIN)** စနစ် တည်ဆောက်ထားပုံကို မြန်မာဘာသာဖြင့် အသေးစိတ် မှတ်တမ်းတင်ထားခြင်း ဖြစ်ပါသည်။

---

## ၁။ အဘယ်ကြောင့် Employee Management နှင့် POS Quick PIN စနစ်ကို တည်ဆောက်ရသနည်း (Why Employee Management & Quick PIN?)

ပြင်ပ စားသောက်ဆိုင်ကြီးများ၏ နေ့စဉ်လုပ်ငန်းခွင် (Daily Restaurant Floor Operations) တွင် အောက်ပါ လက်တွေ့လိုအပ်ချက်များ ရှိပါသည်-

1. **စက္ကန့်ပိုင်းအတွင်း အလျင်အမြန် စားပွဲဝိုင်း Order ကောက်နိုင်ခြင်း (Touchscreen Quick PIN Switch)**:
   - စားသောက်ဆိုင်တွင် စားပွဲထိုးများသည် ဝိုင်းတစ်ခုပြီးတစ်ခု Order ကောက်ယူရသလို၊ ကောင်တာတွင် ငွေကိုင်များသည်လည်း အလှည့်ကျ ဘေလ်ရှင်းရပါသည်။
   - ရှည်လျားသော Email နှင့် Password ကို အကြိမ်ကြိမ် ရိုက်ထည့်နေရပါက အချိန်ကြန့်ကြာပြီး ဧည့်သည်များ စောင့်ဆိုင်းရမှု ပိုမိုကြာမြင့်စေပါသည်။
   - ထို့ကြောင့် ဝန်ထမ်းတစ်ဦးချင်းစီအတွက် ၄ မှ ၆ လုံးပါသော `pin_code` ကို သီးသန့်သတ်မှတ်ပေးပြီး POS Screen ပေါ်တွင် အမြန် Login/Unlock ပြုလုပ်နိုင်စေပါသည်။

2. **ရာထူးအလိုက် လုပ်ပိုင်ခွင့် အဆင့်ဆင့် ခွဲခြားသတ်မှတ်ခြင်း (Role-Based Hierarchy & Access Control)**:
   - **👑 ဆိုင်ပိုင်ရှင် (OWNER)**: မန်နေဂျာအသစ် ခန့်အပ်ခြင်း၊ ငွေကိုင်နှင့် စားပွဲထိုးများ ခန့်အပ်ခြင်း၊ ဆိုင်၏ ဘဏ္ဍာရေးနှင့် ဝန်ထမ်းအားလုံး၏ PIN/Password များကို အပြည့်အဝ စီမံခန့်ခွဲနိုင်ပါသည်။
   - **👔 မန်နေဂျာ (MANAGER)**: ဆိုင်ကြမ်းပြင်တွင် ကြီးကြပ်ရန်အတွက် Cashier နှင့် Dining Staff များကိုသာ ခန့်အပ်/ပြင်ဆင်နိုင်ပြီး၊ မန်နေဂျာ အချင်းချင်း သို့မဟုတ် Owner အကောင့်ကို ဖျက်ပစ်ခြင်း/ပြင်ဆင်ခြင်း လုံးဝမပြုလုပ်နိုင်အောင် စည်းမျဉ်းဖြင့် တားမြစ်ထားပါသည်။
   - **💵 ငွေကိုင် (CASHIER) နှင့် 🍽️ စားပွဲထိုး (STAFF)**: အခြားဝန်ထမ်းများ၏ စာရင်းကို ကြည့်ရှုခွင့်၊ ပြင်ဆင်ခွင့် လုံးဝမရှိစေဘဲ `403 Forbidden` ဖြင့် လုံခြုံစွာ ပိတ်ထားပါသည်။

3. **Multi-Tenancy Restaurant Data Isolation (ဆိုင်ခွဲများအကြား အချက်အလက် သီးခြားဖြစ်စေခြင်း)**:
   - ဝန်ထမ်းတစ်ဦးသည် မိမိနှင့် သက်ဆိုင်သည့် `restaurant_id` ရှိသော စားသောက်ဆိုင်၏ ဝန်ထမ်းစာရင်းကိုသာ စီမံနိုင်ပြီး၊ အခြားဆိုင်ခွဲများ၏ ဝန်ထမ်းအချက်အလက်များကို ဝင်ရောက်စွက်ဖက်ခြင်း မပြုနိုင်ပါ။

---

## ၂။ စနစ်တည်ဆောက်ပုံနှင့် နည်းပညာ အစိတ်အပိုင်းများ (Architecture Components)

### ၂.၁ Database Migration (`users` table)
- `phone`: ဝန်ထမ်း၏ ဆက်သွယ်ရန် ဖုန်းနံပါတ် (String, Nullable)။
- `pin_code`: POS Terminal သို့ လျင်မြန်စွာ ဝင်ရောက်နိုင်သော ၄-၆ လုံး လျှို့ဝှက် PIN (Laravel Hash ဖြင့် လုံခြုံစွာ သိမ်းဆည်းထားပါသည်)။

```php
Schema::table('users', function (Blueprint $table) {
    $table->string('phone')->nullable()->after('email');
    $table->string('pin_code')->nullable()->after('password');
});
```

### ၂.၂ Eloquent Model Security (`User.php`)
- `pin_code` အား `Hidden` တွင် ထည့်သွင်းထားသောကြောင့် API သို့မဟုတ် JSON Response များတွင် ပေါက်ကြားမှု မရှိစေပါ။
- `casts` တွင် `'pin_code' => 'hashed'` သတ်မှတ်ထားသောကြောင့် Plain PIN Code များကို Database တွင် မသိမ်းဆည်းဘဲ အလိုအလျောက် Bcrypt Hashing ပြုလုပ်ပေးပါသည်။

### ၂.၃ EmployeeController (`app/Http/Controllers/Web/EmployeeController.php`)
- **`index()`**: မိမိဆိုင်ရှိ ဝန်ထမ်းစာရင်းကို Role အလိုက် Filter ပြုလုပ်ခြင်း၊ အမည်/အီးမေးလ်/ဖုန်းနံပါတ်တို့ဖြင့် ရှာဖွေခြင်း (Search) နှင့် Pagination ပါဝင်သည်။
- **`create()` & `store()`**:
  - Owner ဖြစ်ပါက `MANAGER`, `CASHIER`, `STAFF` ရာထူးများကို ခန့်အပ်နိုင်ခွင့်ရှိသည်။
  - Manager ဖြစ်ပါက `CASHIER` နှင့် `STAFF` ရာထူးများကိုသာ ခန့်အပ်နိုင်ခွင့်ရှိပြီး Manager သို့မဟုတ် Owner ရာထူး ရွေးချယ်ပါက Validation Error ဖြင့် တားဆီးထားသည်။
- **`edit()` & `update()`**: ဝန်ထမ်းအချက်အလက်၊ ဖုန်းနံပါတ်၊ ရာထူးနှင့် PIN အသစ် ပြောင်းလဲခြင်း။
- **`destroy()`**:
  - မိမိကိုယ်ကို ပြန်လည်ဖျက်ပစ်ခွင့် မရှိပါ။
  - ဆိုင်ပိုင်ရှင် (`OWNER`) အကောင့်ကို မည်သူမျှ ဖျက်ပစ်ခွင့် မရှိပါ။
  - Manager သည် အခြား Manager များကို ဖျက်ပစ်ခွင့် မရှိပါ။

---

## ၃။ အသုံးပြုသူ မျက်နှာပြင်များ (Blade Views)

1. **ဝန်ထမ်းများ စာရင်း စာမျက်နှာ (`admin/employees/index.blade.php`)**:
   - Modern Dark Glassmorphic Design ဖြင့် တည်ဆောက်ထားသည်။
   - ရာထူးအလိုက် အရောင်ခွဲခြားပေးသော Badge များ (Owner: လိမ္မော်ရောင်၊ Manager: အင်ဒီဂိုရောင်၊ Cashier: မြစိမ်းရောင်၊ Staff: အပြာနုရောင်)။
   - POS PIN သတ်မှတ်ထားခြင်း ရှိ/မရှိ ပြသပေးသော Indicator (`🔒 PIN Active` / `⚠️ Not Assigned`)။
2. **ဝန်ထမ်းအသစ် ဖြည့်သွင်းသည့် စာမျက်နှာ (`admin/employees/create.blade.php`)**:
   - အမည်၊ အီးမေးလ်၊ ဖုန်း၊ ရာထူးရွေးချယ်မှု၊ စကားဝှက်နှင့် ၄-၆ လုံး POS Terminal PIN။
3. **ဝန်ထမ်းအချက်အလက် ပြင်ဆင်သည့် စာမျက်နှာ (`admin/employees/edit.blade.php`)**:
   - အချက်အလက်များ ပြင်ဆင်နိုင်ပြီး စကားဝှက် သို့မဟုတ် PIN အား မပြောင်းလဲလိုပါက ကွက်လပ်ထားနိုင်သည့် စနစ်။

---

## ၄။ စမ်းသပ်စစ်ဆေးပြီး အတည်ပြုချက် ရလဒ်များ (Verification & Test Evidence)

Feature Tests စုစုပေါင်း **၄၄ ခု (Assertions ၁၈၄ ခု)** အားလုံး ၁၀၀% အောင်မြင်စွာ စစ်ဆေးပြီးဖြစ်ပါသည်-

1. `guest is redirected to admin login` ✅
2. `cashier and staff are forbidden from employee management` ✅
3. `owner can view employee list with all roles` ✅
4. `owner can create manager with pin code` ✅
5. `manager can create cashier and dining staff` ✅
6. `manager cannot create a manager or owner` ✅
7. `manager cannot edit or delete owner or another manager` ✅
8. `owner can update employee details and role` ✅
9. `user cannot delete themselves or owner account` ✅
10. `owner can delete staff member` ✅
11. `employee from another restaurant cannot be accessed` ✅

Laravel Pint စံသတ်မှတ်ချက်အရ Code Base ဖိုင်ပေါင်း ၄၈ ဖိုင်လုံး သန့်ရှင်းမှု အပြည့်အဝ ရှိကြောင်း အတည်ပြုပြီးဖြစ်ပါသည်။
