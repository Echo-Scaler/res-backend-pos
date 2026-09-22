# ခေတ်မီ Dark Theme အရောင်အသွေး ပြောင်းလဲခြင်းနှင့် Mada စာလုံးဖောင့် စည်းမျဉ်း သတ်မှတ်ခြင်း (Modern Dark Theme & Mada Typography Rule)

## ၁။ နိဒါန်းနှင့် ပြဿနာသုံးသပ်ချက် (Problem Statement)

ယခင် Admin Dashboard တွင် အောက်ပါ အားနည်းချက် (၂) ချက် ရှိနေခဲ့ပါသည်-
1. **မလိုလားအပ်သော Dark Mode နောက်ခံအရောင် (Murky Greenish Dark Theme)**:
   - Dark Mode ဖွင့်ထားချိန်တွင် Body, Header, Sidebar နှင့် Card နောက်ခံများသည် အလွန်ရင့်ပြီး ညစ်နွမ်းသော အစိမ်းရောင်ရင့်ရင့် (`#0b140e`, `#101c14`, `#152219`) များ ဖြစ်နေခဲ့သဖြင့် မျက်စိညောင်းစေပြီး ခေတ်မီဆန်းသစ်သော Dark UI ခံစားချက်ကို မရရှိစေခဲ့ပါ။
2. **စာလုံးဖောင့် စည်းမျဉ်း အသစ် သတ်မှတ်ရန် လိုအပ်ခြင်း (Typography Rule)**:
   - ပရောဂျက်တစ်ခုလုံးတွင် စားသောက်ဆိုင် POS လုပ်ငန်းသုံးအတွက် ရှင်းလင်းပြတ်သားပြီး မျက်စိအေးချမ်းစေသော **`font-family: "Mada", sans-serif;`** ကို အဓိက Standard Rule အဖြစ် ပြောင်းလဲသတ်မှတ်ရန် လိုအပ်ခဲ့ပါသည်။

---

## ၂။ အကောင်အထည်ဖော် ဆောင်ရွက်ခဲ့သော အချက်များ (Implementations)

### (၁) ခေတ်မီဆန်းသစ်သော Obsidian / Dark Slate Theme Palette
Linear, Vercel, Supabase ကဲ့သို့သော ကမ္ဘာ့အဆင့်မီ Dark Mode များ၏ စံနှုန်းအတိုင်း အောက်ပါအတိုင်း အဆင့်မြှင့်တင်ခဲ့ပါသည်-
- **Body Background (`--bg-body`)**: `#0b0f17` (နက်မှောင်သန့်စင်သော Obsidian Black)
- **Header & Sidebar (`--bg-header`, `--bg-sidebar`)**: `#111724` (သပ်ရပ်သော Dark Charcoal Slate)
- **Card Surfaces (`--bg-card`)**: `#161e2e` (ကြည်လင်ပြတ်သားသော Dark Slate Surface)
- **Border Dividers (`--border-color`, `--border-subtle`)**: `#222d42` / `#192233`
- **Text Hierarchy**:
  - Main Text (`--text-main`): `#f1f5f9` (ဖြူစင်တောက်ပသော စာသား)
  - Muted Text (`--text-muted`): `#94a3b8` (Slate Grey ညွှန်းဆိုချက်များ)
  - Light Text (`--text-light`): `#64748b`
- **Brand Accent Lime (`--primary: #9ec63b`)**: အစိမ်းရောင်ကို နောက်ခံတစ်ခုလုံးတွင် မသုံးတော့ဘဲ အရေးကြီးသော ခလုတ်များ၊ တံဆိပ်များ (Badges)၊ Active Tab များနှင့် ဂရပ်မျဉ်းများတွင်သာ ထင်ရှားပေါ်လွင်စေရန် တန်ဆာဆင်ထားပါသည်။

### (၂) Mada Typography စည်းမျဉ်းနှင့် သင့်လျော်သော Font Size သတ်မှတ်ချက်များ
1. **Google Font Mada ချိတ်ဆက်ခြင်း**:
   - `<link href="https://fonts.googleapis.com/css2?family=Mada:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">`
2. **Global CSS Rule သတ်မှတ်ခြင်း**:
   - `* { font-family: "Mada", -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }`
3. **Font Size နှင့် အချိုးအစား ချိန်ညှိမှုများ**:
   - Body Base Size: `15px` / `line-height: 1.5`
   - Dashboard Headings: `1.5rem` (`font-weight: 700`)
   - KPI Values: `1.75rem` (`font-weight: 700`)
   - Subtitles & Labels: `0.85rem - 0.9rem` (`font-weight: 500/600`)
4. **ApexCharts ဂရပ်များတွင် Mada Font သုံးစွဲခြင်း**:
   - Revenue Trend, Hourly Dining Traffic, Weekly Sales, Payment Donut Chart အားလုံးတွင် `fontFamily: 'Mada, sans-serif'` ဖြင့် ပုံဖော်ပေးထားပါသည်။

### (၃) ပရောဂျက် စည်းမျဉ်းသစ်အဖြစ် မှတ်တမ်းတင်ခြင်း (`AGENTS.md`)
- `AGENTS.md` ထဲတွင် Section 5: **Global Typography & Modern Theme Rule** အဖြစ် ထည့်သွင်းမှတ်တမ်းတင်ခဲ့ပြီး နောက်ဆက်တွဲ Feature များ အားလုံးတွင် Mada font နှင့် Modern Dark Palette ကိုသာ မဖြစ်မနေ အသုံးပြုရန် ပြဋ္ဌာန်းခဲ့ပါသည်။

---

## ၃။ အဘယ်ကြောင့် ဤသို့ ပြောင်းလဲရသနည်း (Why?)

1. **စနစ်အသုံးပြုသူ (Cashier, Manager, Owner) မျက်စိသက်သာစေရန် (Reduced Visual Fatigue)**:
   - စားသောက်ဆိုင် POS Dashboard များကို နေ့စဉ် နာရီပေါင်းများစွာ စောင့်ကြည့်အသုံးပြုရသဖြင့် အစိမ်းရောင်ရင့်ရင့် နောက်ခံများထက် အလင်းပြန်မှုနည်းပြီး ခြားနားမှု (Contrast) ကောင်းမွန်သော Obsidian Slate က မျက်စိညောင်းညာမှုကို အထူးလျော့ချပေးပါသည်။
2. **Mada Font ၏ အားသာချက်**:
   - Mada သည် ကိန်းဂဏန်းများ (Numbers)၊ အင်္ဂလိပ်စာလုံးများနှင့် အချက်အလက်ဇယားများကို ပြသရာတွင် အလွန်ရှင်းလင်းပြီး တိကျစွာ ဖတ်ရှုနိုင်သော Modern Geometric Proportions ရှိပါသည်။

---

## ၄။ စစ်ဆေးအတည်ပြုခြင်း ရလဒ်များ (Verification)

1. **Automated Test Suite**:
   ```bash
   php artisan test
   ```
   - အောင်မြင်မှု: **48 passed (249 assertions)**
2. **Code Style Check**:
   ```bash
   ./vendor/bin/pint --test
   ```
   - အောင်မြင်မှု: **0 issues found**
