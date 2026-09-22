# စားသောက်ဆိုင် POS စနစ် - Form Request နှင့် API Resource သီးခြားခွဲထုတ်ခြင်း စည်းမျဉ်း (Mandatory Form Requests & API Resources Architecture)

ဤမှတ်တမ်းသည် Restaurant POS စနစ်တွင် Controller များကို သန့်ရှင်းသပ်ရပ်စေရန် (`Skinny Controllers`)၊ Input Validation များနှင့် လုပ်ပိုင်ခွင့်စစ်ဆေးခြင်းများကို သီးခြားခွဲထုတ်ရန် (`Form Requests`) နှင့် ဒေတာများကို လုံခြုံစွာ Output ထုတ်ပေးနိုင်ရန် (`API Resources`) တည်ဆောက်ထားသည့် အဓိက စည်းမျဉ်းစည်းကမ်းနှင့် နည်းပညာတည်ဆောက်ပုံကို မြန်မာဘာသာဖြင့် အသေးစိတ် မှတ်တမ်းတင်ထားခြင်း ဖြစ်ပါသည်။

---

## ၁။ အဘယ်ကြောင့် ဤစည်းမျဉ်းကို မဖြစ်မနေ လိုက်နာရသနည်း (Why this Rule?)

စားသောက်ဆိုင် POS စနစ်ကဲ့သို့သော Real-Time ငွေကြေးနှင့် အော်ဒါစီမံခန့်ခွဲမှု စနစ်များတွင် ကုတ်ရေးသားမှု ရှုပ်ထွေးသွားပါက Bug များဖြစ်ပေါ်ခြင်း၊ အရေးကြီးသော လျှို့ဝှက်အချက်အလက်များ (ဥပမာ PIN Code, Passwords) မတော်တဆ အပြင်သို့ ပေါက်ကြားသွားခြင်းများ ဖြစ်ပေါ်နိုင်ပါသည်-

1. **Controller များအတွင်း Validation ရောထွေးမှု ကင်းဝေးစေခြင်း**:
   - Controller Method တစ်ခုချင်းစီတွင် `$request->validate([...])` ဟု ရှည်လျားစွာ ရေးသားနေမည့်အစား သီးသန့် Request Class (ဥပမာ `StoreEmployeeRequest`, `UpdateEmployeeRequest`) များသို့ လွှဲပြောင်းပေးလိုက်ခြင်းဖြင့် Single Responsibility Principle (SRP) ကို အပြည့်အဝ လိုက်နာနိုင်ပါသည်။
2. **POS Quick PIN နှင့် စကားဝှက်များ လုံခြုံစွာ ကာကွယ်နိုင်ခြင်း**:
   - Database မှ ထွက်လာသော User / Employee Data များကို JSON Response သို့ တိုက်ရိုက်မထုတ်ပေးဘဲ `EmployeeResource` ဖြင့်သာ ကြားခံ စစ်ထုတ်ပေးပါသည်။
   - ဝန်ထမ်းတစ်ဦးတွင် PIN သတ်မှတ်ထားခြင်း ရှိ/မရှိ (`has_pin: true/false`) ကိုသာ အသိပေးပြီး၊ အမှန်တကယ် PIN Code သို့မဟုတ် Password Hash များကို မည်သည့်အခါမျှ မပေါက်ကြားစေရန် တင်းကြပ်စွာ ကာကွယ်ပေးပါသည်။
3. **Multi-Tenant Authorization စည်းမျဉ်းကို Form Request တွင် တစ်ခါတည်း စစ်ဆေးနိုင်ခြင်း**:
   - Form Request ၏ `authorize()` method တွင် အသုံးပြုသူသည် သက်ဆိုင်ရာ ဆိုင်ခွဲမှ ဟုတ်/မဟုတ်၊ Owner သို့မဟုတ် Manager အဆင့်ရှိ/မရှိကို Controller မရောက်မီ ကြိုတင်စစ်ဆေး ဖယ်ထုတ်နိုင်ပါသည်။

---

## ၂။ လက်တွေ့ အကောင်အထည်ဖော်ထားသော နမူနာ ဖိုင်တည်ဆောက်ပုံ

### ၂.၁ Form Requests (`app/Http/Requests/Admin/Employee/`)
- **`StoreEmployeeRequest.php`**:
  - ဝန်ထမ်းသစ် ထည့်သွင်းချိန်တွင် အမည်၊ အီးမေးလ်၊ ဖုန်း၊ ရာထူး (Owner ဖြစ်ပါက Manager/Cashier/Staff၊ Manager ဖြစ်ပါက Cashier/Staff)၊ စကားဝှက်နှင့် ၄-၆ လုံး POS PIN များကို စစ်ဆေးပေးခြင်း။
- **`UpdateEmployeeRequest.php`**:
  - ဝန်ထမ်းအချက်အလက် ပြင်ဆင်ချိန်တွင် Manager သည် အခြား Manager သို့မဟုတ် Owner အကောင့်များကို ပြင်ဆင်ခွင့်မရှိစေရန် `authorize()` ဖြင့် စည်းမျဉ်းသတ်မှတ်ထားခြင်း။

### ၂.၂ API Resource (`app/Http/Resources/`)
- **`EmployeeResource.php`**:
  - Output ဒေတာ ပုံစံထုတ်ပေးခြင်း-
  ```php
  return [
      'id' => $this->id,
      'restaurant_id' => $this->restaurant_id,
      'name' => $this->name,
      'email' => $this->email,
      'phone' => $this->phone,
      'role' => $this->getRoleNames()->first() ?? 'STAFF',
      'permissions' => $this->getAllPermissions()->pluck('name')->values()->all(),
      'has_pin' => ! empty($this->pin_code), // ✅ PIN Code အစစ်အမှန် မပါစေဘဲ boolean ဖြင့်သာ ဖော်ပြခြင်း
      'created_at' => $this->created_at?->toIso8601String(),
  ];
  ```

---

## ၃။ အတည်ပြုချက်နှင့် စမ်းသပ်စစ်ဆေးမှု ရလဒ်များ (Test Evidence)

- Feature & Unit Tests စုစုပေါင်း **၄၈ ခု (Assertions ၂၄၄ ခု)** အားလုံး Pass ဖြစ်ကြောင်း အတည်ပြုပြီး-
  - `employee resource transforms data securely without exposing pin` ✅
  - `store employee request validation & role permissions` ✅
  - `update employee request authorization boundaries` ✅

- Laravel Pint စံသတ်မှတ်ချက်အရ **ဖိုင်ပေါင်း ၅၄ ဖိုင်လုံး** သန့်ရှင်းမှု အပြည့်အဝ ရှိကြောင်း စစ်ဆေးပြီး (`PASS: 54 files`)။
