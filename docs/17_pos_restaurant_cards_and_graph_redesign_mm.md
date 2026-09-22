# အခန်း (၁၇) - စားသောက်ဆိုင် POS လုပ်ငန်းသုံး ခေတ်မီ Card Graphs ပြန်လည်ဒီဇိုင်းရေးဆွဲခြင်း မှတ်တမ်း

## ၁။ အနှစ်ချုပ် (Overview)
ယခင် SaaS Financial Mockup စတိုင်ဖြင့် ထည့်သွင်းထားသော ကတ်ဟောင်း (၂) ခု (Actual Sales vs Target နှင့် Expense Breakdown Donut Chart) အား အစားထိုး၍ **စားသောက်ဆိုင် POS လုပ်ငန်း၏ လက်တွေ့လိုအပ်ချက်များနှင့် ၁၀၀% ကိုက်ညီသော ခေတ်မီ Card Graphs (၂) ခု** အဖြစ် အဆင့်မြှင့်တင် ရေးဆွဲခဲ့သည်။

အသစ်ထည့်သွင်းလိုက်သော ကတ် (၂) ခုမှာ -
1. **Weekly Revenue Velocity & Dine-in vs Takeaway Trend (Area Spline Chart)**: ဆိုင်ထိုင်စား (Dine-in) နှင့် ပါဆယ်/ပို့ဆောင်ရေး (Takeaway) နေ့စဉ်ရောင်းအား တိုးတက်မှုအား အစိမ်းနုရောင် Gradient Fill ဖြင့် ဖော်ပြသော ချောမွေ့သည့် ဧရိယာဂရပ်။
2. **Hourly Dining Traffic & Kitchen Rush Load (Interactive Column Chart)**: နေ့လယ်စာနှင့် ညစာ ရောင်းအားအမြင့်ဆုံး အချိန်များ (Peak Rush Hours: 12 PM & 7 PM)၊ မီးဖိုချောင် အော်ဒါဝန်ထုပ်ဝန်ပိုးနှင့် ပျမ်းမျှ အစားအသောက်ပြင်ဆင်ချိန်တို့ကို အချိန်ပိုင်းအလိုက် ခွဲခြားပြသသော Bar Graph စနစ်။

ထို့အပြင် Top 4 Financial KPI ကတ်များရှိ `$` သင်္ကေတများအား မြန်မာကျပ်ငွေ (`MMK`) သို့ ပြောင်းလဲခြင်း၊ Dark Mode တွင် နောက်ခံအရောင်များ အဆင်မပြေဖြစ်ခြင်းအား သဘာဝသစ်တောစိမ်းရင့်ရောင်စနစ်ဖြင့် အပြည့်အဝ ကိုက်ညီစေရန် ပြင်ဆင်ခြင်းနှင့် စာလုံးဖောင့် (Font Family: Inter) ၏ အထူ/အပါး (Font Weight) တို့ကို ပိုမိုသက်သောင့်သက်သာ ဖတ်ရှုနိုင်စေရန် ညှိနှိုင်းပြင်ဆင်ခဲ့သည်။

---

## ၂။ အဘယ်ကြောင့် ဤ Card Graphs (၂) ခုကို ပြောင်းလဲရွေးချယ်ရသနည်း (Why)

### (က) စားသောက်ဆိုင် POS လုပ်ငန်းအတွက် လက်တွေ့ အသုံးဝင်မှု (Real-world Restaurant Utility)
- ယခင်ကတ်ဟောင်းများသည် ကော်ပိုရိတ်ဘဏ္ဍာရေး စာရင်းကိုင်များ ကြည့်ရှုသည့် လအလိုက် ငွေစာရင်း (ဥပမာ - ရုံးခန်းငှားရမ်းခ၊ ဝန်ထမ်းလစာ) ဖြစ်နေသဖြင့် ဆိုင်ရှင်နှင့် မန်နေဂျာများ နေ့စဉ် ဆိုင်လည်ပတ်ရေး (Daily Operations) အတွက် အသုံးမဝင်ခဲ့ပါ။
- ယခု အသစ်ပြောင်းလဲထားသော ဂရပ်များမှာ -
  1. **နေ့စဉ် ရောင်းအားနှင့် ဝန်ဆောင်မှုအမျိုးအစား ခွဲခြမ်းစိတ်ဖြာမှု**: ဆိုင်ထိုင်စား ဝင်ငွေ (၇၀%) နှင့် ပါဆယ်ဝင်ငွေ (၃၀%) အချိုးအစားကို သိရှိနိုင်သဖြင့် ထုပ်ပိုးပစ္စည်းနှင့် စားပွဲပြင်ဆင်မှု လိုအပ်ချက်ကို ကြိုတင်ခန့်မှန်းနိုင်သည်။
  2. **အချိန်ပိုင်းအလိုက် အော်ဒါစီးဆင်းမှု (Hourly Peak Traffic)**: နေ့လယ် (၁၂ မှ ၁ နာရီ) နှင့် ညနေ (၆ မှ ၈ နာရီ) ရောင်းအား အမြင့်ဆုံး Rush Hours များကို အရောင်ခွဲခြား ပြသထားသဖြင့် စားပွဲထိုးဝန်ထမ်း အဆိုင်းခွဲဝေမှု (Shift Scheduling) နှင့် မီးဖိုချောင် ကြိုတင်ပြင်ဆင်မှု (Kitchen Prep / Mise-en-place) ကို ထိရောက်စွာ စီမံခန့်ခွဲနိုင်သည်။

### (ခ) Dark Mode အလင်းပြန်မှုနှင့် မျက်စိညောင်းညာမှု မရှိစေရန် ဖြေရှင်းခြင်း (True Dark Mode Sync)
- ယခင်ကတ်များသည် Dark Screen ဖြစ်နေချိန်တွင် အဖြူရောင်စစ်စစ် (`#ffffff`) ဖြင့် ထင်းထွက်နေပြီး အလင်းပြန်၍ မျက်စိစူးရှစေခဲ့သည်။
- ယခုအခါ `:root` နှင့် `[data-theme="dark"]` အတွက် `var(--bg-card)` (`#152219`), `var(--border-color)` (`#233828`), `var(--text-main)` (`#f0f7f1`) တို့ကို စနစ်တကျ အသုံးပြုထားသဖြင့် Light Mode တွင် သန့်ရှင်းကြည်လင်ပြီး Dark Mode တွင် ညင်သာအေးမြသော သစ်တောစိမ်း အနက်ရောင်ဖြင့် အလွန်ကြည့်ကောင်းသည်။

### (ဂ) မြန်မာကျပ်ငွေစနစ် (`MMK`) နှင့် ဖောင့်အလေးချိန် ညှိယူခြင်း (Font Weight Optimization)
- စားသောက်ဆိုင်သုံး စနစ်ဖြစ်သည့်အတိုင်း ဒေါ်လာသင်္ကေတ (`$`) အစား မြန်မာကျပ်ငွေ `MMK` သို့ တိကျစွာ ပြောင်းလဲခဲ့သည်။
- စာသားများအားလုံးကို အလွန်အမင်း ထူလွန်းသော Bold (`800/900`) အစား ရှင်းလင်းပြတ်သားပြီး မျက်စိအေးချမ်းသော `font-weight: 600` နှင့် `700` သို့ လျှော့ချညှိယူပေးခဲ့သည်။

---

## ၃။ အသေးစိတ် အစိတ်အပိုင်းများနှင့် နည်းပညာ အကောင်အထည်ဖော်မှု (Technical Specifications)

### ၁။ Card 1: Weekly Revenue Velocity (ApexCharts Spline Area)
- **Container ID**: `#chart-pos-revenue-trend`
- **Graph Type**: `area` (Smooth Spline Curve with Gradient Fill)
- **Data Series**:
  - `Dine-In Orders` (ရောင်းအား၏ ၇၀%) - Brand Lime Green (`#9ec63b`)
  - `Takeaway / Delivery` (ရောင်းအား၏ ၃၀%) - Medium Olive Green (`#5c8623`)
- **Top Metrics Row**:
  - ၇ ရက်တာ စုစုပေါင်းရောင်းအား: `9,460,000 MMK`
  - တစ်ရက် ပျမ်းမျှဝင်ငွေ: `1,351,400 MMK`
  - ဆိုင်ထိုင်စား ရောင်းအားဝေစု: `70% (Lime Highlight)`
- **Interactive Features**: Tooltip ပေါ်တွင် Dine-in နှင့် Takeaway ကျပ်ငွေပမာဏကို Theme အလိုက် သီးခြားလှပစွာ ထုတ်ပြပေးခြင်း။

### ၂။ Card 2: Hourly Dining Traffic & Kitchen Rush (ApexCharts Dynamic Bar)
- **Container ID**: `#chart-pos-peak-hours`
- **Graph Type**: `bar` (Column Width 46%, Border Radius 5px)
- **Operating Hours**: 11 AM မှ 10 PM အထိ အချိန် ၁၂ ပိုင်း ခွဲခြားပြသခြင်း
- **Dynamic Color Threshold**:
  - အော်ဒါ ၄၀ နှင့်အထက် (Lunch Peak 12 PM): Brand Primary Lime (`#9ec63b`)
  - အော်ဒါ ၃၀ မှ ၃၉ ကြား (Dinner Rush 6-7 PM): Leaf Green (`#7ea826`)
  - အော်ဒါ ၂၀ မှ ၂၉ ကြား: Olive Green (`#5c8623`)
  - ပုံမှန်အချိန်များ: Dark mode တွင် `#253d29` / Light mode တွင် `#d5dfcd`
- **Custom HTML Tooltip**: အချိန်အပိုင်းအခြား၊ လက်ခံရရှိသော လက်မှတ်အရေအတွက်နှင့် Rush Level Badge (ဥပမာ - `🔥 Peak Dining Rush`, `⚡ High Kitchen Load`) တို့ကို တိုက်ရိုက် ပြသပေးခြင်း။

---

## ၄။ ဖိုင်များ ပြင်ဆင်ခဲ့သည့် စာရင်း (Modified Files)

1. `backendPos/resources/views/admin/dashboard.blade.php`:
   - `.financial-kpi-card`: စတာလင်/ဒေါ်လာအစား `MMK` ပြောင်းလဲခြင်းနှင့် Dark Mode တွင် `var(--bg-card)` ဖြင့် ကိုက်ညီစေခြင်း။
   - `.financial-main-grid`: ကတ် (၂) ခုလုံး ညီညာတပြေးညီဖြစ်စေရန် `grid-template-columns: 1fr 1fr;` သို့ ချိန်ညှိခြင်း။
   - HTML Structure: ယခင် Actual Sales/Expense Donut အား ဖယ်ရှား၍ `#chart-pos-revenue-trend` နှင့် `#chart-pos-peak-hours` ကတ်သစ်များ အစားထိုးခြင်း။
   - JavaScript Section: ApexCharts Area Graph နှင့် Bar Graph တို့အတွက် Light/Dark Theme Adaptive Options များ အပြည့်အစုံ ရေးသားခြင်း။
2. `backendPos/tests/Feature/Admin/AdminDashboardTest.php`:
   - Dashboard စစ်ဆေးမှု Assertion များ အားလုံးနှင့် အပြည့်အဝ ကိုက်ညီအောင် စစ်ဆေးအတည်ပြုခြင်း (၄၈ ခု စစ်ဆေးမှုလုံး အောင်မြင်)။

---

## ၅။ စမ်းသပ်စစ်ဆေးမှု ရလဒ် (Verification Evidence)
- **PHPUnit Automated Test Suite**:
  ```bash
  php artisan test
  # Tests: 48 passed (245 assertions)
  # Result: PASSED (100%)
  ```
- **Laravel Pint Code Styling Check**:
  ```bash
  ./vendor/bin/pint --test
  # Result: PASSED
  ```
