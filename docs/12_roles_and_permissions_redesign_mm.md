# Roles & Permissions Workspace Redesign (မြန်မာဘာသာဖြင့် မှတ်တမ်း)

ဤမှတ်တမ်းသည် Restaurant POS စနစ်၏ **Roles & Permissions Workspace** (`/admin/roles-permissions`) အား ခေတ်မီပြီး အဆင့်မြင့်သော UI/UX ဖြင့် အသစ်ပြန်လည်ဒီဇိုင်းရေးဆွဲထားခြင်းနှင့် ပတ်သက်သည့် နည်းပညာဆိုင်ရာ အသေးစိတ်နှင့် "အဘယ်ကြောင့် ဤသို့ ရွေးချယ်အသုံးပြုရသနည်း (Why)" ကို ရှင်းလင်းတင်ပြထားပါသည်။

---

## ၁။ အဘယ်ကြောင့် Roles & Permissions Workspace ကို ပြန်လည်ဒီဇိုင်းဆွဲရသနည်း (Why)

စားသောက်ဆိုင်တစ်ဆိုင်တွင် ဝန်ထမ်းအဖွဲ့အစည်း (Organizational Hierarchy) နှင့် လုပ်ပိုင်ခွင့်အာဏာ (Access Control) သည် အလွန်အရေးကြီးပါသည်-
1. **စစ်ဆေးရလွယ်ကူသော Grouped Structure**:
   - စားသောက်ဆိုင်တွင် တာဝန်အဆင့်အတန်းအလိုက် Owner (ဆိုင်ရှင်)၊ Manager (မန်နေဂျာ)၊ Cashier (ငွေကိုင်)၊ Waiter/Staff (စားပွဲထိုး) ဟူ၍ အုပ်စုကွဲပြားပါသည်။
   - ၎င်းတို့ကို အုပ်စုခွဲ (Grouped Sections) ဖြင့် သီးသန့်မြင်တွေ့နိုင်မှသာ မည်သူက မည်သည့်အဆင့်တွင် တာဝန်ယူထားသည်ကို ဆိုင်ရှင်အနေဖြင့် တစ်ချက်ကြည့်ရုံဖြင့် ချက်ချင်းခွဲခြားသိရှိနိုင်ပါသည်။
2. **မြန်ဆန်သော Inline Role Change (အချိန်ကုန်သက်သာစေခြင်း)**:
   - ဝန်ထမ်းတစ်ဦး၏ ရာထူး/လုပ်ပိုင်ခွင့်ကို ပြောင်းလဲလိုပါက စာမျက်နှာအသစ်သို့ သွားစရာမလိုဘဲ Table ထဲတွင် Dropdown ဖြင့် ချက်ချင်းပြောင်းလဲနိုင်သောကြောင့် POS Back-Office အသုံးပြုရာတွင် အလွန်သွက်လက်မြန်ဆန်စေပါသည်။
3. **Email Copy Button (တစ်ချက်နှိပ်ရုံဖြင့် ကူးယူနိုင်ခြင်း)**:
   - ဝန်ထမ်းများထံ ဆက်သွယ်ရန် သို့မဟုတ် စနစ်သို့ Invite ပေးပို့ရန်အတွက် Email လိပ်စာဘေးတွင် "Copy" ခလုတ်ပါဝင်သဖြင့် အမှားအယွင်းမရှိ လွယ်ကူစွာ ကူးယူနိုင်ပါသည်။
4. **Spatie RBAC Matrix Visualization**:
   - စားသောက်ဆိုင် စနစ်တစ်ခုလုံးတွင် မည်သည့် Role က မည်သည့် ခွင့်ပြုချက် (ဥပမာ- `pos-checkout`, `take-orders`, `manage-menu`, `view-financial-reports`) ရရှိထားသည်ကို Matrix ဇယားဖြင့် တိကျစွာ သုံးသပ်နိုင်ပါသည်။

---

## ၂။ ဗိသုကာနှင့် ဖိုင်တည်ဆောက်ပုံ (Architecture & File Structure)

```
backendPos/
├── app/
│   ├── Http/
│   │   ├── Controllers/Web/
│   │   │   └── RolePermissionController.php        # Roles & Permissions အဓိက Controller
│   │   ├── Requests/Admin/Roles/
│   │   │   └── UpdateUserRoleRequest.php           # Role ပြောင်းလဲခြင်း စစ်ဆေးမှု (Form Request)
│   │   └── Resources/
│   │       └── UserRoleResource.php                # လုံခြုံသော JSON ထုတ်ပေးမှု (API Resource)
├── resources/views/admin/roles/
│   ├── permissions_index.blade.php                 # အဓိက Redesigned View (Tabs & Header)
│   └── partials/
│       └── member_row.blade.php                    # Table Row တစ်ခုချင်းစီ၏ Component
├── routes/
│   └── web.php                                     # /admin/roles-permissions လမ်းကြောင်းများ
└── tests/Feature/Admin/
    └── RolePermissionManagementTest.php            # စစ်ဆေးမှုပြုလုပ်သော Automated Tests
```

---

## ၃။ အဓိက အစိတ်အပိုင်းများနှင့် "Why" ရှင်းလင်းချက်များ

### (က) `RolePermissionController.php`
- **အဘယ်ကြောင့် သီးသန့် Controller ခွဲထုတ်ရသနည်း**:
  - ယခင်က Generic `AdminModuleController` ၏ Placeholder သာဖြစ်ခဲ့သော်လည်း ယခုအခါ Spatie RBAC Logic များ၊ Restaurant Users များကို အုပ်စုဖွဲ့ခြင်း (Grouped Filtering) များနှင့် Inline AJAX Role Update Logic များကို Single Responsibility Principle (SRP) နှင့်အညီ သီးသန့် ထိန်းကျောင်းနိုင်ရန် ဖန်တီးထားခြင်း ဖြစ်ပါသည်။
- **အဓိက Methods**:
  - `index()`: ဝန်ထမ်းများကို Role အလိုက် Filter ခွဲထုတ်ပြီး Stats များ၊ Permission Matrix Map များနှင့်အတူ View သို့ ပေးပို့ပါသည်။
  - `updateRole()`: ဝန်ထမ်းတစ်ဦး၏ Spatie Role ကို `syncRoles([$newRole])` ဖြင့် ချက်ချင်းပြောင်းလဲပေးပြီး JSON response သို့မဟုတ် Redirect ပြန်လည်ပေးပို့ပါသည်။

### (ခ) `UpdateUserRoleRequest.php` (Mandatory Form Request စည်းမျဉ်း)
- **အဘယ်ကြောင့် Inline Validation မသုံးဘဲ Form Request ခွဲထုတ်ရသနည်း**:
  - Controller အား ရှင်းလင်းကျစ်လစ်စေရန် (Skinny Controller) ဖြစ်ပါသည်။
  - လုံခြုံရေးစည်းမျဉ်းအရ အောက်ပါတို့ကို စစ်ဆေးပေးပါသည်-
    1. Caller သည် `OWNER` role ရှိသူ ဖြစ်ရမည်။
    2. ပြင်ဆင်မည့် `user_id` သည် လက်ရှိ login ဝင်ထားသော ဆိုင်ရှင်၏ စားသောက်ဆိုင် (`restaurant_id`) ထဲမှ ဝန်ထမ်းသာ ဖြစ်ရမည် (အခြားဆိုင်ခွဲမှ ဝန်ထမ်းအား ပြင်ဆင်ခွင့်မရှိစေရန်)။
    3. သတ်မှတ်မည့် `role` သည် `OWNER`, `MANAGER`, `CASHIER`, `STAFF` ထဲမှ တစ်ခုသာ ဖြစ်ရမည်။
    4. **Self-Demotion Prevention**: Owner သည် မိမိ၏ အကောင့်အား မိမိဘာသာ အခြား Role သို့ လျှော့ချခြင်း (Demote) မပြုလုပ်နိုင်ရန် Validator After-hook ဖြင့် တားဆီးထားပါသည်။

### (ဂ) `UserRoleResource.php` (Mandatory API Resource စည်းမျဉ်း)
- **အဘယ်ကြောင့် Raw Model အစား Resource အသုံးပြုရသနည်း**:
  - ဝန်ထမ်းများ၏ `password` hash၊ `pin_code` စသည့် လျှို့ဝှက်အချက်အလက်များ response ထဲသို့ လုံးဝ မပေါက်ကြားစေရန် (Data Leaking Prevention) ဖြစ်ပါသည်။
  - အသစ်သတ်မှတ်လိုက်သော Role နှင့် Formatted Created Date များကို သန့်ရှင်းစွာ ပြန်လည်ပေးပို့ပါသည်။

### (ဃ) Blade Views & Dynamic UI Features
1. **Mockup နှင့် ကိုက်ညီသော Sectioned Table Layout**:
   - အုပ်စုလိုက်ခွဲထားသော Header Rows (`Owner & Executive`, `Management & Supervisors`, `POS Cashiers`, `Dining Floor & Waiters`)။
2. **Avatar နှင့် Status Dot**:
   - Active ဖြစ်နေသော ဝန်ထမ်းများအတွက် စိမ်းပြာရောင် Status Indicator Dot ပါဝင်သောကြောင့် အကောင့်အခြေအနေကို ချက်ချင်းသိမြင်နိုင်ပါသည်။
3. **One-Click Email Copy Utility**:
   - ကလစ်တစ်ချက်နှိပ်ရုံဖြင့် Clipboard ထဲသို့ Email ကူးယူပေးပြီး "Copied! ✓" အဖြစ် ယာယီပြောင်းလဲကာ Toast Notification ပြသပေးပါသည်။
4. **Instant AJAX Role Switcher**:
   - Table ထဲရှိ Dropdown မှ Role အသစ်ကို ရွေးချယ်လိုက်ပါက နောက်ကွယ်မှ CSRF Token ဖြင့် `updateRole` endpoint သို့ AJAX ပို့ကာ စာမျက်နှာ Refresh မဖြစ်စေဘဲ Role အသစ်သို့ ပြောင်းလဲပေးပါသည်။
5. **Interactive Tabs**:
   - **Tab 1: Team Role Assignment** (User Mockup ဒီဇိုင်းအတိုင်း ဝန်ထမ်းများ စာရင်း)။
   - **Tab 2: Permissions Matrix (RBAC)** (Spatie fine-grained permissions များကို Category အလိုက် Role ၄ ခုနှင့် နှိုင်းယှဉ်ပြသသော Matrix ဇယား)။
   - **Tab 3: Role Authorities** (Role တစ်ခုချင်းစီ၏ တာဝန်နှင့် POS လုပ်ပိုင်ခွင့် ကတ်ပြားများ)။

---

## ၄။ စစ်ဆေးပြီးစီးမှုဆိုင်ရာ အထောက်အထားများ (Verification Evidence)

အောက်ပါ Automated Tests များအားလုံး အောင်မြင်စွာ စစ်ဆေးပြီးဖြစ်ပါသည်-

```bash
php artisan test --filter=RolePermissionManagementTest
# [PASS] 6 tests, 27 assertions

php artisan test
# [PASS] 54 tests, 271 assertions (100% Passed)

./vendor/bin/pint --test
# [PASS] Style check passed with 0 errors
```

စစ်ဆေးခဲ့သော စာမေးပွဲအခြေအနေများ-
1. ဆိုင်ရှင် (Owner) သည် Roles & Permissions Workspace သို့ အောင်မြင်စွာ ဝင်ရောက်နိုင်ခြင်း။
2. Manager, Cashier, Staff တို့သည် ဝင်ရောက်ခွင့်မရှိဘဲ `403 Forbidden` ရရှိခြင်း။
3. Owner သည် ဝန်ထမ်းတစ်ဦး၏ Role အား AJAX ဖြင့် အောင်မြင်စွာ ပြောင်းလဲနိုင်ခြင်း။
4. Owner သည် မိမိ၏ အကောင့်အား မိမိဘာသာ Demote ပြုလုပ်ခွင့်မရှိဘဲ Validation Error ပြသခြင်း။
5. အခြားဆိုင်ခွဲမှ ဝန်ထမ်းအား ပြင်ဆင်ခွင့်မပြုဘဲ ကာကွယ်ထားခြင်း။
