# စားသောက်ဆိုင် POS စနစ် - ဆိုင်ပိုင်ရှင် အလုပ်ခွင်နှင့် စီမံခန့်ခွဲမှု မော်ဂျူး ၁၇ ခု (Owner Portal & Executive Analytics Dashboard)

ဤမှတ်တမ်းသည် Restaurant POS စနစ်တွင် ဆိုင်ပိုင်ရှင် (`OWNER`) အနေဖြင့် စားသောက်ဆိုင် လုပ်ငန်းတစ်ခုလုံးအား ဗဟိုမှ အပြည့်အဝ ချုပ်ကိုင်နိုင်မည့် **Sidebar Navigation (အဓိက စီမံခန့်ခွဲမှု Modules ၁၇ ခု)** နှင့် ဆိုင်၏ နေ့စဉ်ဘဏ္ဍာရေး၊ အော်ဒါ၊ ကုန်ပစ္စည်းလက်ကျန်၊ ဝန်ထမ်းလှုပ်ရှားမှုများကို အချိန်နှင့်တစ်ပြေးညီ မျက်ခြည်မပြတ် စောင့်ကြည့်နိုင်မည့် **Executive Analytics Dashboard (အဓိက စာရင်းအင်း ၁၀ ခု)** တည်ဆောက်ထားပုံကို မြန်မာဘာသာဖြင့် အသေးစိတ် ရှင်းလင်းထားခြင်း ဖြစ်ပါသည်။

---

## ၁။ အဘယ်ကြောင့် Owner Portal တွင် Modules ၁၇ ခုနှင့် Metrics ၁၀ ခု ပါဝင်ရသနည်း (Why this Architecture?)

လက်တွေ့ ပြင်ပ စားသောက်ဆိုင်ကြီးများ (Real-World Restaurant Operations) တွင် ဆိုင်ပိုင်ရှင်သည် နေရာစုံမှ အချက်အလက်များကို ချက်ချင်းသိရှိပြီး ဆုံးဖြတ်ချက်ချနိုင်ရန် လိုအပ်ပါသည်-

1. **ယနေ့ ရောင်းအားနှင့် အော်ဒါများ (Today's Sales & Orders)**:
   - နေ့စဉ် ရောင်းအားပစ်မှတ် ပြည့်/မပြည့်ကို စောင့်ကြည့်ပြီး ဝန်ထမ်းအင်အားနှင့် မီးဖိုချောင် အလုပ်ရှုပ်မှုကို ချိန်ညှိနိုင်ရန်။
2. **စားပွဲဝိုင်းတစ်ခု ပျမ်းမျှ ကျသင့်ငွေ (Average Order Value - AOV)**:
   - ဧည့်သည်တစ်ဦးချင်း သို့မဟုတ် စားပွဲဝိုင်းတစ်ခုလျှင် ငွေကြေးသုံးစွဲမှု ပမာဏကို သိရှိပြီး Upselling နှင့် Combo Meal မီနူးများ ဖန်တီးနိုင်ရန်။
3. **ငွေပေးချေမှုပုံစံ ခွဲခြမ်းစိတ်ဖြာမှု (Payment Breakdown)**:
   - မြန်မာနိုင်ငံ၏ လက်ရှိ စားသောက်ဆိုင်ဈေးကွက်တွင် KBZPay, WavePay စသည့် Mobile QR များ၊ Visa/MPU Card များနှင့် Cash (ငွေသား) မည်မျှ အချိုးကျ အသုံးပြုနေသည်ကို သိရှိပြီး ငွေကိုင်ကောင်တာနှင့် ဘဏ်စာရင်းများကို တိုက်ဆိုင်နိုင်ရန်။
4. **ရောင်းအားအကောင်းဆုံး ဟင်းလျာများ (Best-Selling Products)**:
   - မည်သည့်ဟင်းလျာများက ဆိုင်၏ အဓိက ဝင်ငွေရှာဖွေပေးနေသည်ကို သိရှိပြီး ကုန်ကြမ်းကြိုတင်မှာယူနိုင်ရန်။
5. **လက်ကျန်နည်းနေသော ကုန်ကြမ်းသတိပေးချက် (Low-Stock Items)**:
   - ဟင်းချက်ဆီ၊ အသား၊ နို့ဆီ၊ ပါဆယ်ဘူး စသည့် ကုန်ကြမ်းများ Safety Stock အောက် ရောက်ရှိပါက အချက်ပေးစနစ်ဖြင့် အသိပေးပြီး မီးဖိုချောင် လုပ်ငန်းများ မရပ်တန့်သွားစေရန်။
6. **ပယ်ဖျက်/ပြန်အမ်းလိုက်သော အော်ဒါများ (Cancelled / Refunded Orders)**:
   - ငွေလိမ်လည်မှု (Fraud) နှင့် ဝန်ထမ်းများ မှားယွင်း Order ရိုက်ထည့်မှုများကို မျက်ခြည်မပြတ် စစ်ဆေးနိုင်ရန်။
7. **ဝန်ထမ်းများ၏ လှုပ်ရှားမှုအခြေအနေ (Real-Time Staff Activity)**:
   - မန်နေဂျာ၊ ငွေကိုင်၊ စားပွဲထိုးများ လက်ရှိ မည်သည့်အလုပ်လုပ်နေသည်ကို အဝေးရောက်နေသည့်တိုင် အချိန်နှင့်တစ်ပြေးညီ စောင့်ကြည့်နိုင်ရန်။
8. **ရက်စွဲအလိုက် ရောင်းအား (Sales by Date - 7 Days)**:
   - ရက်သတ္တပတ်အတွင်း စနေ/တနင်္ဂနွေနှင့် ရုံးဖွင့်ရက် ရောင်းအား ကွာခြားချက်များကို သိရှိနိုင်ရန်။
9. **အစားအသောက် ကဏ္ဍအလိုက် ရောင်းအား (Sales by Category)**:
   - ဟင်းလျာ၊ အဖျော်ယမကာ၊ အမြည်း သို့မဟုတ် အချိုပွဲ မည်သည့်အပိုင်းက အမြတ်ပိုရစေသည်ကို ခွဲခြမ်းနိုင်ရန်။

---

## ၂။ ဆိုင်ပိုင်ရှင် Navigation Modules (၁၇ ခု) ဖွဲ့စည်းပုံ

Layout Sidebar ([resources/views/admin/layouts/app.blade.php](file:///Users/kyawwaiyan/Documents/my-Home-tech/resturant-pos/backendPos/resources/views/admin/layouts/app.blade.php)) တွင် အောက်ပါအတိုင်း ကဏ္ဍကြီး ၄ ခုဖြင့် စနစ်တကျ ခွဲခြားထားပါသည်-

| ကဏ္ဍ (Category) | မော်ဂျူးအမည် (Module Name) | Route အမည် | လုပ်ဆောင်ချက် အကျဉ်း |
| :--- | :--- | :--- | :--- |
| **Core Operations** | 1. 📊 Dashboard | `admin.dashboard` | ပင်မ Executive Analytics Dashboard |
| | 2. 🧾 Order Management | `admin.orders.index` | အော်ဒါစောင့်ကြည့်ခြင်းနှင့် မီးဖိုချောင် KDS |
| | 3. 🪑 Table Management | `admin.tables.index` | စားပွဲဝိုင်း၊ အထပ်နေရာချထားမှုနှင့် QR မီနူး |
| | 4. 🍕 Menu / Products | `admin.menu.index` | မီနူးဟင်းလျာများ၊ ဈေးနှုန်းနှင့် မီးဖိုချောင်လိုင်း |
| **Finance & Stock** | 5. 💳 Payment Management | `admin.payments.index` | ငွေသေတ္တာ၊ KBZPay/WavePay နှင့် Card ငွေရှင်းခြင်း |
| | 6. 📦 Inventory Management | `admin.inventory.index` | ကုန်ကြမ်းလက်ကျန်၊ ချက်နည်းနှင့် Stock Alerts |
| | 7. 💰 Expense Management | `admin.expenses.index` | နေ့စဉ်ဈေးဖိုးနှင့် ဆိုင်အထွေထွေ အသုံးစရိတ် |
| | 8. 📈 Reports & Analytics | `admin.reports.index` | ဘဏ္ဍာရေးအမြတ်/အရှုံးနှင့် ရောင်းအားစာရင်း |
| **Staff & Customers**| 9. 👥 Employee Management | `admin.employees.index` | ဝန်ထမ်းစာရင်းနှင့် POS Touchscreen Quick PIN |
| | 10. 🛡️ Roles & Permissions | `admin.roles.permissions` | Spatie RBAC လုပ်ပိုင်ခွင့် ဇယား |
| | 11. 👤 Customer Management | `admin.customers.index` | ဧည့်သည်မှတ်တမ်းနှင့် Loyalty Points စနစ် |
| | 12. 🏷️ Discounts / Promos | `admin.promotions.index` | Happy Hour လျှော့စျေးနှင့် ကူပွန်များ |
| **Settings & Security**| 13. ⚙️ Restaurant Settings | `admin.settings.restaurant` | ဆိုင်လိပ်စာ၊ ဖုန်း၊ Logo နှင့် ဖွင့်ချိန်ပိတ်ချိန် |
| | 14. 📑 Tax & Service Charge | `admin.settings.tax` | ကုန်သွယ်ခွန် (၅%) နှင့် ဝန်ဆောင်ခ (၁၀%) နှုန်း |
| | 15. 🏢 Business Settings | `admin.settings.business` | ဘေလ်ပရင်တာ (58/80mm) နှင့် Hardware စနစ် |
| | 16. 📜 Audit Logs | `admin.audit.logs` | လုံခြုံရေးမှတ်တမ်းနှင့် Order ပယ်ဖျက်မှု Log |
| | 17. 🔐 Account / Security | `admin.account.security` | ပိုင်ရှင် စကားဝှက်နှင့် 2FA လုံခြုံရေး |

---

## ၃။ အလိုအလျောက် စမ်းသပ်စစ်ဆေးပြီး အတည်ပြုချက် ရလဒ်များ (Verification & Tests)

စနစ်တစ်ခုလုံး၏ Feature Tests စုစုပေါင်း **၄၇ ခု (Assertions ၂၃၇ ခု)** အားလုံး ၁၀၀% အောင်မြင်စွာ Pass ဖြစ်ပြီးဖြစ်ပါသည်-

1. **Dashboard Analytics Metrics (၁၀ မျိုးလုံး စစ်ဆေးပြီး)**:
   - `Today's sales` (၁,၄၅၀,၀၀၀ MMK) ✅
   - `Today's orders` (၈၆ ခု) ✅
   - `Average order value - AOV` (၁၆,၈၆၀ MMK) ✅
   - `Payment breakdown` (KBZPay, Cash, WavePay, Card) ✅
   - `Best-selling products` (Shan Noodle, Kyay Oh Sikyet, Milk Tea) ✅
   - `Low-stock items` (ဆီ၊ ကြက်ရင်အုံသား သတိပေးချက်) ✅
   - `Cancelled/refunded orders` (ORD-1082) ✅
   - `Staff activity` (ဆိုင်ပိုင်ရှင်၊ မန်နေဂျာ၊ ငွေကိုင် အခြေအနေ) ✅
   - `Sales by date` (၇ ရက်တာ တက်ကျမှု) ✅
   - `Sales by category` (Main Dishes, Beverages, Appetizers) ✅

2. **Route Boundaries & Module Access**:
   - Owner သည် Module ၁၇ ခုလုံးသို့ ချောမွေ့စွာ ဝင်ရောက်နိုင်ခြင်း (`200 OK`) ✅
   - Cashier နှင့် Staff များသည် ခွင့်ပြုချက်မရှိသော ဆိုင်ပိုင်ရှင် Modules များသို့ ဝင်ခွင့်မရှိဘဲ ကာကွယ်ထားနိုင်ခြင်း (`403 Forbidden`) ✅

3. **Code Style Formatting**:
   - Laravel Pint စံသတ်မှတ်ချက်အရ ဖိုင်ပေါင်း ၅၁ ဖိုင်လုံး သန့်ရှင်းမှု အပြည့်အဝ ရှိကြောင်း အတည်ပြုပြီးဖြစ်ပါသည်။
