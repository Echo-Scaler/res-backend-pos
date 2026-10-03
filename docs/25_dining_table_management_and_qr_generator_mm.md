# Dining Table Management & Dynamic QR Code Generator စနစ် ဗိသုကာနှင့် တည်ဆောက်မှု မှတ်တမ်း

## ၁။ နိဒါန်းနှင့် ရည်ရွယ်ချက် (Introduction & Objectives)

ဂျပန်နိုင်ငံ၏ **Saizeriya (サイゼリヤ)** နှင့် ခေတ်မီစံပြ စားသောက်ဆိုင်ကြီးများတွင် ဧည့်သည်များသည် မည်သည့် App မှ Install လုပ်စရာမလိုဘဲ စားပွဲခုံပေါ်ရှိ QR Code ကို မိမိဖုန်းဖြင့် Scan ဖတ်ကာ တိုက်ရိုက်အော်ဒါတင်နိုင်သော **Table QR Self-Ordering System** ကို အသုံးပြုကြပါသည်။

ဤ Module သည် စားသောက်ဆိုင်၏ စားပွဲခုံများကို စနစ်တကျ စီမံခန့်ခွဲနိုင်ရန်၊ စားပွဲတစ်ခုချင်းစီအတွက် လုံခြုံစိတ်ချရသော **Dynamic QR Code** များ အလိုအလျောက် ထုတ်ပေးနိုင်ရန်၊ စားပွဲတင် Acrylic Stand (Table Tent) များကို အရည်အသွေးမြင့် ပရင့်ထုတ်နိုင်ရန်နှင့် စားပွဲအခြေအနေများကို Real-time စောင့်ကြည့်နိုင်ရန်အတွက် တည်ဆောက်ထားသော **Production-Ready Dining Table Management System** ဖြစ်ပါသည်။

---

## ၂။ အဘယ်ကြောင့် ဤသို့ ဒီဇိုင်းရေးဆွဲရသနည်း? (Architecture Decisions & "Why")

### (၁) အဘယ်ကြောင့် Table ID အစား လျှို့ဝှက် `qr_token` ကို သုံးရသနည်း?
- အကယ်၍ URL ကို `http://restaurant.com/order/table/1` ဟု ရိုးရိုး ID သုံးပါက မသမာသူ သို့မဟုတ် အခြားစားပွဲမှ ဧည့်သည်များသည် URL ရှိ နံပါတ်ကို `2`, `3`, `4` ဟု ပြောင်းလဲကာ အခြားစားပွဲများအတွက် မလိုလားအပ်သော အော်ဒါများ ရိုက်နှိပ်နှောင့်ယှက်နိုင်ပါသည်။
- ထို့ကြောင့် စားပွဲတစ်ခုချင်းစီအတွက် ခန့်မှန်းရခက်ခဲသော 32-character Cryptographic Random Token (`qr_token`) ကို အသုံးပြုထားပြီး၊ လိုအပ်ပါက Admin မှ QR Token ကို တစ်ချက်နှိပ်ရုံဖြင့် လုံခြုံရေးအရ အသစ်ပြန်လည်လဲလှယ်နိုင်သော **Regenerate QR Token** စနစ်ကို ထည့်သွင်းထားပါသည်။

### (၂) Dual Printing Support (Single Stand A6 Print vs Batch Print A4 Sheet)
- **Single Print Stand (A6/80mm Table Tent)**: စားပွဲတစ်လုံးတည်းအတွက် စလစ်ကလစ်ခွက် သို့မဟုတ် Acrylic Stand အသစ်လဲလှယ်လိုပါက သီးသန့် ပရင့်ထုတ်နိုင်ရန်။
- **Batch Print All Stands (A4 Multi-Grid Sheet)**: ဆိုင်ဖွင့်စတွင် စားပွဲအားလုံးအတွက် Stand များကို တစ်ရွက်တည်းတွင် ၄ ခုတွဲ အလွယ်တကူ တစ်ချက်တည်း ပရင့်ထုတ်ပြီး ကတ်ကြေးဖြင့် ညှပ်ကာ Acrylic ခွက်ထဲ ထည့်သွင်းနိုင်ရန်။

### (၃) အဆင့် (၆) ဆင့်ပါသော Real-time Floor Statuses
- 🟢 `VACANT`: စားပွဲ အားလပ်နေပြီး ဧည့်သည် အသစ်လက်ခံရန် အသင့်ဖြစ်နေသော အခြေအနေ။
- 🟡 `ORDERING`: ဧည့်သည် ထိုင်နေပြီး ဖုန်းဖြင့် Menu ကြည့်ရှုကာ အော်ဒါရွေးချယ်နေသော အခြေအနေ။
- 🔵 `OCCUPIED`: အော်ဒါကျပြီး အစားအသောက်များ စားပွဲသို့ ရောက်ရှိသုံးဆောင်နေသော အခြေအနေ။
- 🟠 `BILLING`: ဧည့်သည် စားသောက်ပြီး၍ စားပွဲတင်စလစ် (伝票) ကိုယူကာ ကောင်တာသို့ ငွေရှင်းရန် သွားနေသော အခြေအနေ။
- 🟣 `RESERVED`: ညနေပိုင်း သို့မဟုတ် ကြိုတင်ဘွတ်ကင် ပြုလုပ်ထားသော အခြေအနေ။
- ⚪ `OUT_OF_SERVICE`: ပြုပြင်ထိန်းသိမ်းဆဲ သို့မဟုတ် ခေတ္တပိတ်ထားသော စားပွဲ။

---

## ၃။ ဒေတာဘေ့စ် ဗိသုကာ (Database Schema)

`dining_tables` ဇယားကို အောက်ပါအတိုင်း တည်ဆောက်ထားပါသည် -

```sql
CREATE TABLE dining_tables (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    restaurant_id BIGINT UNSIGNED NOT NULL,
    table_number VARCHAR(50) NOT NULL,
    name VARCHAR(100) NULL,
    seating_capacity SMALLINT UNSIGNED DEFAULT 4,
    floor_area VARCHAR(100) DEFAULT 'Main Dining Hall',
    status VARCHAR(50) DEFAULT 'VACANT',
    qr_token VARCHAR(64) UNIQUE NOT NULL,
    current_order_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE,
    FOREIGN KEY (current_order_id) REFERENCES orders(id) ON DELETE SET NULL,
    UNIQUE (restaurant_id, table_number),
    INDEX (restaurant_id, status),
    INDEX (restaurant_id, floor_area)
);
```

---

## ၄။ တင်းကျပ်သော စည်းမျဉ်းများနှင့်အညီ တည်ဆောက်ထားသော ဖိုင်များ (Strict Rules Compliance)

### (၁) Form Requests စည်းမျဉ်း (Inline Validation မသုံးစွဲဘဲ သီးခြားခွဲထုတ်ခြင်း)
- `App\Http\Requests\Admin\Table\StoreDiningTableRequest`: စားပွဲအသစ်ဖန်တီးခြင်း၊ ဆိုင်တစ်ခုတည်းအတွင်း စားပွဲနံပါတ် ထပ်နေမှု မရှိစေရန် စစ်ဆေးခြင်း။
- `App\Http\Requests\Admin\Table\UpdateDiningTableRequest`: စားပွဲအချက်အလက် ပြင်ဆင်ခြင်း စစ်ဆေးမှု။
- `App\Http\Requests\Admin\Table\UpdateDiningTableStatusRequest`: စားပွဲ Status အမြန်ပြောင်းလဲခြင်း (VACANT, OCCUPIED, BILLING စသည်) စစ်ဆေးမှု။

### (၂) API & JSON Resources စည်းမျဉ်း (Raw Model ထုတ်မပြဘဲ Transform ပြုလုပ်ခြင်း)
- `App\Http\Resources\DiningTableResource`: စားပွဲနံပါတ်၊ နေရာ၊ အခြေအနေ၊ အော်ဒါ URL နှင့် လိုအပ်သော ဒေတာများကို စနစ်တကျ Format ချ၍ ပြန်လည်ပေးပို့ခြင်း။

### (၃) Typography & Modern Dark Theme စည်းမျဉ်း
- ဖောင့်စနစ်: **`font-family: "Mada", sans-serif;`** ကို တင်းကျပ်စွာ အသုံးပြုထားသည်။
- အရောင်စနစ်: Modern Slate/Obsidian Background (`#0b0f17`, `#111724`, `#161e2e`, `#222d42`) နှင့် Brand Accent (`#9ec63b` Lime Green) ကို အသုံးပြုထားသည်။

### (၄) Currency MMK စည်းမျဉ်း
- Customer Mobile Order UI နှင့် ဈေးနှုန်းပြသမှုအားလုံးတွင် **`MMK` (Burmese Kyats)** ဖြင့်သာ ပြသထားပါသည်။

### (၅) Floor Plan Card ပေါ်တွင် Active Order စာရင်း တိုက်ရိုက်စစ်ဆေးခြင်းနှင့် Zero Refresh စနစ်
- **Clickable OCCUPIED Badge Toggle (Occupied နှိပ်မှသာ ဟင်းပွဲစာရင်း ဖွင့်ပြခြင်း)**: စားပွဲ Card များ မျက်နှာပြင်တွင် ရှုပ်ထွေးရှည်လျားမနေစေရန် မူလအနေအထားတွင် Order Items Box ကို ဖျောက်ထားပြီး၊ စားပွဲ Card ထောင့်ရှိ **`OCCUPIED`** (သို့မဟုတ် `ORDERING`, `BILLING`) Badge ကို နှိပ်မှသာ မှာယူထားသော ဟင်းပွဲစာရင်းနှင့် ဘေလ်အသေးစိတ်ကို အောက်သို့ ချောမွေ့စွာ ဖြန့်ချပြီး ပြသပေးပါသည်။ ပြန်လည်နှိပ်ပါက ပြန်လည်ကျုံ့သွားမည် ဖြစ်သည်။
- **Active Order Items List**: စားပွဲတစ်ခုချင်းစီ၏ Card ပေါ်တွင် လက်ရှိမှာယူထားသော ဟင်းပွဲအမည်များ၊ အရေအတွက် (Qty)၊ စားသုံးသူ၏ သီးသန့်မှာကြားချက် (Special Notes) နှင့် စုစုပေါင်းကျသင့်ငွေ (MMK) များကို ဝန်ထမ်းများ ချက်ချင်းစစ်ဆေးနိုင်ရန် ထည့်သွင်းထားသည်။
- **Direct Slip Reprint**: စားပွဲ Card ပေါ်မှ တိုက်ရိုက် `Reprint` (မိတ္တူထုတ်ခြင်း)၊ `Chit` (မီးဖိုချောင်စလစ်) နှင့် `Bill` (ဧည့်သည်ငွေတောင်းခံလွှာ) များကို Window အသစ်ဖြင့် ဖွင့်လှစ်ထုတ်ယူနိုင်ပါသည်။
- **Zero Page Refresh (စာမျက်နှာ Reload မဖြစ်စေခြင်း)**: စားပွဲ အခြေအနေ (Status Dropdown) ပြောင်းလဲခြင်းနှင့် စလစ်မိတ္တူ ထုတ်ယူမှု အားလုံးကို JavaScript `fetch` (AJAX) ဖြင့် ပြုလုပ်ထားသဖြင့် Browser Page Refresh ဖြစ်စရာမလိုဘဲ အချိန်နှင့်တပြေးညီ Badge များနှင့် KPI Metric များ ချက်ချင်း ပြောင်းလဲသွားပါသည်။

### (၆) Modern UI Design & Color Aesthetics စနစ် (ခေတ်မီ အဆင့်မြင့် ဒီဇိုင်းနှင့် အရောင်စနစ် အဆင့်မြှင့်တင်မှု)
- **Top Status Hairline Glow**: စားပွဲ Card တစ်ခုချင်းစီ၏ ထိပ်ဆုံးအနားသတ်တွင် စားပွဲ၏ Status အလိုက် ကွဲပြားသော အရောင်ပြေး (Gradient Glow) အလင်းတန်းကို တပ်ဆင်ထားသည် (ဥပမာ- Vacant အတွက် Emerald Green, Occupied အတွက် Electric Sapphire Blue, Ordering အတွက် Radiant Amber, Billing အတွက် Sunset Orange, Reserved အတွက် Regal Amethyst)။
- **Pulsing Status Dots & Rotating Chevron**: Status Badge တစ်ခုချင်းစီတွင် Real-time လှုပ်ရှားနေသော အလင်းစက်ဝိုင်း (`@keyframes statusPulse`) ကို ထည့်သွင်းထားပြီး၊ အော်ဒါရှိသော စားပွဲတွင် Expand/Collapse ကို ညွှန်ပြသော မြှားခေါင်း (`.badge-chevron`) သည် နှိပ်လိုက်ပါက ၁၈၀ ဒီဂရီ ချောမွေ့စွာ လှည့်ပတ်ပြောင်းလဲသွားပါသည်။
- **Obsidian Glassmorphism Order Panel**: ဟင်းပွဲစာရင်း box ကို အဆင့်မြင့် မှန်သားအနက်ရောင် (`linear-gradient(180deg, #111827 0%, #0c101a 100%)`) နှင့် နီယွန်သံပုရာစိမ်း (`#a3e635`) စာလုံး၊ အပူပေး Amber Cooking NotesBadge များဖြင့် အထူးတလည် ဖွဲ့စည်းဖန်တီးထားပါသည်။
- **Modern Interactive Elements**: Zone filter pills, metric KPI cards, status select dropdowns များနှင့် QR/Stand Action Button များကို soft radius (10px–20px)၊ hover lift effect နှင့် vibrant brand shadow များဖြင့် အဆင့်မြှင့်တင်ထားပါသည်။

### (၇) Independent Card Height (ကပ်လျက် Card များ မဆန့်ထွက်စေခြင်း ဗိသုကာ)
- **အဘယ်ကြောင့် Card တစ်ခုဖွင့်ပါက အခြား Card များ အရပ်ရှည်မလာစေရသနည်း?**: မူလ CSS Grid တွင် default `align-items: stretch` ဖြစ်နေသဖြင့် Card တစ်ခုတွင် `OCCUPIED` ကို နှိပ်ပြီး ဟင်းပွဲစာရင်း အကွက် ဖြန့်ချလိုက်သောအခါ တစ်တန်းတည်းရှိ ကပ်လျက် စားပွဲ Card များပါ အောက်သို့ လိုက်လံဆန့်ထွက်ပြီး အလယ်တွင် ကွက်လပ်ကြီးများ ဖြစ်ပေါ်စေခဲ့သည်။
- **ဖြေရှင်းထားသော စံနှုန်း**: `.tables-grid` တွင် `align-items: start;` နှင့် `.table-box` တွင် `align-self: start;` ကို သတ်မှတ်လိုက်သောကြောင့် စားပွဲ Card တစ်ခုချင်းစီသည် မိမိ၏ မူရင်း Content အမြင့်အတိုင်းသာ သီးခြား ရပ်တည်ပြီး၊ အခြား Card များ၏ အနေအထားနှင့် အမြင့်ကို မည်သို့မျှ နှောင့်ယှက်ခြင်း မရှိတော့ပါ။

### (၈) Restaurant-Side Table Change / Transfer System (စားပွဲ ပြောင်းလဲပေးခြင်း စနစ်နှင့် လုံခြုံရေး)
- **အဘယ်ကြောင့် Customer ဖုန်း QR မှ စားပွဲပြောင်းလဲခွင့် မပြုသနည်း?**: အကယ်၍ စားသုံးသူသည် မိမိဖုန်းမှ စားပွဲနံပါတ်ကို စိတ်ကြိုက် ပြောင်းလဲခွင့်ရပါက အခြားစားပွဲ၏ ဘေလ်နှင့် အော်ဒါများ ရောထွေးရှုပ်ထွေးသွားခြင်း၊ ဆိုင်အတွင်း မသမာမှုများ ဖြစ်ပေါ်နိုင်ခြင်းကြောင့် ဖြစ်ပါသည်။ ထို့ကြောင့် စားပွဲပြောင်းလဲခြင်း (Table Transfer) ကို ဆိုင်ဝန်ထမ်း/မန်နေဂျာ (Restaurant Side) ကသာ စိစစ်ခွင့်ပြုနိုင်သော စနစ်ဖြင့် ကန့်သတ်တည်ဆောက်ထားပါသည်။
- **Table Transfer ၏ လုပ်ဆောင်ချက် အဆင့်ဆင့်**:
  1. စားပွဲ Card ပေါ်ရှိ `Change Table` ခလုတ်ကို နှိပ်ပါက Modern Transfer Modal ပွင့်လာမည်။
  2. လက်ရှိ ဆိုင်အတွင်း အမှန်တကယ် လွတ်လပ်နေသော `VACANT` စားပွဲများကိုသာ ရွေးချယ်ခွင့် ပြုထားသည်။
  3. စားပွဲပြောင်းလဲသည့် အကြောင်းပြချက် (Reason: ရှုခင်းသာသောနေရာ၊ လူဦးရေတိုးလာ၍ စားပွဲကျယ်ပြောင်းခြင်း စသည်) ကို စနစ်တကျ မှတ်တမ်းတင်နိုင်သည်။
  4. စားပွဲပြောင်းလိုက်သည်နှင့် ယခင်စားပွဲရှိ အော်ဒါစာရင်း၊ ချက်ပြုတ်ဆဲ အခြေအနေနှင့် ဘေလ်ကျသင့်ငွေ အားလုံးသည် စားပွဲအသစ်သို့ အလိုအလျောက် ရွှေ့ပြောင်းသွားပြီး၊ ယခင် စားပွဲဟောင်းသည် ချက်ချင်း **`VACANT`** အဖြစ် အလိုအလျောက် ပြန်လည်လွတ်လပ်သွားပါသည်။

---

## ၅။ စနစ်၏ စမ်းသပ်မှု မှတ်တမ်း (Automated Tests & Quality Verification)

`tests/Feature/Admin/DiningTableManagementTest.php` တွင် Unit/Feature Test ပေါင်း (၁၈) ခု ရေးသားစစ်ဆေးခဲ့ပြီး Test အားလုံး ၁၀၀% အောင်မြင်ပါသည် -

```bash
PASS  Tests\Feature\Admin\DiningTableManagementTest
✓ owner and manager can view dining tables index and kpis
✓ cashier and staff cannot manage dining tables
✓ can create dining table with unique number and auto generated qr token
✓ duplicate table number in same restaurant is rejected
✓ table number can be same across different restaurants
✓ can update table details
✓ can update table status via patch
✓ can regenerate table qr token
✓ can view print stand layout
✓ can view batch print stands layout
✓ customer can open table qr order page without auth
✓ cannot delete occupied table until cleared
✓ dining table resource transforms cleanly
✓ table card displays active order items and reprint actions
✓ async table status update returns json and floor metrics for zero refresh
✓ owner or manager can transfer occupied table and order to vacant table
✓ cannot transfer to occupied table or same table
✓ cashier and staff cannot transfer tables


---

## ၆။ Active Order Card ဒီဇိုင်းသစ်နှင့် Status Dropdown ဖျောက်ထားမှု (UI Refinement & Auto-Hide Status)

### ၆.၁ ပြုပြင်ပြောင်းလဲခဲ့သော အချက်များ
1. **ခေတ်မီဆန်းသစ်သော Clean Color Palette သို့ ပြောင်းလဲခြင်း**:
   - ယခင်က မည်းနက်လွန်းသော Midnight Gradient အစား Light Theme စားပွဲကတ်များနှင့် လိုက်ဖက်လှပသည့် Soft Slate Palette (`#f8fafc`, White Dish Rows `#ffffff`, Slate Borders `#e2e8f0`, High Contrast Charcoal Text `#0f172a`) သို့ ပြောင်းလဲပေးထားပါသည်။
   - Dark Theme ပြောင်းလဲအသုံးပြုချိန်တွင်လည်း Modern Dark Slate (`#111724` / `#161e2e`) အဖြစ် အလိုအလျောက် သဟဇာတဖြစ်စွာ ပြသနိုင်အောင် ဖန်တီးထားပါသည်။
2. **Occupied (Seated) Dropdown အား Auto-Hide ပြုလုပ်ပေးခြင်း**:
   - ဝန်ထမ်းသည် စားပွဲခုံ၏ `OCCUPIED` Badge ကို နှိပ်၍ အော်ဒါအသေးစိတ်ကို ကြည့်ရှုနေချိန်တွင် အောက်ဘက်ရှိ `[ 🔵 Occupied (Seated) ▾ ]` Status Switcher Wrap အား အလိုအလျောက် ဖျောက်ထားပေးပြီး၊ Order Box အား ပြန်လည်ပိတ်သိမ်း (Collapse) လိုက်ချိန်တွင် မူလအတိုင်း ပြန်လည်ပြသပေးပါသည်။
3. **READY FOR DELIVERY Badge အား ဖယ်ရှားခြင်း**:
   - အော်ဒါစာရင်း Box ၏ ခေါင်းစီးတွင် မလိုအပ်သော Kitchen Status ("READY FOR DELIVERY") Badge အား ရှင်းလင်းဖယ်ရှားပြီး Order Reference Number နှင့် Order Time Ago သာ သန့်ရှင်းစွာ ပြသစေပါသည်။

### ၆.၂ အဘယ်ကြောင့် အသုံးပြုရသနည်း (Why)
- **Eye Strain & Contrast**: စားသောက်ဆိုင် POS Tablet သို့မဟုတ် ကွန်ပျူတာဖန်သားပြင်တွင် အဖြူရောင် Card များကြား မည်းနက်လွန်းသော Order Box ကြောင့် မျက်စိညောင်းညာခြင်းမှ ကင်းဝေးစေပြီး သန့်ရှင်းသပ်ရပ်သော POS Appearance ရရှိစေပါသည်။
- **Accidental Click Prevention**: အော်ဒါစစ်ဆေးနေစဉ် သို့မဟုတ် Slip ပြန်ထုတ်နေစဉ်အတွင်း ဝန်ထမ်းများ စားပွဲခုံ၏ Status Select Box ကို မတော်တဆ ထိမိပြောင်းလဲမိခြင်းမှ ကာကွယ်ပေးပြီး စားပွဲ Card ပေါ်တွင် နေရာလွတ် (Card Real Estate) ပိုမိုကျယ်ဝန်းစေပါသည်။
- **Focused Simplicity**: ဝန်ထမ်းများအနေဖြင့် ဟင်းပွဲအမည်၊ အထူးမှာကြားချက် (Special Notes) နှင့် ကျသင့်ငွေ (MMK) တို့ကိုသာ အဓိကထား စစ်ဆေးနိုင်စေရန် UI Noise များကို လျှော့ချပေးခြင်းဖြစ်ပါသည်။

---

## ၇။ စားသောက်ဆိုင်အတွင်း စားပွဲခုံ ၁ မှ ၄၇ အထိ ပုံသေ သတ်မှတ်ခြင်းနှင့် Direct Changed Table Input စနစ် (Constant Tables 1-47 & Changed Table Input)

### ၇.၁ ပြုပြင်ပြောင်းလဲခဲ့သော အချက်များ (Key Features)
1. **ဆိုင်တွင်း စားပွဲခုံ အရေအတွက် ၁ မှ ၄၇ အထိ ပုံသေ သတ်မှတ်ပေးခြင်း (Constant Tables 1 to 47)**:
   - `DiningTableSeeder` တွင် စားသောက်ဆိုင်အတွက် စားပွဲခုံပေါင်း (၄၇) ခုကို `1, 2, 3, ... 47` အဖြစ် စနစ်တကျ ထည့်သွင်းပေးထားပါသည်။
   - Main Dining Hall (စားပွဲ ၁ မှ ၂၈)၊ Outdoor Terrace (စားပွဲ ၂၉ မှ ၃၆)၊ VIP Dining Room (စားပွဲ ၃၇ မှ ၄၂) နှင့် Bar Counter Area (စားပွဲ ၄၃ မှ ၄၇) ဟူ၍ နေရာအလိုက် သတ်မှတ်ပေးထားပါသည်။
   - သဘာဝကျသော ဂဏန်းအစဉ်လိုက် (Natural Numerical Sorting: `(table_number + 0) ASC`) ဖြင့် စီစဉ်ပြသပေးထားသဖြင့် စားပွဲ ၁ မှ ၄၇ အထိ မှန်ကန်စွာ ပေါ်ထွက်လာပါသည်။
   - စတင်ချိန်တွင် စားပွဲနံပါတ် ၂ (Table 2) တွင် အော်ဒါအသင့်ရှိနေပြီး (၁၉,၉၅၀ ကျပ်)၊ စားပွဲနံပါတ် ၅ (Table 5) အား လွတ်လပ်သော VACANT စားပွဲအဖြစ် ထားရှိပေးထားသဖြင့် Table 2 မှ Table 5 သို့ ချက်ချင်း စမ်းသပ် ပြောင်းလဲနိုင်ပါသည်။
2. **Changed Table No. Input ထည့်သွင်းပေးခြင်း (Direct Table Number Input)**:
   - စားပွဲပြောင်းလဲသည့် Modal ပေါ်တွင် `Current Seating (Table 2) ➔ Change To (Table 5)` အဖြစ် Step Flow Banner ဖြင့် ရှင်းလင်းစွာ ပြသထားပါသည်။
   - ဝန်ထမ်းများအနေဖြင့် ပြောင်းလဲလိုသော စားပွဲနံပါတ် အကွက် (`Changed Table No. (1 - 47)`) ထဲသို့ `5` ဟု ရိုက်ထည့်နိုင်သလို၊ အောက်ဘက်ရှိ Dropdown သို့မဟုတ် Quick Pick Chip ခလုတ်များမှလည်း နှိပ်၍ ရွေးချယ်နိုင်ပါသည်။
3. **တိုက်ရိုက် အပြန်အလှန် စစ်ဆေးပေးခြင်း (Real-Time Availability Validation)**:
   - ဥပမာ- နံပါတ် `5` ဟု ရိုက်ထည့်လိုက်ပါက Table 5 သည် အမှန်တကယ် လွတ်နေခြင်း ရှိမရှိ စစ်ဆေးပြီး `✓ Table 5 (Window Booth 5 • 4 Seats) is Available (VACANT)` ဟု အစိမ်းရောင်ဖြင့် ပြသပေးပါသည်။
   - အကယ်၍ လက်ရှိစားပွဲ (Table 2) အား ရိုက်ထည့်ပါက သို့မဟုတ် အခြားဧည့်သည် ထိုင်နေသော စားပွဲအား ရိုက်ထည့်ပါက သတိပေးချက် ပြသပြီး မှားယွင်းပြောင်းရွှေ့မိခြင်းကို ကာကွယ်ပေးပါသည်။
4. **Backend Form Request အဆင့်မြှင့်တင်ခြင်း (`TransferDiningTableRequest`)**:
   - `target_table_number` (ဥပမာ- "5") ပေးပို့လာပါက `prepareForValidation()` တွင် စားသောက်ဆိုင်၏ အဆိုပါ စားပွဲအား အလိုအလျောက် ရှာဖွေပြီး `target_table_id` အဖြစ် ချိတ်ဆက်ပေးကာ စစ်ဆေးမှုများ (Vacant Check, Same Table Check, Active Check) အား တိကျစွာ ဆောင်ရွက်ပေးပါသည်။

### ၇.၂ အဘယ်ကြောင့် အသုံးပြုရသနည်း (Why)
- **High-Speed Operations (မြန်ဆန်သော ဆိုင်တွင်း လုပ်ငန်းစဉ်)**: စားသောက်ဆိုင်အတွင်း စားပွဲနံပါတ် ၁ မှ ၄၇ အထိ ပုံသေနံပါတ်ပြားများ တပ်ဆင်ထားလေ့ရှိရာ Dropdown ထဲတွင် လိုက်လံရှာဖွေနေစရာမလိုဘဲ ကီးဘုတ်မှ "5" ဟု ချက်ချင်း ရိုက်ထည့်လိုက်ရုံဖြင့် Table Move ပြုလုပ်နိုင်သဖြင့် စက္ကန့်ပိုင်းအတွင်း အော်ဒါနှင့် စားပွဲနေရာ ကူးပြောင်းပေးနိုင်ပါသည်။
- **Zero Human Error (မှားယွင်းမှု လုံးဝမရှိစေခြင်း)**: လွတ်နေသော စားပွဲနံပါတ် ဟုတ်မဟုတ် စနစ်က ချက်ချင်း အရောင်နှင့် စာသားဖြင့် တိုက်ရိုက် စစ်ဆေးပြသပေးသဖြင့် လူထိုင်နေသော စားပွဲပေါ်သို့ အော်ဒါမှားယွင်း ကူးပြောင်းမိခြင်း မဖြစ်ပေါ်စေပါ။

---

## ၈။ Modal Layout ညှပ်သွားမှု ပြင်ဆင်ခြင်းနှင့် Page Filter သီးခြားခွဲထုတ်မှု (Modal Layout Clipping Fix & Data Filter Isolation)

### ၈.၁ ပြဿနာ ဖြစ်ပွားရသည့် အကြောင်းရင်း (Root Causes Diagnosed)
1. **Modal Body အမြင့် ကျော်လွန်၍ အပေါ်/အောက် ညှပ်သွားခြင်း (Viewport Overflow Clipping)**:
   - Laptop မျက်နှာပြင်များပေါ်တွင် Modal Dialog အား သတ်မှတ်ထားသော `max-height` နှင့် Flex Column Layout မပါရှိခဲ့သဖြင့် အပေါ်ဘက် ခေါင်းစီးစာသား (`Change / Transfer Dining Table`) နှင့် အောက်ဘက် အတည်ပြုခလုတ်များ (`Cancel`, `Confirm Table Change`) တို့သည် Browser Viewport ၏ အပြင်ဘက်သို့ ကျွံထွက်ကာ ညှပ်နေခဲ့ပါသည်။
2. **Page Filter ကြောင့် စားပွဲခုံများ ပျောက်ကွယ်နေခြင်း (Filter Data Leak to Modal)**:
   - User အနေဖြင့် `http://localhost:8000/admin/tables?status=OCCUPIED` ဟု စစ်ထုတ်ကြည့်ရှုချိန်တွင် `$tables` Query သည် OCCUPIED ဖြစ်နေသော စားပွဲ (Table 1, Table 2) သာ ပြန်ပေးခဲ့ပါသည်။
   - Modal တွင်လည်း အဆိုပါ Filtered `$tables` ကိုပင် Loop ပတ်ထားသဖြင့် အခြားလွတ်နေသော စားပွဲများ (ဥပမာ- Table 34, Table 5) စသည်တို့သည် Dropdown ထဲတွင် မပါရှိတော့ဘဲ "Table not found (1-47)" ဟူသော အမှား ပြသခဲ့ခြင်း ဖြစ်ပါသည်။

### ၈.၂ ဖြေရှင်းဆောင်ရွက်ခဲ့သော နည်းလမ်းများ (Solutions Implemented)
1. **Modal Layout အား Viewport အတွင်း အံဝင်စေခြင်း (Responsive Fixed Header/Footer & Scrollable Body)**:
   - `.modal-dialog-custom` နှင့် form အား `max-height: min(92vh, 820px); display: flex; flex-direction: column; overflow: hidden;` သို့ ပြောင်းလဲပေးခဲ့ပါသည်။
   - Header နှင့် Footer တို့အား `flex-shrink: 0;` ဖြင့် အပေါ်နှင့် အောက်ဘက်တွင် ပုံသေ ရပ်တည်စေပြီး၊ အလယ်ပိုင်း `.modal-body-custom` အား `overflow-y: auto; flex: 1;` ဖြင့် သီးခြား Scroll ဆွဲနိုင်အောင် ပြင်ဆင်လိုက်သဖြင့် မည်သည့် စခရင်အရွယ်အစားတွင်မဆို ရှင်းလင်းစွာ မြင်တွေ့နိုင်ပါသည်။
2. **Modal အတွက် ဆိုင်တွင်း စားပွဲအားလုံး သီးခြား ပေးပို့ခြင်း (`$allTables`)**:
   - `DiningTableController@index` တွင် စားသောက်ဆိုင်၏ စားပွဲခုံ ၁ မှ ၄၇ ခုလုံးအား `(table_number + 0) ASC` ဖြင့် စီထားသော `$allTables` Collection အား View ထံ သီးခြား ပေးပို့စေခဲ့ပါသည်။
   - Modal အတွင်းရှိ Target Select နှင့် Quick Pick Chips တို့တွင် `$allTables` ကို အသုံးပြုစေလိုက်သဖြင့် User သည် မည်သည့် Filter (`?status=OCCUPIED`, `?area=...`, စသည်) ဖြင့် ကြည့်ရှုနေစေကာမူ Modal ထဲတွင် စားပွဲ ၁ မှ ၄၇ ခုလုံးကို ချောမွေ့စွာ ရိုက်ထည့်/ရွေးချယ်နိုင်ပြီ ဖြစ်ပါသည်။

---

## ၉။ Table Change Modal UI ရှင်းလင်းသပ်ရပ်စေခြင်းနှင့် Reason Input စနစ် ပြင်ဆင်မှု (Modal UI Declutter & Clean Reason Input)

### ၉.၁ ပြုပြင်ပြောင်းလဲခဲ့သော အချက်များ (Key UI Refinements)
1. **မလိုအပ်သော Security Alert Notice Box အား ရှင်းလင်းဖယ်ရှားခြင်း (Removed Security Notice Alert)**:
   - စားပွဲပြောင်းလဲသည့် Modal တွင် ပါရှိနေခဲ့သော အပြာရောင် သတိပေးစာတန်း (`Restaurant-Side Security: Table moves are performed only by restaurant staff...`) သည် နေရာယူမှုများပြီး မျက်စိရှုပ်ထွေးစေခဲ့သဖြင့် အပြီးအပိုင် ဖြုတ်ပယ်ရှင်းလင်းခဲ့ပါသည်။
2. **Reason Dropdown (Choice Box) အား ဖြုတ်ပယ်ပြီး Single Clean Input သာ ထားရှိခြင်း (Removed Reason Preset Dropdown)**:
   - "Reason for Table Change (Optional)" အပိုင်းတွင် ယခင်က ပါရှိခဲ့သော မလိုအပ်ဘဲ ရှုပ်ထွေးနေသည့် Select Dropdown (`-- Quick Select Reason --`) အား လုံးဝဖယ်ရှားလိုက်ပါသည်။
   - ၎င်းအစား ဝန်ထမ်းများ စိတ်ကြိုက် အကြောင်းပြချက် ရိုက်ထည့်လိုပါက ရိုက်ထည့်နိုင်သော (သို့မဟုတ် အလွတ်ထားနိုင်သော) ရိုးရှင်းသန့်ရှင်းသည့် တစ်ကြောင်းတည်း Input Box (`<input type="text" name="reason" placeholder="Type reason or leave blank (e.g. window view, larger table)...">`) ကိုသာ တိုက်ရိုက် ထားရှိပေးထားပါသည်။
   - JavaScript အတွင်းရှိ မလိုအပ်တော့သော `transfer_reason_preset` ရှင်းလင်းမှုများနှင့် Event Handler များကိုလည်း ဖယ်ရှားပြီး Controller/Request နှင့် သန့်ရှင်းစွာ ချိတ်ဆက်ထားပါသည်။

### ၉.၂ အဘယ်ကြောင့် အသုံးပြုရသနည်း (Why)
- **Reduced Visual Clutter & Cognitive Load (မျက်စိရှုပ်ထွေးမှုနှင့် စဉ်းစားရမှု လျှော့ချခြင်း)**: စားသောက်ဆိုင်တွင် ဧည့်သည် စားပွဲပြောင်းလိုသည့်အခါ ဝန်ထမ်းသည် စားပွဲနံပါတ်ရိုက်ထည့်ပြီး အတည်ပြုရုံသာ အဓိကဖြစ်ပါသည်။ မလိုအပ်သော သတိပေးစာသားများနှင့် Dropdown အဆင့်ဆင့် ရွေးချယ်နေရခြင်းတို့ကို ဖယ်ရှားလိုက်ခြင်းဖြင့် Modal UI သည် ပေါ့ပါးသပ်ရပ်ပြီး ကြည့်ရအလွန်ရှင်းလင်းသွားပါသည်။
- **Direct & Fast Input (မြန်ဆန်သွက်လက်သော အသုံးပြုမှု)**: လိုအပ်ပါက အကြောင်းပြချက်ကို တိုက်ရိုက် ရိုက်ထည့်နိုင်ပြီး မလိုပါက လစ်လျူရှုနိုင်သဖြင့် ဝန်ထမ်းများ လုပ်ငန်းဆောင်ရွက်မှု နှောင့်နှေးကြန့်ကြာမှု မရှိစေပါ။
