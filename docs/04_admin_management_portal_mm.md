# အဆင့် (၄) - Back-Office Admin Management Portal နှင့် Login စနစ် တည်ဆောက်ခြင်း ရှင်းလင်းချက်

ဤမှတ်တမ်းသည် Restaurant POS စနစ်၏ **Back-Office Management System (စီမံခန့်ခွဲမှု ဗဟို)** အတွက် Visual Web UI (မျက်နှာပြင်) ဖြင့် Browser ပေါ်မှ ဝင်ရောက်နိုင်သော **Admin Login (`/admin/login`)** နှင့် **Admin Management Dashboard (`/admin/dashboard`)** တည်ဆောက်ပုံ အဆင့်ဆင့်နှင့် အဘယ်ကြောင့် ဤဗိသုကာကို အသုံးပြုရသနည်းဆိုသည့် အချက်များကို မြန်မာဘာသာဖြင့် အသေးစိတ် ရှင်းလင်းထားပါသည်။

---

## ၁။ စနစ်ခွဲခြားမှု ဗိသုကာ (System Architecture & Separation of Concerns)

Restaurant POS ပရောဂျက်တွင် အစိတ်အပိုင်း (၂) ခုကို အောက်ပါအတိုင်း တိကျစွာ ခွဲခြားထားပါသည် -

| အစိတ်အပိုင်း | ဖိုဒါ | အဓိက တာဝန်နှင့် အသုံးပြုမည့်သူ |
| :--- | :--- | :--- |
| **Back-Office Management Portal** | `backendPos` | **ဆိုင်ရှင် (Owner) နှင့် Manager များ** အတွက် Menu စာရင်းများ၊ ကုန်ပစ္စည်း/ကုန်ကြမ်းများ၊ ဝန်ထမ်းများ၊ စားပွဲဝိုင်းများနှင့် အရောင်းစာရင်းများကို စီမံခန့်ခွဲသည့် Web Dashboard။ |
| **POS Customer / Waiter App** | `frontendPos` | **စားသုံးသူများနှင့် စားပွဲထိုးများ** အတွက် Tablet / Mobile / Touchscreen ပေါ်တွင် Menu ပြသခြင်း၊ အော်ဒါယူခြင်းနှင့် ငွေရှင်းကောင်တာ POS မျက်နှာပြင်။ |

---

## ၂။ အကောင်အထည်ဖော်ခဲ့သော Web Route များနှင့် လုံခြုံရေး

| HTTP Method | Route URL | Route Name | လိုအပ်သော ခွင့်ပြုချက် | ရှင်းလင်းချက် |
| :--- | :--- | :--- | :--- | :--- |
| `GET` | `/` & `/admin/login` | `admin.login` | Public | ခေတ်မီလှပသော Restaurant Admin Login မျက်နှာပြင်ကို ပြသသည်။ |
| `POST` | `/admin/login` | `admin.login.submit` | Public | Email နှင့် Password စစ်ဆေး၍ Session စတင်သည်။ Owner/Manager မဟုတ်ပါက အလိုအလျောက် ပိတ်ပင်သည်။ |
| `GET` | `/admin/dashboard` | `admin.dashboard` | `auth`, `role:OWNER\|MANAGER` | ဆိုင်၏ အချက်အလက်၊ Role Badge များနှင့် Management Modules များကို ပြသသည့် ဗဟို Dashboard။ |
| `POST` | `/admin/logout` | `admin.logout` | `auth` | Session ကို ဖျက်ပစ်ပြီး Login မျက်နှာပြင်သို့ ပြန်လည်ပို့ဆောင်သည်။ |

---

## ၃။ အဘယ်ကြောင့် ဤ Feature များကို အသုံးပြုရသနည်း (Why Use Each Feature)

### (၁) အဘယ်ကြောင့် Web Session Auth နှင့် API Sanctum Token နှစ်မျိုးလုံးကို တွဲဖက်အသုံးပြုသနည်း။
- **Web Browser အတွက် Session Cookies**: Admin Portal ကို ကွန်ပျူတာ Browser ဖြင့် အသုံးပြုသည့်အခါ Secure HTTP-Only Cookie Session သည် အသုံးပြုရ အလွန်လွယ်ကူပြီး CSRF Attack များမှ အလိုအလျောက် ကာကွယ်ပေးနိုင်ပါသည်။
- **POS Devices များအတွက် Bearer Token**: စားပွဲထိုး Tablet သို့မဟုတ် Cashier စက်များ (`frontendPos`) မှ Request ပို့ရာတွင်မူ ပေါ့ပါးမြန်ဆန်ပြီး Cross-Origin ချိတ်ဆက်နိုင်သော Sanctum Token စနစ်ကို အသုံးပြုပါသည်။

### (၂) အဘယ်ကြောင့် `role:OWNER|MANAGER` Middleware ဖြင့် ကာကွယ်ရသနည်း။
- စားသောက်ဆိုင်တွင် စားပွဲထိုး (Staff) သို့မဟုတ် ငွေကိုင် (Cashier) များသည် POS စက်တွင် Login ဝင်နိုင်သော်လည်း ဆိုင်၏ အဓိက စီမံခန့်ခွဲမှု Dashboard ထဲသို့ ဝင်ရောက်ခွင့် **လုံးဝ မရှိစေရပါ**။
- Spatie Middleware ဖြင့် စစ်ဆေးထားသောကြောင့် `OWNER` နှင့် `MANAGER` အကောင့်များသာ Admin Dashboard သို့ ဝင်ရောက်ခွင့် ရရှိမည် ဖြစ်ပါသည်။

### (၃) Quick Demo Auto-Fill ခလုတ် ထည့်သွင်းထားရခြင်း အကြောင်းရင်း
- Local Development နှင့် Testing ပြုလုပ်ရာတွင် အချိန်ကုန်သက်သာစေရန်အတွက် Default Owner Account (`admin@example.com` / `password123`) ကို 1-Click ဖြင့် အလိုအလျောက် ဖြည့်ပေးနိုင်သော JavaScript helper ကို Login စခရင်တွင် ထည့်သွင်းပေးထားပါသည်။

---

## ၄။ Browser ပေါ်တွင် တိုက်ရိုက် စတင်အသုံးပြုနည်း

1. မိမိ၏ Web Browser (Chrome / Safari) တွင် အောက်ပါလိပ်စာကို ဖွင့်ပါ -
   ```text
   http://localhost:8000/admin/login
   ```
   *(သို့မဟုတ် `http://localhost:8000/` သို့ သွားပါကလည်း Login စခရင်သို့ တိုက်ရိုက် ရောက်ရှိပါမည်)*

2. **Login အချက်အလက်များ ဖြည့်သွင်းပါ**:
   - **Email**: `admin@example.com`
   - **Password**: `password123`
   *(သို့မဟုတ် "Auto-Fill Owner Credentials" ခလုတ်ကို နှိပ်ပါ)*

3. **"Sign In to Dashboard"** ခလုတ်ကို နှိပ်လိုက်သည်နှင့် `Rangoon Spice Kitchen` ၏ **Admin Management Dashboard (`/admin/dashboard`)** သို့ အောင်မြင်စွာ ဝင်ရောက်သွားမည် ဖြစ်ပါသည်။

---

## ၅။ စမ်းသပ်စစ်ဆေးမှု ရလဒ်များ (Automated Test Results)

```bash
$ docker compose exec backend-app php artisan test

   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\Admin\AdminDashboardTest
  ✓ guest is redirected to admin login                                   0.16s  
  ✓ admin login page renders successfully                                0.02s  
  ✓ owner can login with valid credentials and redirects to dashboard    0.02s  
  ✓ login fails with invalid credentials                                 0.21s  
  ✓ authenticated owner can view dashboard                               0.03s  
  ✓ user without owner or manager role cannot access dashboard           0.02s  
  ✓ owner can logout from admin portal                                   0.01s  

   PASS  Tests\Feature\Auth\AuthenticationTest
  ✓ login fails with invalid credentials returning 401                   0.02s  
  ✓ login succeeds and returns token user restaurant and roles           0.01s  
  ✓ me endpoint requires authentication                                  0.01s  
  ✓ me endpoint returns authenticated owner and restaurant               0.01s  
  ✓ logout revokes current token                                         0.01s  

   PASS  Tests\Feature\Auth\RestaurantSetupTest
  ✓ restaurant setup succeeds and creates owner with role and token      0.02s  
  ✓ setup fails with duplicate email                                     0.01s  
  ✓ setup fails with password confirmation mismatch                      0.01s  
  ✓ password is never returned in api response                           0.01s  
  ✓ setup transaction rolls back when operation fails                    0.01s  

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response                        0.01s  

  Tests:    19 passed (83 assertions)
  Duration: 0.73s
```
