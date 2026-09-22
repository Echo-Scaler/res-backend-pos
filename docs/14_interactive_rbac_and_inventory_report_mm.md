# Interactive RBAC Matrix & Inventory Remaining Stock Report (မြန်မာဘာသာဖြင့် မှတ်တမ်း)

ဤမှတ်တမ်းသည် Restaurant POS စနစ်၏ **Permissions Matrix အား ပိုမိုရှင်းလင်းထင်ရှားစွာ မြင်တွေ့နိုင်စေရန် ပြုပြင်ခြင်း (Sticky Headers & Clear Badges)**၊ **ခွင့်ပြုချက်များကို တိုက်ရိုက် Add/Remove (Grant/Revoke) ပြုလုပ်နိုင်သည့် Interactive Toggles များ ထည့်သွင်းခြင်း** နှင့် **Inventory Stock Remaining Report (မီးဖိုချောင်ကုန်ကြမ်း လက်ကျန်နှင့် တန်ဖိုးတွက်ချက်မှု အစီရင်ခံစာ)** တို့နှင့် ပတ်သက်သည့် "အဘယ်ကြောင့် ဤသို့ ပြုလုပ်ရသနည်း (Why)" နှင့် နည်းပညာဆိုင်ရာ အသေးစိတ်ကို ရှင်းလင်းတင်ပြထားပါသည်။

---

## ၁။ အဘယ်ကြောင့် ဤလုပ်ဆောင်ချက်များကို ပြုလုပ်ရသနည်း (Why)

1. **ရှင်းလင်းထင်ရှားသော Permissions Matrix (Sticky Headers & Visibility)**:
   - စားသောက်ဆိုင် လုပ်ငန်းတွင် permissions ပေါင်းများစွာ (Menu, Inventory, Tables, Orders, Staff, Financials) ပါဝင်သဖြင့် ဇယားကို အောက်သို့ scroll ဆွဲကြည့်ရှုချိန်တွင် ကော်လံခေါင်းစဉ်များ (👑 OWNER, 💼 MANAGER, 💳 CASHIER, 🍽️ STAFF) ပျောက်ကွယ်မသွားဘဲ **Sticky Header** အဖြစ် ထင်ရှားစွာ မြင်တွေ့နေရရန် လိုအပ်ပါသည်။
   - ယခင်က မရှင်းလင်းသော static badge များအစား Role အလိုက် အရောင်ကွဲပြားသော ခေါင်းစဉ်များနှင့် Hover highlights များကို အသုံးပြုထားသဖြင့် မည်သည့် role တွင် မည်သည့် ခွင့်ပြုချက်ရှိသည်ကို ချက်ချင်းရှင်းလင်းစွာ ခွဲခြားနိုင်ပါသည်။
2. **Dynamic Permission Add & Remove (ချက်ချင်း ခွင့်ပြုချက် အတိုး/အလျှော့ ပြုလုပ်နိုင်ခြင်း)**:
   - ဆိုင်ရှင် (Owner) အနေဖြင့် Manager, Cashier သို့မဟုတ် Staff များအတွက် လုပ်ပိုင်ခွင့်တစ်ခုခု (ဥပမာ- `pos-checkout`, `manage-inventory`, `apply-discounts`) အား ဖြုတ်လိုပါက သို့မဟုတ် ထပ်မံပေးအပ်လိုပါက ကလစ်တစ်ချက်နှိပ်ရုံဖြင့် ချက်ချင်း AJAX ဖြင့် Toggle ပြုလုပ်နိုင်ပါသည်။
   - လုံခြုံရေးစည်းမျဉ်းအရ ဆိုင်ရှင်၏ Master အာဏာကို မတော်တဆ demote မဖြစ်စေရန် `ToggleRolePermissionRequest` ဖြင့် တားဆီးထားပါသည်။
3. **Inventory Stock Remaining Report (မီးဖိုချောင် လက်ကျန်ကုန်ကြမ်း စစ်ဆေးမှု အစီရင်ခံစာ)**:
   - စားသောက်ဆိုင် မီးဖိုချောင်တွင် နေ့စဉ် ချက်ပြုတ်ရောင်းချပြီးနောက် ကုန်ကြမ်းပစ္စည်းများ (Remaining Stock) မည်မျှကျန်ရှိသည်ကို တိကျစွာ သိရှိရန် အလွန်အရေးကြီးပါသည်။
   - လက်ကျန်ပမာဏ (Remain)၊ အနိမ့်ဆုံးသတိပေးချက် (Safety Threshold)၊ တစ်ယူနစ်ကုန်ကျစရိတ် (Unit Cost) နှင့် ကျန်ရှိသော ပစ္စည်းများ၏ စုစုပေါင်း ငွေကြေးတန်ဖိုး (Total Remaining Worth in MMK) တို့ကို တွက်ချက်ပြသပေးထားပါသည်။
   - စားဖိုမှူးနှင့် စာရင်းကိုင်များ စာရင်းစစ်နိုင်ရန်အတွက် **Print Report** (စက္ကူဖြင့် Print ထုတ်ယူခြင်း) နှင့် **Export CSV** (Excel ထဲသို့ ဒေတာထုတ်ယူခြင်း) လုပ်ဆောင်ချက်များ ထည့်သွင်းပေးထားပါသည်။

---

## ၂။ နည်းပညာဆိုင်ရာ ဗိသုကာနှင့် ဖိုင်တည်ဆောက်ပုံ

```
backendPos/
├── app/
│   ├── Http/
│   │   ├── Controllers/Web/
│   │   │   ├── RolePermissionController.php        # togglePermission() method အသစ်
│   │   │   └── InventoryController.php             # report() method နှင့် CSV export
│   │   └── Requests/Admin/Roles/
│   │       └── ToggleRolePermissionRequest.php     # Role & Permission တရားဝင်မှု စစ်ဆေးခြင်း
├── resources/views/admin/
│   ├── roles/
│   │   └── permissions_index.blade.php             # Sticky Header & Interactive Matrix Toggle
│   └── inventory/
│       ├── index.blade.php                         # Remaining Stock Report သို့ သွားသည့် ခလုတ်
│       └── report.blade.php                        # Print & Export ပါဝင်သော Stock Report View
├── routes/
│   └── web.php                                     # /admin/roles-permissions/toggle-permission နှင့် /admin/inventory/report
└── tests/Feature/Admin/
    └── InteractiveRbacAndInventoryReportTest.php   # စစ်ဆေးမှု Feature Tests
```

---

## ၃။ အဓိက အစိတ်အပိုင်းများနှင့် လုပ်ဆောင်ပုံ ရှင်းလင်းချက်

### (က) `ToggleRolePermissionRequest.php` (Mandatory Form Request)
- Controller ထဲတွင် validation logic များ မရောထွေးစေရန် သီးသန့် Request class အဖြစ် ဖန်တီးထားပါသည်။
- **စည်းမျဉ်းများ**:
  1. Login ဝင်ထားသူသည် `OWNER` ဖြစ်ရမည်။
  2. Target role သည် `MANAGER`, `CASHIER`, `STAFF` ထဲမှ တစ်ခုသာ ဖြစ်ရမည် (Owner role အား ဖြုတ်ချခွင့်မပြု)။
  3. Permission သည် Spatie permissions ဇယားထဲတွင် အမှန်တကယ် ရှိသော အမည်ဖြစ်ရမည်။

### (ခ) `RolePermissionController@togglePermission`
- Spatie ၏ `hasPermissionTo()` ဖြင့် လက်ရှိရှိမရှိ စစ်ဆေးပြီး ရှိပါက `revokePermissionTo()` ဖြင့် ဖြုတ်ပေးကာ၊ မရှိပါက `givePermissionTo()` ဖြင့် ထည့်သွင်းပေးပါသည်။
- Spatie Permission Cache ကို `forgetCachedPermissions()` ဖြင့် ချက်ချင်းရှင်းလင်းပေးသဖြင့် အခွင့်အရေးများ ချက်ချင်း အကျိုးသက်ရောက်မှု ရှိစေပါသည်။

### (ဂ) `InventoryController@report` နှင့် Print/CSV Export
- စားသောက်ဆိုင်၏ ပစ္စည်းအားလုံး၏ လက်ကျန်ပမာဏ၊ အနိမ့်ဆုံးသတိပေးအဆင့်၊ ယူနစ်ကုန်ကျစရိတ်နှင့် စုစုပေါင်းတန်ဖိုးကို တွက်ချက်ပေးပါသည်။
- `?export=csv` parameter ပါလာပါက Browser ထဲသို့ CSV ဖိုင်အဖြစ် တိုက်ရိုက် streamDownload ပြုလုပ်ပေးပါသည်။
- `window.print()` အတွက် သီးသန့် `@media print` CSS ရေးဆွဲထားသဖြင့် Print ထုတ်ချိန်တွင် Sidebar နှင့် Header များ မပါဝင်ဘဲ သန့်ရှင်းသော စာရင်းဇယားအဖြစ်သာ ထွက်ရှိလာမည် ဖြစ်ပါသည်။

---

## ၄။ စစ်ဆေးပြီးစီးမှုဆိုင်ရာ အထောက်အထားများ (Verification Evidence)

အောက်ပါ Automated Tests အားလုံး အောင်မြင်စွာ စစ်ဆေးပြီးဖြစ်ပါသည်-

```bash
php artisan test --filter=InteractiveRbacAndInventoryReportTest
# [PASS] 6 tests, 22 assertions

php artisan test
# [PASS] 67 tests, 322 assertions (100% Passed)

./vendor/bin/pint --test
# [PASS] Style check passed with 0 errors
```

စစ်ဆေးခဲ့သော အချက်များ-
1. Owner သည် AJAX ဖြင့် Manager ထံသို့ permission အသစ် ထည့်သွင်းခြင်းနှင့် ပြန်လည်ဖြုတ်ချခြင်း အောင်မြင်ခြင်း။
2. Owner permissions များကို toggle ဖြင့် ဖြုတ်ချခွင့် မရှိဘဲ 422 validation error ပြသခြင်း။
3. Manager သည် အခြား role များ၏ permissions ကို toggle ပြုလုပ်ခွင့်မရှိဘဲ 403 Forbidden ရရှိခြင်း။
4. Owner နှင့် Manager တို့သည် Inventory Stock Remaining Report ကို ကြည့်ရှုနိုင်ပြီး လက်ကျန်ပမာဏနှင့် စုစုပေါင်းတန်ဖိုးများ မှန်ကန်စွာ တွက်ချက်ပြသခြင်း။
5. Cashier နှင့် Staff တို့သည် Report ကို ကြည့်ရှုခွင့်မရှိဘဲ 403 Forbidden ရရှိခြင်း။
6. CSV Export စနစ်သည် text/csv format ဖြင့် အောင်မြင်စွာ ဒေါင်းလုဒ်ပေးပို့နိုင်ခြင်း။
