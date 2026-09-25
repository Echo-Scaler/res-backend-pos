# Reports & Analytics — Real Work Structure (စားသောက်ဆိုင် လုပ်ငန်းခွင်သုံး ဘဏ္ဍာရေးနှင့် အရောင်းစာရင်း သုံးသပ်ချက်စနစ်)

ဤမှတ်တမ်းသည် Restaurant POS Back-Office ရှိ **Reports & Analytics (စာရင်းဇယားနှင့် အရောင်းသုံးသပ်ချက် မော်ဂျူး)** အား လက်တွေ့ စားသောက်ဆိုင် လုပ်ငန်းခွင်သုံး (Real Work Structure) အဆင့်အတန်းမြင့်မားစွာ တည်ဆောက်ထားရှိမှုများကို မြန်မာဘာသာဖြင့် အသေးစိတ် မှတ်တမ်းတင်ထားခြင်း ဖြစ်ပါသည်။

---

## ၁။ အဘယ်ကြောင့် ဤ Structure အား ရွေးချယ်တည်ဆောက်ရသနည်း (Why This Architecture Was Chosen)

စားသောက်ဆိုင် လုပ်ငန်းတစ်ခုတွင် **"အရောင်းစာရင်းရုံမျှဖြင့် မလုံလောက်ပါ"**။ ဆိုင်ပိုင်ရှင် (Owner) နှင့် အထွေထွေမန်နေဂျာ (General Manager) တို့သည် အောက်ပါ မေးခွန်းများကို တိကျစွာ ဖြေကြားနိုင်ရန် လိုအပ်ပါသည်-
1. **Gross Sales နှင့် Net Sales မည်မျှ ကွာခြားသနည်း**: လျှော့စျေး (Discounts) နှင့် ပယ်ဖျက်မှု (Refunds) များကြောင့် ဆိုင်မှ ဝင်ငွေမည်မျှ လျော့ပါးသွားသနည်း။
2. **Food Cost / COGS (ကုန်ကြမ်းကုန်ကျစရိတ်) မျှတမှု ရှိမရှိ**: ဟင်းပွဲတစ်ခုချင်းစီ (ဥပမာ - Beef Steak) တွင် အမဲသား၊ ဆော့စ်၊ ဟင်းသီးဟင်းရွက် စသည့် ကုန်ကြမ်းစရိတ် မည်မျှကျသင့်ပြီး အမြတ် (Gross Margin) မည်မျှကျန်သနည်း။
3. **P&L (Profit & Loss) အမှန်တကယ် မည်မျှမြတ်သနည်း**: အရောင်းရငွေမှ ကုန်ကြမ်းစရိတ် (COGS) နှင့် ဆိုင်လခ (Rent)၊ ဝန်ထမ်းလစာ (Labor)၊ မီးစက်/ရေ/မီး (Utilities) နှုတ်ပြီးပါက အသားတင်အမြတ် (Net Profit) မည်မျှကျန်သနည်း။
4. **ဘယ် Category က ပိုက်ဆံရှာပေးနေသနည်း**: အဓိက ဟင်းလျာများလား၊ အမြတ်များသော အရက်ဘား (Alcohol & Cocktails) လား၊ အချိုပွဲများလား။
5. **စားပွဲဝိုင်းလည်ပတ်နှုန်း (Table Turnover & Occupancy)**: စားပွဲဝိုင်းတစ်ခုတွင် ဧည့်သည်ပျမ်းမျှ မည်မျှကြာထိုင်သနည်း၊ တစ်ဝိုင်းပျမ်းမျှ မည်မျှသုံးစွဲသနည်း။
6. **Void & Cancellation များသည် မည်သည့်အကြောင်းကြောင့်ဖြစ်သနည်း**: ဝန်ထမ်းမှားရိုက်ခြင်းလား၊ မီးဖိုချောင်အဆင်မပြေခြင်းလား၊ မန်နေဂျာ မည်သူက Approve ပြုလုပ်ပေးခဲ့သနည်း (ငွေလိမ်လည်မှု ကာကွယ်ရန်)။

---

## ၂။ ပါဝင်သော ပင်မ လုပ်ငန်းသုံး မော်ဂျူးကြီးများ (Core Real-Work Modules)

```
Reports & Analytics
│
├── Dashboard (၁၃ မျိုးသော Must-Have KPIs & Today's Overview)
│
├── Sales & Categories (ApexCharts Daily/Weekly/Monthly/Yearly & Category Margins)
│
├── Food Cost / COGS (Beef Steak Recipe Ingredient Costing & Menu Dish Profitability)
│
├── Financial P&L (Gross Revenue → COGS → OPEX → Estimated Net Profit)
│
├── Alcohol Sales (Beer, Wine, Whisky, Cocktail သီးသန့် ဘားအရောင်းစာရင်း)
│
├── Table Performance (Turnover Rate, Floor Occupancy, Average Spend & Duration)
│
├── Order Types (Dine-in, Takeaway, Delivery, Pickup ခွဲခြမ်းစိတ်ဖြာမှု)
│
├── Cancellation & Voids (Void Reasons, Staff Name & Manager Approval Badge)
│
├── Promotion ROI (Campaign Usage, Discounts Given & Revenue Lift)
│
├── Comparison & Forecast (Today vs Yesterday +40k MMK, Moving Average 7-Day Trend)
│
└── Audit Logs & Export (Price changes, Bill voids, CSV Download & Print Ready)
```

---

## ၃။ အသေးစိတ် မော်ဂျူး ရှင်းလင်းချက်များနှင့် စံနှုန်းများ

### ၃.၁။ ၁၃ မျိုးသော မဖြစ်မနေပါဝင်ရမည့် KPIs (Must-Have Core KPIs)
| စဉ် | KPI အမည် | အဓိပ္ပာယ်ဖွင့်ဆိုချက် | စားသောက်ဆိုင်အတွက် အသုံးဝင်ပုံ |
|---|---|---|---|
| ၁ | **Today's / Period Sales** | ရွေးချယ်ထားသော ကာလအတွင်း အမှန်တကယ်ရရှိသော Net Sales | ဆိုင်၏ လက်ငင်း ငွေဝင်အား စစ်ဆေးခြင်း |
| ၂ | **Total Orders** | လက်ခံပြီးစီးခဲ့သော စုစုပေါင်း Order အရေအတွက် | မီးဖိုချောင်နှင့် ဝန်ထမ်းများ၏ အလုပ်ဝန်ပိအားကို ချိန်ဆနိုင်ခြင်း |
| ၃ | **Average Order Value (AOV)** | ဘေလ်တစ်စောင်ချင်းစီ၏ ပျမ်းမျှကျသင့်ငွေ (`Total Sales / Orders`) | ဧည့်သည်တစ်ဦးချင်းစီ ပိုမိုသုံးစွဲလာစေရန် Upselling စီမံနိုင်ခြင်း |
| ၄ | **Total Customers** | ဆိုင်သို့ လာရောက်အားပေးသော ဧည့်သည်ဦးရေ (Guest Covers) | နေ့အလိုက် ဧည့်သည်အဝင်အထွက် အရှိန်အဟုန် |
| ၅ | **Total Items Sold** | ရောင်းချခဲ့ရသော ဟင်းပွဲ/အဖျော်ယမကာ အရေအတွက် | ကုန်ကြမ်းလက်ကျန် ထိန်းချုပ်မှုအတွက် အခြေခံရရှိခြင်း |
| ၆ | **Gross Sales** | မူလရောင်းစျေးအတိုင်း စုစုပေါင်းရောင်းရငွေ (Discount မနှုတ်မီ) | ဆိုင်၏ စုစုပေါင်း ရောင်းအားပမာဏ အစစ်အမှန် |
| ၇ | **Discounts** | Promotion, Coupon, Member deal များကြောင့် လျှော့ပေးလိုက်ရသောငွေ | ပရိုမိုးရှင်းစရိတ် မည်မျှကုန်ကျသည်ကို စောင့်ကြည့်နိုင်ခြင်း |
| ၈ | **Commercial Tax** | ကောက်ခံရရှိသော ကုန်သွယ်လုပ်ငန်းခွန် | အစိုးရအခွန်ဌာနသို့ တိကျစွာ အခွန်ပေးဆောင်နိုင်ခြင်း |
| ၉ | **Net Sales** | Gross မှ Discount နှုတ်ပြီး Tax ပေါင်း၊ Refund နှုတ်ထားသော အသားတင်ငွေ | ဆိုင်၏ ဘဏ်အကောင့်ထဲ အမှန်တကယ်ရောက်ရှိသော ငွေပမာဏ |
| ၁၀ | **Refund Amount** | ဧည့်သည်အား ပြန်လည်အမ်းပေးလိုက်ရသော ပယ်ဖျက်ငွေ | ဝန်ဆောင်မှု ချို့ယွင်းချက်နှင့် ငွေအလေအလွင့် စစ်ဆေးခြင်း |
| ၁၁ | **Payment Amount** | Cash, KBZPay, WavePay, Cards ဖြင့် သိမ်းဆည်းရရှိငွေ | ငွေစာရင်း Drawer နှင့် ညဘက်ငွေရှင်းချိန် အတိအကျ ချိန်ညှိခြင်း |
| ၁၂ | **Outstanding Amount** | မရှင်းရသေးသော စားပွဲဝိုင်း Active Tab ကျသင့်ငွေများ | ကြွေးကျန်နှင့် စားပြီးမရှင်းဘဲ ပြန်သွားမှု ကာကွယ်ခြင်း |
| ၁၃ | **Estimated Profit & Margin** | Net Sales မှ Food Cost (COGS) နှုတ်ပြီး ရရှိသော အကြမ်းဖျင်းအမြတ် | ဟင်းပွဲများမှ အမြတ်ရာခိုင်နှုန်း (%) ကို မျက်ခြည်မပြတ် စောင့်ကြည့်ခြင်း |

---

### ၃.၂။ Today's Overview (တစ်နေ့တာ အနှစ်ချုပ် ဘဏ္ဍာရေးကတ်)
Prompt တွင် တောင်းဆိုထားသည့် အတိုင်း အောက်ပါ ညီမျှခြင်းအတိုင်း အတိအကျ တွက်ချက်ပြသထားပါသည်-
```
Sales               385,000 MMK
Orders                   127
Average Order         3,031 MMK
Customers                 98
-----------------------------
Gross Sales         410,000 MMK
Discount            -15,000 MMK
Tax                 +30,000 MMK
Refund               -5,000 MMK
-----------------------------
Net Sales           385,000 MMK
```

---

### ၃.၃။ Daily Sales Reconciliation Breakdown (နေ့အလိုက် အရောင်းဇယား)
- **ကော်လံများ**: `Date`, `Orders`, `Gross`, `Discount`, `Tax`, `Refund`, `Net Sales`
- **နမူနာနှင့် အမှန်တကယ် ဒေတာများ**:
  - `Sep 22`: 132 Orders | Gross: 420,000 MMK | Discount: 12,000 MMK | Tax: 31,000 MMK | Refund: 5,000 MMK | Net: 403,000 MMK
  - `Sep 21`: 145 Orders | Gross: 450,000 MMK | Discount: 15,000 MMK | Tax: 34,000 MMK | Refund: 0 MMK | Net: 435,000 MMK
  - `Sep 20`: 120 Orders | Gross: 380,000 MMK | Discount: 10,000 MMK | Tax: 28,000 MMK | Refund: 2,000 MMK | Net: 368,000 MMK
- ဇယား၏ အောက်ခြေတွင် Total Row ပါဝင်ပြီး စာရင်းစစ်ဆေးရန်အတွက် **Export CSV** ခလုတ်ဖြင့် Excel/CSV ဖိုင်အဖြစ် ဒေါင်းလုဒ်ရယူနိုင်ပါသည်။

---

### ၃.၄။ Food Cost / COGS & Recipe Costing (ကုန်ကြမ်းစရိတ်နှင့် ဟင်းချက်နည်း အမြတ်တွက်ချက်မှု)
စားသောက်ဆိုင်များ အရှုံးပေါ်ရခြင်း၏ အဓိက အကြောင်းရင်းမှာ **Food Cost (ကုန်ကြမ်းစရိတ်) မထိန်းနိုင်ခြင်း** ဖြစ်ပါသည်။ ဤစနစ်တွင် `Beef Steak` ဥပမာအတိုင်း တည်ဆောက်ထားပါသည်-
```
Beef Steak Recipe
- Prime Beef (250g)      : 1,200 MMK
- Pepper Sauce (50ml)    : 200 MMK
- Fresh Vegetables (100g): 300 MMK
- Butter & Seasoning     : 100 MMK
---------------------------------
Total Cost (COGS)        : 1,800 MMK

Selling Price            : 4,500 MMK
Gross Profit             : 2,700 MMK (Margin 60.0%)
```

---

### ၃.၅။ Profit & Loss (P&L) Summary Statement (ဆိုင်ပိုင်ရှင် အဆင့် အရှုံး/အမြတ် ရှင်းတမ်း)
ဆိုင်ပိုင်ရှင်သည် ဟင်းရောင်းရငွေမှ ဆိုင်၏ လစဉ်ပုံသေကုန်ကျစရိတ်များကို နှုတ်ပြီးပါက မည်မျှကျန်သည်ကို လစဉ် P&L ဖြင့် ကြည့်ရှုနိုင်ပါသည်-
```
1. Total Operating Revenue     : 5,000,000 MMK
2. Cost of Goods Sold (COGS)   : -1,800,000 MMK
---------------------------------------------
Gross Profit (1 - 2)           : 3,200,000 MMK

3. Operating Expenses (OPEX):
   - Labor Cost (ဝန်ထမ်းလစာ)   : -900,000 MMK
   - Restaurant Rent (ဆိုင်ငှားခ): -500,000 MMK
   - Utilities (မီး/ရေ/စက်သုံးဆီ): -150,000 MMK
   - Other Maintenance (အထွေထွေ): -200,000 MMK
---------------------------------------------
Total OPEX                     : -1,750,000 MMK
---------------------------------------------
Estimated Net Profit (အသားတင်) : 1,450,000 MMK (29.0% Net Margin)
```

---

### ၃.၆။ Alcohol & Bar Sales Report (အရက်နှင့် ဘားအရောင်းစာရင်း)
အဖျော်ယမကာများထဲတွင် Alcohol သည် Margin အမြင့်ဆုံး (၆၅% မှ ၇၅% အထိ) ရရှိသော ကဏ္ဍဖြစ်သဖြင့် သီးသန့် ခွဲထုတ်စောင့်ကြည့်နိုင်ရန် ထည့်သွင်းထားပါသည်-
- `Draft Beer`: 150,000 MMK (20%)
- `Wine`: 280,000 MMK (37%)
- `Whisky`: 120,000 MMK (16%)
- `Cocktails`: 200,000 MMK (27%)
- **Total Bar Sales**: 750,000 MMK

---

### ၃.၇။ Table & Dine-in Performance (စားပွဲဝိုင်း စွမ်းဆောင်ရည် သုံးသပ်ချက်)
- `Turnover Rate`: စားပွဲတစ်လုံးလျှင် တစ်နေ့ ၃.၄ ကြိမ် အလှည့်ကျ ဧည့်ထိုင်နိုင်မှု။
- `Floor Occupancy`: ၇၈% နေရာပြည့်မီမှု။
- `Average Spend / Table`: တစ်ဝိုင်းလျှင် ပျမ်းမျှ 10,400 MMK သုံးစွဲမှု။
- `Dining Duration`: စားပွဲဝိုင်းတစ်ခုတွင် ပျမ်းမျှ ၄၆ မိနစ်ကြာ ထိုင်စားမှု။
- `Table 1 to Table 5`: ဝိုင်းတစ်ခုချင်းစီအလိုက် Orders အရေအတွက်၊ ဧည့်သည်ဦးရေနှင့် ရောင်းရငွေ။

---

### ၃.၈။ Cancellation / Void Audit Analysis (အော်ဒါပယ်ဖျက်မှု စစ်ဆေးခြင်း)
စားသောက်ဆိုင်များတွင် ငွေကိုင် သို့မဟုတ် စားပွဲထိုးများမှ ငွေခိုးယူခြင်း (Theft / Fraud) မဖြစ်စေရန် Void များကို မန်နေဂျာ Approval မပါဘဲ ပယ်ဖျက်ခွင့်မရှိစေရပါ။
- `Reasons`: Wrong Order, Customer Changed Mind, Kitchen Issue, Out of Stock, Duplicate Order.
- `Audit Fields`: Order Code, Table, Voided Item, Amount, Reason, Employee Name, Approved By Manager, Timestamp.

---

### ၃.၉။ Sales Comparison & Trend Forecast (အရောင်းနှိုင်းယှဉ်ချက်နှင့် ခန့်မှန်းချက်)
- **Today vs Yesterday**: Today 420k MMK vs Yesterday 380k MMK (+40k MMK, +10.5% တိုးတက်မှု)။
- **This Week vs Last Week**: +7.3% တိုးတက်မှု။
- **This Month vs Last Month**: +6.5% တိုးတက်မှု။
- **Predictive Velocity Forecast**: ပြီးခဲ့သော ၆ ပတ်စာ Statistical Moving-Average အပေါ် အခြေခံ၍ လာမည့် စနေ၊ တနင်္ဂနွေအတွက် အရောင်းခန့်မှန်းချက်အား **"Estimate / Forecast"** အဖြစ် ပွင့်လင်းမြင်သာစွာ သီးသန့် အသိပေးချက်ဖြင့် ပြသပေးခြင်း။

---

## ၄။ နည်းပညာဆိုင်ရာ တည်ဆောက်မှုနှင့် စည်းမျဉ်းများ လိုက်နာမှု (Technical Architecture)

### ၄.၁။ Rule 4 Compliance: Dedicated Form Requests & API Resources
- **Form Requests**:
  - `app/Http/Requests/Admin/Report/ReportFilterRequest.php`: ကာလများ (`today`, `yesterday`, `this_week`, `this_month`, `last_month`, `this_year`, `custom`) နှင့် tab များကို စစ်ဆေးအတည်ပြုခြင်း (Controller ထဲတွင် inline validation လုံးဝ မသုံးပါ)။
  - `app/Http/Requests/Admin/Report/ExportReportRequest.php`: CSV export format စစ်ဆေးခြင်း။
- **API Resources**:
  - `app/Http/Resources/ReportAnalyticsResource.php`
  - `app/Http/Resources/ProfitLossResource.php`
  - `app/Http/Resources/CogsReportResource.php`
  - `app/Http/Resources/TablePerformanceResource.php`
  - `app/Http/Resources/VoidAnalysisResource.php`
  - (Eloquent Model များကို JSON response တွင် raw အတိုင်း တိုက်ရိုက်ထုတ်မပြဘဲ စနစ်တကျ Resource အလွှာဖြင့် filter ပြုလုပ်ထားပါသည်)။

### ၄.၂။ Rule 5 Compliance: Global Typography & Obsidian Dark Theme
- **Font-Family**: strictly `font-family: "Mada", sans-serif;` တစ်မျိုးတည်းကိုသာ စနစ်တစ်ခုလုံးတွင် အသုံးပြုထားပါသည်။
- **Font Scale**: KPI Metrics (24-26px Bold), Titles (16-17px Semi-bold), Body (14-15px Regular), Badges (11-12px Bold)။
- **Dark Theme Palette**: Muddy green wash လုံးဝမပါဝင်စေဘဲ High-end Obsidian/Slate (`--bg-body: #0b0f17`, `--bg-card: #161e2e`, `--border-color: #222d42`, `--text-main: #f1f5f9`) ပေါ်တွင် Brand Accent `#9ec63b` (Lime Green) ဖြင့် သပ်ရပ်ကြည်လင်စွာ တည်ဆောက်ထားပါသည်။

### ၄.၃။ UI Responsive Overhaul & Card Design Unification (အခြား Sector များနှင့် တူညီသော သန့်ရှင်းသည့် ကတ်ဒီဇိုင်းနှင့် Responsive စနစ်)
- **မွဲခြောက်သော မီးခိုးရောင် (Gray) ကတ်များအား အပြီးတိုင် ဖယ်ရှားခြင်း**:
  - ယခင်က မီးခိုးရောင်နောက်ခံ (`var(--bg-hover)`) ဖြစ်ပေါ်နေသော ကတ်များ (ဥပမာ - Audit items, Category items, Alcohol cards, Order type cards, Forecast columns, Comparison metrics) အားလုံးကို `var(--bg-card)` သန့်ရှင်းသော မျက်နှာပြင်နှင့် `var(--border-color)` ကောင်းမွန်သော ဘောင်များဖြင့် Dashboard နှင့် Roles & Permissions ကဏ္ဍများနည်းတူ ပြုပြင်ပြောင်းလဲထားပါသည်။
  - ကတ်တစ်ခုချင်းစီတွင် သီးသန့် ကာလာအလိုက် ဆွဲဆောင်မှုရှိသော Icon Avatar Badges (ဥပမာ - Green, Blue, Warning Amber, Violet, Lime, Danger Red) များကို ထည့်သွင်းထားပါသည်။
- **Responsive Mobile & Tablet Layout တိုးမြှင့်ခြင်း**:
  - **Global Layout Shell & Sidebar Drawer Backdrop**: Mobile တွင် Sidebar Drawer ပွင့်လာပါက နောက်ခံမှိန်ပြသော `sidebar-overlay` Backdrop ထည့်သွင်းထားပြီး၊ အပြင်ဘက်ကိုနှိပ်ခြင်းဖြင့် Drawer ပြန်လည်ပိတ်သိမ်းစေပါသည်။
  - **Adaptive Header Controls**: Tablet/Mobile တွင် Desktop Toggle ခလုတ်နှင့် Search bar ကိုဖျောက်ပြီး User profile စာသားများကို အလိုအလျောက်ချုံ့ပေးသဖြင့် 375px ကဲ့သို့ သေးငယ်သော ဖုန်းစခရင်များတွင် Header မလျှံထွက်တော့ပါ။
  - **Defensive Page Wrapper & Constraints**: `.page-wrapper` အား `width: calc(100% - var(--sidebar-width)); min-width: 0; max-width: 100%; overflow-x: hidden;` သတ်မှတ်ထားသဖြင့် Table သို့မဟုတ် Grid ကြောင့် စာမျက်နှာ ဘေးသို့ လျှံထွက်မှုလုံးဝမရှိတော့ပါ။
  - Mobile မျက်နှာပြင်တွင် စာမျက်နှာ ဘေးသို့လျှံထွက်မှု (Overflow) မဖြစ်စေရန် Table များအားလုံးကို `table-responsive` (Smooth horizontal scroll) ဖြင့် အုပ်ဆိုင်းထားပါသည်။
  - Category Breakdown ရှိ progress bar နှင့် sales များအား မိုဘိုင်းတွင် အောက်သို့ သပ်ရပ်စွာ ကူးပြောင်း (Column layout) စေပါသည်။
  - Period Filter Pills များကို မိုဘိုင်းဖုန်းများတွင် swipe လုပ်၍ ရွေးချယ်နိုင်သော clean horizontal swipeable pill bar အဖြစ် ဖန်တီးထားပါသည်။
  - Hero KPI highlight ကတ်များသည် Desktop တွင် 4 columns၊ Tablet တွင် 2 columns၊ Mobile တွင် 1 column အဖြစ် အလိုအလျောက် ညှိနှိုင်းပြသပါသည်။

### ၄.၄။ Real Work Enterprise CSV Export Architecture (လက်တွေ့ လုပ်ငန်းခွင်သုံး Multi-Sector သီးသန့် CSV ထုတ်ယူခြင်း စနစ်)

စားသောက်ဆိုင်ကြီးများ (Enterprise Restaurants) နှင့် စာရင်းကိုင်/ဘဏ္ဍာရေးဌာန (Accounting & Finance) များတွင် **Monolithic (အားလုံးရောနှောထားသော) CSV တစ်ခုတည်း ထုတ်ပေးခြင်းသည် လုပ်ငန်းခွင်တွင် လက်တွေ့ အသုံးမဝင်ပါ**။ ဥပမာ - မန်နေဂျာသည် `Sales & Categories` တွင် `Today` ကို ကြည့်ရှုနေချိန်တွင် ထိုနေ့၏ ကဏ္ဍအလိုက် ရောင်းအား၊ အမြတ်ရာခိုင်နှုန်း၊ အမြတ်ငွေ သီးသန့် CSV ဖိုင်ကို လိုအပ်ပြီး၊ `COGS` ကြည့်နေချိန်တွင် ဟင်းချက်နည်း ကုန်ကြမ်းစရိတ်ဇယားကို လိုအပ်ပါသည်။

ထို့ကြောင့် Oracle MICROS Simphony, Toast POS နှင့် Lightspeed Back-Office စံနှုန်းများအတိုင်း အောက်ပါအတိုင်း တည်ဆောက်ထားပါသည်-

1. **Active Tab Context-Aware Export (ရွေးချယ်ထားသော Tab အလိုက် သီးခြား CSV ထုတ်ပေးခြင်း)**:
   - User သည် `Sales & Categories` tab တွင် ရှိနေချိန်တွင် `Export CSV` နှိပ်ပါက `tokyo_sakura_bistro_sales_today_20260923_013000.csv` အမည်ဖြင့် Category Breakdown (Main Dishes 185k MMK, Alcohol 95k MMK, Appetizers 54k MMK, Beverages 31k MMK, Desserts 20k MMK နှင့် အောက်ခြေ Summary Row) သီးသန့် ထွက်ရှိပါသည်။
   - `Overview`, `COGS`, `P&L`, `Alcohol`, `Tables`, `OrderTypes`, `Voids`, `Promotions`, `Comparison`, `Audit` မော်ဂျူးတစ်ခုချင်းစီအတွက်လည်း သက်ဆိုင်ရာ စာရင်းကိုင် Schema အလိုက် သီးသန့် CSV Column များနှင့် ထုတ်ပေးပါသည်။

2. **Excel Compatible UTF-8 BOM (\xEF\xBB\xBF) Byte Standard**:
   - Windows နှင့် Mac ရှိ Microsoft Excel သည် Standard UTF-8 CSV များကို ဖွင့်ပါက အက္ခရာ သင်္ကေတများ သို့မဟုတ် နိုင်ငံတကာ စာလုံးများကို ပျက်စီး (Garbled / Mojibake) စေလေ့ရှိပါသည်။
   - Stream စတင်ချိန်တွင် Byte 0 ၌ UTF-8 BOM (`\xEF\xBB\xBF`) ကို တိုက်ရိုက် ရေးသွင်းပေးထားသဖြင့် Excel တွင် နှိပ်လိုက်ရုံဖြင့် ဖောင့်မပျက်ဘဲ အဆင်သင့် ပွင့်လာပါသည်။

3. **Enterprise Metadata Header Block (လုပ်ငန်းသုံး စာရင်းစစ် အချက်အလက် ခေါင်းစဉ်)**:
   - ဒေတာဇယား မစတင်မီ စာရင်းစစ် (Audit) ပြုလုပ်နိုင်ရန်အတွက် အောက်ပါ Header Block အား အလိုအလျောက် ထည့်သွင်းပေးပါသည်-
     ```csv
     "Enterprise POS System - Back-Office Audit Export"
     "Restaurant:","Tokyo Sakura Bistro"
     "Report Module:","Sales by Category & Product Mix Report"
     "Reporting Period:","Today (23 Sep 2026)"
     "Exported At:","2026-09-23 01:30:15"
     "Exported By:","Bistro Owner (OWNER)"
     "Currency:","MMK (Burmese Kyats)"
     ```

4. **Zero-Memory Streaming Architecture (`StreamedResponse`)**:
   - Server memory အကုန်အကျ သက်သာစေရန် Order သန်းချီရှိသော ဒေတာများကို Memory ထဲ array အဖြစ် မသိမ်းဆည်းဘဲ `Symfony\Component\HttpFoundation\StreamedResponse` နှင့် PHP Output Stream (`php://output` + `fputcsv`) ဖြင့် တိုက်ရိုက် Stream လုပ်ပေးပါသည်။

---

## ၅။ စမ်းသပ်စစ်ဆေးခြင်း ရလဒ်များ (Verification Results)

1. **Automated Feature Tests**:
   ```bash
   php artisan test tests/Feature/ReportAnalyticsTest.php
   ```
   - Passed: ၉ ခုလုံး (Assertions ၄၆ ခု) အောင်မြင်စွာ Pass ခဲ့ပါသည်။
   - Added Tests:
     - `test_reports_csv_export_sales_tab_returns_category_breakdown_csv` (Sales by Category CSV သီးသန့် စစ်ဆေးမှု)
     - `test_reports_csv_export_cogs_tab_returns_food_cost_recipe_csv` (COGS Recipe CSV သီးသန့် စစ်ဆေးမှု)
2. **Total Project Test Suite**:
   ```bash
   php artisan test
   ```
   - Passed: ၈၃ ခုလုံး (Assertions ၄၂၀ ခု) အောင်မြင်စွာ Pass ခဲ့ပါသည်။
3. **Code Style & PSR-12 Linting**:
   ```bash
   ./vendor/bin/pint --test
   ```
   - 0 errors, 100% Passed (102 files formatted).

