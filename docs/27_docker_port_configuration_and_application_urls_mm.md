# Docker Port ပြင်ဆင်သတ်မှတ်ခြင်းနှင့် Application URLs အသုံးပြုနည်းလမ်းညွှန်

## ၁။ ပြဿနာဖြစ်ပေါ်ရသည့် အကြောင်းအရင်း (Root Cause Analysis)

စနစ်အား `docker compose up` ဖြင့် စတင်လည်ပတ်ချိန်တွင် အောက်ပါ Port Conflict Error ဖြစ်ပေါ်ခဲ့ပါသည်-
```
Error response from daemon: failed to set up container networking: driver failed programming external connectivity on endpoint pos-backend-redis: Bind for 0.0.0.0:6379 failed: port is already allocated
```

### အဘယ်ကြောင့် ဤပြဿနာဖြစ်ရသနည်း (Why did this happen?)
Local ကွန်ပျူတာပေါ်တွင် အခြား Docker Project တစ်ခု (`laravel-nestjs-ecom`) သည် Host machine ၏ Port `6379` (Redis) နှင့် Port `8000` (Backend Web) တို့ကို ကြိုတင်ရယူ (bind) အသုံးပြုထားသောကြောင့် ဖြစ်ပါသည်။

---

## ၂။ ဖြေရှင်းဆောင်ရွက်ခဲ့သည့် ဗိသုကာပုံစံ (Architecture & Port Resolution)

အခြား running container များကို ရပ်တန့်စရာမလိုဘဲ Restaurant POS project အား ပြိုင်တူ (side-by-side) ချောမွေ့စွာ run နိုင်ရန်အတွက် `docker-compose.yml` နှင့် `backendPos/.env` တို့တွင် Port များကို အောက်ပါအတိုင်း ပြင်ဆင်သတ်မှတ်ခဲ့ပါသည်-

| Container Service | နဂို Host Port | ပြင်ဆင်ပြီး Host Port | Container Internal Port | ရည်ရွယ်ချက်နှင့် အကြောင်းပြချက် |
| :--- | :--- | :--- | :--- | :--- |
| **`pos-backend-web` (Nginx)** | `8000` | **`8085`** | `80` | အခြား Project ၏ port 8000 နှင့် မငြိဘဲ Web Browser မှ တိုက်ရိုက် access လုပ်နိုင်ရန် |
| **`pos-backend-redis` (Redis)** | `6379` | **`6380`** | `6379` | Redis Host port conflict ကို ရှောင်ရှားရန် (Container အချင်းချင်း ဆက်သွယ်မှုမှာ docker network မှတစ်ဆင့် ချိတ်ဆက်သဖြင့် ထိခိုက်မှုမရှိပါ) |
| **`pos-backend-db` (MySQL)** | `3306` | `3306` | `3306` | လက်ရှိ Port 3306 တွင် conflict မရှိသဖြင့် မူလအတိုင်း ဆက်လက်အသုံးပြုသည် |
| **`pos-backend-app` (PHP-FPM)**| - | - | `9000` | Nginx မှ FastCGI ဖြင့် စီမံခန့်ခွဲသည် |

---

## ၃။ အသုံးပြုနိုင်သော စနစ် URLs (Application URLs Directory)

လက်ရှိ Web Container သည် Port **`8085`** တွင် အောင်မြင်စွာ run လျက်ရှိပြီး အောက်ပါ URLs များအား Browser တွင် တိုက်ရိုက်ဖွင့်လှစ် အသုံးပြုနိုင်ပါသည်-

### က။ အကောင့်ဝင်ရောက်ခြင်း (Authentication)
* **စနစ် Login စာမျက်နှာ**: [http://localhost:8085/admin/login](http://localhost:8085/admin/login) သို့မဟုတ် [http://localhost:8085/](http://localhost:8085/)

### ခ။ ရာထူးအလိုက် Dashboard ပေါ်တယ်များ (Role-Based Portals)
1. **ဆိုင်ရှင် (Owner Executive Dashboard)**:
   * URL: [http://localhost:8085/admin/dashboard](http://localhost:8085/admin/dashboard)
   * ခွင့်ပြုချက်: စားသောက်ဆိုင်၏ ဘဏ္ဍာရေး၊ အမြတ်အစွန်း၊ ဝန်ထမ်း၊ မီနူး၊ စာရင်းအင်းနှင့် စနစ်တစ်ခုလုံးကို အပြည့်အဝ စီမံခန့်ခွဲခွင့်ရှိသည်။
2. **မန်နေဂျာ (Operations & Floor Manager Portal)**:
   * URL: [http://localhost:8085/manager/dashboard](http://localhost:8085/manager/dashboard)
   * ခွင့်ပြုချက်: ဝန်ထမ်းများ၊ စားပွဲဝိုင်းများ၊ ကုန်ကျစရိတ်အတည်ပြုချက်နှင့် နေ့စဉ်ဆိုင်လုပ်ငန်းဆောင်ရွက်မှုများ။
3. **ငွေကိုင် (Cashier POS Register Portal)**:
   * URL: [http://localhost:8085/cashier/dashboard](http://localhost:8085/cashier/dashboard)
   * ခွင့်ပြုချက်: ငွေရှင်းကောင်တာ၊ ဘေလ်ရှင်းတမ်းထုတ်ပေးခြင်း၊ ငွေလက်ခံခြင်း။
4. **စားပွဲထိုး / ဝန်ထမ်း (Staff / Waiter Floor Ordering Portal)**:
   * URL: [http://localhost:8085/staff/dashboard](http://localhost:8085/staff/dashboard)
   * ခွင့်ပြုချက်: စားပွဲဝိုင်း အော်ဒါယူခြင်း၊ စားပွဲအခြေအနေစစ်ဆေးခြင်း။

### ဂ။ အဓိက မော်ဂျူးကြီးများ (Core Business Modules)
* **စားပွဲဝိုင်းနှင့် Dynamic QR Generator (Tables Management)**:
  * URL: [http://localhost:8085/admin/tables](http://localhost:8085/admin/tables)
* **မီးဖိုချောင်နှင့် အော်ဒါစစ်ဆေးအတည်ပြုခြင်း (Order Verification Pass)**:
  * URL: [http://localhost:8085/admin/orders/verification](http://localhost:8085/admin/orders/verification)
* **အစားအသောက် မီနူးနှင့် ဈေးနှုန်းများ (Menu & Dishes)**:
  * URL: [http://localhost:8085/admin/menu](http://localhost:8085/admin/menu)
* **ကုန်ကြမ်းပစ္စည်းလက်ကျန် (Inventory & Stock Levels)**:
  * URL: [http://localhost:8085/admin/inventory](http://localhost:8085/admin/inventory)
* **ဆိုင်တွင်း အသုံးစရိတ်စီမံခန့်ခွဲမှု (Expense Management)**:
  * URL: [http://localhost:8085/admin/expenses](http://localhost:8085/admin/expenses)
* **အရောင်းနှင့် စီးပွားရေးအစီရင်ခံစာများ (Sales & COGS Reports)**:
  * URL: [http://localhost:8085/admin/reports](http://localhost:8085/admin/reports)

### ဃ။ ဧည့်သည် စားပွဲဝိုင်းမှ Mobile QR စကင်ဖတ်ပြီး အော်ဒါတင်ခြင်း (Customer QR Self-Ordering)
ဧည့်သည်များ စားပွဲဝိုင်းရှိ QR Code အား မိုဘိုင်းဖုန်းဖြင့် Scan ဖတ်၍ တိုက်ရိုက် အော်ဒါတင်နိုင်သော Public Web Menu URL ပုံစံ:
* **Table 1**: [http://localhost:8085/order/table/XlPGhgJN8FcfNVvheW4NIwpJzRe7eVf8](http://localhost:8085/order/table/XlPGhgJN8FcfNVvheW4NIwpJzRe7eVf8)
* **Table 2**: [http://localhost:8085/order/table/ADuMnUy3Hpuc2olm2tJOarYyH3w06sPM](http://localhost:8085/order/table/ADuMnUy3Hpuc2olm2tJOarYyH3w06sPM)
* **Table 3**: [http://localhost:8085/order/table/DKQLQ0h7fp5aS0AkDw7AuVme9jTZMF7m](http://localhost:8085/order/table/DKQLQ0h7fp5aS0AkDw7AuVme9jTZMF7m)

---

## ၄။ စမ်းသပ်အသုံးပြုနိုင်သော စနစ်အကောင့်များနှင့် လျှို့ဝှက်နံပါတ်များ (Default Credentials)

| ရာထူး (Role) | အီးမေးလ် (Email) | စကားဝှက် (Password) | ဝင်ရောက်နိုင်သော Portal |
| :--- | :--- | :--- | :--- |
| **OWNER (ဆိုင်ရှင်)** | `admin@example.com` | `password123` | [Admin Dashboard](http://localhost:8085/admin/dashboard) |
| **MANAGER (မန်နေဂျာ)** | `manager@example.com` | `password123` | [Manager Dashboard](http://localhost:8085/manager/dashboard) |
| **CASHIER (ငွေကိုင်)** | `cashier@example.com` | `password123` | [Cashier Dashboard](http://localhost:8085/cashier/dashboard) |
| **STAFF (စားပွဲထိုး)** | `staff@example.com` | `password123` | [Staff Dashboard](http://localhost:8085/staff/dashboard) |

---

## ၅။ Docker စတင်/ရပ်တန့် အသုံးပြုနည်း အမိန့်များ (Commands)

```bash
# Containers အားလုံးအား Background တွင် စတင်လည်ပတ်ခြင်း
docker compose up -d

# Containers အခြေအနေ စစ်ဆေးခြင်း
docker compose ps

# Container log များကို စောင့်ကြည့်ခြင်း
docker compose logs -f backend-web

# Containers အားလုံးအား ရပ်တန့်ခြင်း
docker compose down
```
