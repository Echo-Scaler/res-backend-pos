# စားသောက်ဆိုင် POS စနစ် - ရာထူးအလိုက် Dashboard URL သီးသန့်ခွဲခြားခြင်းနှင့် လုံခြုံရေးစည်းမျဉ်းများ (Role-Based Dynamic Dashboard Routing)

ဤမှတ်တမ်းသည် Restaurant POS စနစ်တွင် မတူညီသော ဝန်ထမ်းရာထူးများ (`OWNER`, `MANAGER`, `CASHIER`, `STAFF`) စနစ်ထဲသို့ Login ဝင်ရောက်ချိန်တွင် မိမိတို့တာဝန်နှင့် သက်ဆိုင်သည့် သီးသန့် Dashboard URL များသို့ အလိုအလျောက် ပို့ဆောင်ပေးခြင်း (`Dynamic Redirection`) နှင့် ခွင့်ပြုချက်မရှိဘဲ အခြားအပိုင်းများသို့ မဝင်ရောက်နိုင်စေရန် ကာကွယ်ထားသော လုံခြုံရေးစနစ်တည်ဆောက်ပုံကို မြန်မာဘာသာဖြင့် ရှင်းလင်းထားခြင်း ဖြစ်ပါသည်။

---

## ၁။ အဘယ်ကြောင့် ရာထူးအလိုက် Dashboard URL များကို သီးခြားခွဲထုတ်ရသနည်း (Why Role-Specific Routing?)

အပြင်လက်တွေ့ စားသောက်ဆိုင်ကြီးများ (Real-World Restaurant Operations) တွင် တာဝန်ယူရသည့် ကဏ္ဍများသည် အလွန်ကွဲပြားပါသည်-

1. **👑 OWNER (`/admin/dashboard`) - ဆိုင်ပိုင်ရှင် အလုပ်ခွင်**:
   - ဆိုင်၏ အမြတ်ငွေ၊ ဘဏ္ဍာရေးစာရင်းများ၊ မန်နေဂျာခန့်အပ်ခြင်း/ထုတ်ပယ်ခြင်း၊ ဆိုင်၏ အဓိက Settings များကို ချုပ်ကိုင်သည့် Back-Office ဖြစ်သည်။
   - သာမန် ဝန်ထမ်း (သို့မဟုတ်) မန်နေဂျာများ မမြင်သင့်သော အရေးကြီး အချက်အလက်များ ပါဝင်သည်။

2. **👔 MANAGER (`/manager/dashboard`) - ကြီးကြပ်ရေးမှူး အလုပ်ခွင်**:
   - ဆိုင်ကြမ်းပြင်တွင် စားပွဲထိုးများနှင့် ငွေကိုင်များ အဆင်ပြေပြေ အလုပ်လုပ်နေခြင်း ရှိ/မရှိ စစ်ဆေးခြင်း။
   - ဝန်ထမ်းများ၏ Shift အလှည့်ကျစနစ်ကို စီမံခြင်း၊ ပျက်စီးသွားသော Order များကို Void ပယ်ဖျက်ရန် အတည်ပြုပေးခြင်း (Void Approval)။
   - ဘဏ္ဍာရေးအမြတ်ငွေနှင့် ဆိုင်ပိုင်ရှင် သီးသန့်အချက်အလက်များကို ဝင်ခွင့်မရှိစေရ။

3. **💵 CASHIER (`/cashier/dashboard`) - ငွေကိုင်ကောင်တာ POS အလုပ်ခွင်**:
   - ငွေကိုင်သည် စက္ကန့်နှင့်အမျှ ရှင်းစရာရှိသည့် စားပွဲဝိုင်းဘေလ်များကို မြန်မြန်ဆန်ဆန် ရှင်းပေးရပါမည်။
   - Cash Drawer (ငွေသေတ္တာ) ဖွင့်ပိတ်စာရင်း၊ KBZPay / WavePay QR Code၊ MPU/Visa Card ဖြင့် ငွေရှင်းပေးခြင်းတို့ကို အဓိကထားသည့် Screen ဖြစ်ရပါမည်။
   - စီမံခန့်ခွဲမှုဆိုင်ရာ စာရင်းရှုပ်ထွေးမှုများမပါဘဲ အလျင်အမြန် ငွေရှင်းနိုင်သော Counter UI လိုအပ်သည်။

4. **🍽️ STAFF / WAITER (`/staff/dashboard`) - စားပွဲထိုး အလုပ်ခွင်**:
   - စားပွဲထိုးသည် မိုဘိုင်း Tablet (သို့မဟုတ်) POS စက်ဖြင့် ဧည့်သည်များထိုင်နေသော စားပွဲဝိုင်းနံပါတ်များ (`T-01`, `T-02`, စသည်) ကို ကြည့်ပြီး Order ကောက်ရပါမည်။
   - မီးဖိုချောင်မှ ထွက်လာသော ဟင်းလျာများ အဆင်သင့်ဖြစ်ကြောင်း Alert များကို ချက်ချင်းမြင်တွေ့ရမည့် Tableside Order UI ဖြစ်ရပါမည်။

---

## ၂။ URL တည်ဆောက်ပုံနှင့် Route လုံခြုံရေး စည်းမျဉ်းများ (URL Structure & Security Matrix)

| ရာထူး (Role) | သီးသန့် Dashboard URL | Spatie Middleware ကာကွယ်မှု | အခြား Portal များ ဝင်ရောက်ခွင့် |
| :--- | :--- | :--- | :--- |
| **👑 OWNER** | `http://localhost:8000/admin/dashboard` | `['auth', 'role:OWNER']` | အားလုံးသို့ ကြီးကြပ်ရန် ဝင်နိုင်သည် (`200 OK`) |
| **👔 MANAGER** | `http://localhost:8000/manager/dashboard` | `['auth', 'role:OWNER\|MANAGER']` | Cashier, Staff သို့ ကြီးကြပ်နိုင်၊ Admin သို့ ဝင်ခွင့်မရှိ (`403 Forbidden`) |
| **💵 CASHIER** | `http://localhost:8000/cashier/dashboard` | `['auth', 'role:OWNER\|MANAGER\|CASHIER']` | Admin နှင့် Manager သို့ ဝင်ခွင့်မရှိ (`403 Forbidden`) |
| **🍽️ STAFF** | `http://localhost:8000/staff/dashboard` | `['auth', 'role:OWNER\|MANAGER\|STAFF']` | Admin, Manager, Cashier သို့ ဝင်ခွင့်မရှိ (`403 Forbidden`) |

---

## ၃။ အလိုအလျောက် လမ်းကြောင်းပြောင်းလဲပေးခြင်း ယန္တရား (Dynamic Redirection Flow)

`AdminAuthController.php` တွင် မည်သည့် Role မဆို တစ်ခုတည်းသော Login စာမျက်နှာ (`/admin/login` သို့မဟုတ် `/`) မှ ဝင်ရောက်နိုင်ပြီး၊ အထောက်အထားမှန်ကန်ပါက `redirectBasedOnRole()` helper ဖြင့် မိမိရာထူးနှင့် ကိုက်ညီသော Dashboard သို့ အလိုအလျောက် လမ်းကြောင်းလွှဲပေးပါသည်-

```php
public function redirectBasedOnRole(User $user): RedirectResponse
{
    if ($user->hasRole('OWNER')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('MANAGER')) {
        return redirect()->route('manager.dashboard');
    }

    if ($user->hasRole('CASHIER')) {
        return redirect()->route('cashier.dashboard');
    }

    if ($user->hasRole('STAFF')) {
        return redirect()->route('staff.dashboard');
    }

    Auth::logout();
    return redirect()->route('admin.login')->withErrors([
        'email' => 'Your account does not have a designated POS role assigned.',
    ]);
}
```

ထို့အပြင် Navbar ထိပ်ရှိ ဆိုင် Logo Brand ကို နှိပ်ပါကလည်း အခြားရာထူး၏ Dashboard သို့ မရောက်ဘဲ မိမိ၏ Dashboard Route သို့သာ အလိုအလျောက် ပြန်ရောက်စေရန် Blade layout တွင် စီမံထားပါသည်။

---

## ၄။ စမ်းသပ်စစ်ဆေးပြီး အတည်ပြုချက်ရလဒ်များ (Verification & Automated Test Evidence)

Feature Tests စုစုပေါင်း **၃၃ ခု (Assertions ၁၃၆ ခု)** အား အောင်မြင်စွာ စစ်ဆေးပြီးဖြစ်ပါသည်-

1. **Role Redirection Tests**:
   - `owner login redirects to admin dashboard` ✅
   - `manager login redirects to manager dashboard` ✅
   - `cashier login redirects to cashier dashboard` ✅
   - `staff login redirects to staff dashboard` ✅
   - `authenticated cashier visiting home redirects to cashier dashboard` ✅

2. **Security & Boundary Enforcement Tests**:
   - `cashier cannot access owner or manager portal (403 Forbidden)` ✅
   - `staff cannot access owner manager or cashier portal (403 Forbidden)` ✅
   - `manager cannot access owner portal (403 Forbidden)` ✅
   - `owner can access all portals for supervision (200 OK)` ✅

3. **Code Formatting & Clean Code**:
   - Laravel Pint: PHP ဖိုင်ပေါင်း ၄၅ ဖိုင်လုံး စံသတ်မှတ်ချက်နှင့်အညီ သန့်ရှင်းမှုရှိကြောင်း အတည်ပြုပြီး။
