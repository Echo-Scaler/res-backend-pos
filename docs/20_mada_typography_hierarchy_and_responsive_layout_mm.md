# Mada Typography Hierarchy & Responsive Layout Optimization (မြန်မာဘာသာဖြင့် ရှင်းလင်းချက် မှတ်တမ်း)

## ၁။ အနှစ်ချုပ်နှင့် အဓိကရည်ရွယ်ချက် (Executive Summary & Objective)

ဤမှတ်တမ်းသည် Restaurant POS စနစ်တစ်ခုလုံးတွင် **`font-family: "Mada", sans-serif;`** ကို တစ်သမတ်တည်း မဖြစ်မနေ လိုက်နာရမည့် စံနှုန်းအဖြစ် သတ်မှတ်ခြင်း၊ **တူညီသော စာလုံးအရွယ်အစား (Font Sizes) နှင့် အလေးချိန် (Font Weights)** စနစ်တကျ ပြဋ္ဌာန်းခြင်း၊ Desktop မျက်နှာပြင်များတွင် ဖြစ်ပေါ်နေသော **Responsive Layout ညာဘက်အခြမ်း 260px ဖြတ်တောက်နေမှု (Right-side Clipping Overflow)** ကို အပြီးသတ်ဖြေရှင်းခြင်းနှင့် **Roles & Permissions Matrix Table Sticky Header အထပ်ထပ်ဖြစ်နေမှု (Overlap Bug)** ကို ဖြေရှင်းပေးခဲ့သည့် အသေးစိတ်မှတ်တမ်းဖြစ်ပါသည်။

---

## ၂။ အဘယ်ကြောင့် အသုံးပြုရသနည်း (Why Restaurant POS Requires Strict Mada Typography & Layout Fixes)

### ၂.၁။ စားသောက်ဆိုင် POS တွင် တူညီသော စာလုံးဖောင့်နှင့် အလေးချိန် လိုအပ်ခြင်း (Why Unified Typography Scale Matters)
1. **စက္ကန့်ပိုင်းအတွင်း အမှာစာနှင့် ငွေစာရင်းဖတ်ရှုနိုင်မှု (Rapid Visual Hierarchy)**:
   - စားသောက်ဆိုင်ကြမ်းပြင် (Dining Floor) နှင့် ငွေကိုင်ကောင်တာ (Cashier Register) များတွင် စားပွဲထိုးနှင့် ငွေကိုင်များသည် ဖောင့်ပုံစံ အမျိုးစုံမတူညီပါက အချက်အလက်ဖတ်ရှုရာတွင် နှောင့်နှေးကြန့်ကြာမှု ဖြစ်စေပါသည်။
   - `Mada` ဖောင့်သည် Geometric Sans-Serif ဖြစ်ပြီး အက္ခရာများအကြား ခြားနားချက်ရှင်းလင်းကာ ကိန်းဂဏန်းများ (Numbers/Prices) ဖတ်ရှုရာတွင် အထူးသင့်လျော်ပြတ်သားပါသည်။
2. **တူညီသော အလေးချိန် (Weights) နှင့် အရွယ်အစား (Sizes) စံနှုန်း**:
   - မလိုလားအပ်သော `font-weight: 900` သို့မဟုတ် Browser default ဖောင့်များ ရောထွေးနေမှုကို ပယ်ဖျက်ပြီး `400 (Regular)`, `500 (Medium)`, `600 (Semi-bold)`, `700 (Bold)` ဟူသော စံ ၄ မျိုးတည်းဖြင့် UI တစ်ခုလုံးကို တိကျစွာ ထိန်းကျောင်းထားပါသည်။
   - KPI တန်ဖိုးများ၊ ခေါင်းစဉ်ကြီးများ၊ စားပွဲနံပါတ်များနှင့် စျေးနှုန်းများကို အဆင့်ဆင့် မျက်စိအေးချမ်းစွာ အချိုးကျ မြင်တွေ့နိုင်ပါသည်။

### ၂.၂။ Responsive Layout ညာဘက်အခြမ်း 260px Clipping Bug ၏ အကြောင်းရင်း (Why Right Overflow Occurred)
- ယခင် Layout တည်ဆောက်ပုံအရ `.main-wrapper` သည် `display: flex; width: 100%` ဖြစ်ပြီး `.sidebar` သည် `position: fixed; width: 260px` ဖြစ်နေပါသည်။
- အဓိကအကြောင်းရင်းမှာ `.sidebar` သည် fixed ဖြစ်နေသဖြင့် flex flow ထဲမှ ဖယ်ထုတ်ခံရပြီး `.page-wrapper` သည် `flex: 1` ကြောင့် parent ၏ ၁၀၀% အကျယ်အပြည့် ယူလိုက်သည့်အပြင် `margin-left: 260px` ထပ်မံပေါင်းထည့်လိုက်သဖြင့် စုစုပေါင်း width သည် `100% + 260px` ဖြစ်သွားခဲ့ပါသည်။
- ရလဒ်အနေဖြင့် Desktop စခရင်များတွင် ညာဘက်ဆုံးရှိ Date Filter Pill၊ စတုတ္ထမြောက် KPI Card ("Cash Drawer Balance") နှင့် ညာဘက် Revenue Chart များသည် Screen ၏ အပြင်ဘက်သို့ ၂၆၀ ပစ်ဇယ် ကျော်လွန်ကာ ဖြတ်တောက် (Clipped) ခံခဲ့ရခြင်း ဖြစ်ပါသည်။

---

## ၃။ ပြုပြင်ပြောင်းလဲခဲ့သော အဓိကအစိတ်အပိုင်းများ (Key Implementation Changes)

### ၃.၁။ Master Layout (`app.blade.php`) တွင် Strict Mada Typography သတ်မှတ်ခြင်း
```css
/* Standardized Typography Scale (Mada) */
:root {
    --font-family-base: "Mada", -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    
    /* Strict Font Sizes */
    --font-size-kpi: 1.625rem;       /* 26px (KPI values, large metrics) */
    --font-size-h1: 1.5rem;          /* 24px (Page main titles) */
    --font-size-h2: 1.25rem;         /* 20px (Section headings, modal headers) */
    --font-size-card-title: 1.05rem; /* ~17px (Card titles, widget titles) */
    --font-size-body: 0.9375rem;     /* 15px (Default body text, table cells) */
    --font-size-sm: 0.84375rem;      /* 13.5px (Subtitles, button text, inputs) */
    --font-size-xs: 0.75rem;         /* 12px (Badges, tags, hints, timestamps) */

    /* Strict Font Weights */
    --font-weight-regular: 400;      /* Standard paragraph & reading text */
    --font-weight-medium: 500;       /* Interactive text, dropdown items, inputs */
    --font-weight-semibold: 600;     /* Card titles, table headers, buttons */
    --font-weight-bold: 700;         /* KPI metrics, page titles, important badges */
}

*, *::before, *::after {
    box-sizing: border-box;
    font-family: var(--font-family-base) !important;
}

body {
    font-family: var(--font-family-base) !important;
    font-size: var(--font-size-body);
    font-weight: var(--font-weight-regular);
}
```

### ၃.၂။ Responsive Page Width Overflow ပြုပြင်ခြင်း (`app.blade.php`)
```css
/* Page Content Wrapper */
.page-wrapper {
    flex: 1;
    width: calc(100% - var(--sidebar-width));
    max-width: calc(100% - var(--sidebar-width));
    min-width: 0;
    margin-left: var(--sidebar-width);
    margin-top: var(--header-height);
    min-height: calc(100vh - var(--header-height));
    transition: var(--transition);
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
}

body.sidebar-collapsed .page-wrapper {
    width: calc(100% - var(--sidebar-collapsed-width));
    max-width: calc(100% - var(--sidebar-collapsed-width));
    margin-left: var(--sidebar-collapsed-width);
}

@media (max-width: 1024px) {
    .page-wrapper {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
    }
}
```

### ၃.၃။ Roles & Permissions Matrix Sticky Table Overlap ပြုပြင်ခြင်း (`permissions_index.blade.php`)
- Matrix Table အား `.matrix-scroll-container` (`max-height: 680px; overflow-y: auto;`) ထဲသို့ ထည့်သွင်းပြီး Table Header (`th`) အား Container ထိပ်ဆုံး `top: 0` တွင် ခိုင်မာစွာ ကပ်ထားစေပါသည်။
- Group Category Headers (`Point of Sale`, `Dining & Inventory`) များအား `position: sticky; top: 49px; z-index: 20;` ဖြင့် သတ်မှတ်ပေးလိုက်သဖြင့် အောက်သို့ scroll ဆွဲသည့်အခါ ပထမဆုံး permission row (`pos-checkout`) သည် Header အောက်သို့ တိုးဝင်ညှပ်ခေါက်ခြင်း လုံးဝမရှိတော့ဘဲ Group Header အောက်မှ ချောမွေ့စွာ ဖြတ်သန်းသွားမည်ဖြစ်ပါသည်။

### ၃.၄။ Tabler Icons Webfont ပျက်စီးမှု (Missing Glyph Horizontal Bars) ပြုပြင်ခြင်း
- ယခင် `*, *::before, *::after` တွင် `font-family: "Mada", sans-serif !important;` တိုက်ရိုက်သတ်မှတ်လိုက်မိခြင်းကြောင့် Tabler Icons ၏ pseudo-elements (`.ti-*:before`) များပါ `Mada` ဖောင့်သို့ အတင်းပြောင်းလဲသွားခဲ့ပြီး Unicode Codepoint များသည် စာလုံးမရှိသော Missing Glyph (လိုင်းစင်း ဘားများ သို့မဟုတ် တရုတ်စာလုံးများ) အဖြစ် ပျက်စီးပြသခဲ့ပါသည်။
- ၎င်းအား ဖြေရှင်းရန်အတွက် `*::before` မှ font-family အား ဖယ်ရှားကာ အောက်ပါအတိုင်း Tabler Icon Webfont Protection အား `app.blade.php` တွင် တင်းကျပ်စွာ သတ်မှတ်ပေးခဲ့ပါသည်:
```css
/* Strict protection for icon webfonts (Tabler Icons) */
.ti, [class^="ti-"], [class*=" ti-"],
.ti::before, [class^="ti-"]::before, [class*=" ti-"]::before {
    font-family: "tabler-icons" !important;
    font-style: normal;
    font-weight: normal;
    font-variant: normal;
    text-transform: none;
    line-height: 1;
    display: inline-block;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
```
- ထို့အပြင် Roles & Permissions matrix tab တွင် Tabler icon library တွင် မရှိသော `ti-matrix` အား တရားဝင် icon ဖြစ်သည့် `ti-table` သို့ အစားထိုးပြင်ဆင်ပေးခဲ့ပါသည်။

---

## ၄။ ပရောဂျက်စည်းမျဉ်း လိုက်နာမှု အတည်ပြုချက် (Rule Compliance Verification)

1. **Rule 1 (Burmese Documentation)**:
   - ဤ `20_mada_typography_hierarchy_and_responsive_layout_mm.md` ဖြင့် အသေးစိတ်မြန်မာဘာသာ မှတ်တမ်းတင်ထားပါသည်။
2. **Rule 2 (Git Branch & Commit Reminder)**:
   - တုံ့ပြန်မှုအဆုံးတွင် active branch နှင့် conventional commit message သတိပေးချက် ထည့်သွင်းထားပါသည်။
3. **Rule 3 (Automated Verification)**:
   - `php artisan test`: 74 tests (370 assertions) 100% Passed.
   - `./vendor/bin/pint --test`: 83 files passed with zero styling violations.
4. **Rule 5 (Global Typography & Modern Theme)**:
   - `font-family: "Mada", sans-serif;` ကို HTML body နှင့် heading/input element များတွင် တိကျသော scale (400, 500, 600, 700) ဖြင့် စနစ်တကျ ပြဋ္ဌာန်းပြီး Icon Webfonts များကိုလည်း မထိခိုက်အောင် ကာကွယ်ထားပြီးဖြစ်ပါသည်။
