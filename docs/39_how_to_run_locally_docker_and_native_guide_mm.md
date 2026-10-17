# Restaurant POS စနစ်အား Local စက်တွင် အမှားအယွင်းမရှိ မှန်ကန်စွာ Run နည်း လမ်းညွှန် (Complete Guide: How to Run Locally with Docker & Native Mac)

---

## ၁။ ဖြစ်ပေါ်ခဲ့သော အမှားများ၏ မူလဇစ်မြစ် (Root Cause Analysis)

ယခင်က Local တွင် Run သည့်အခါ အောက်ပါအချက် ၃ ချက်ကြောင့် အမှားများ (Errors) ဖြစ်ပေါ်ခဲ့ခြင်းဖြစ်ပါသည်-

1. **`package.json` Not Found / `vite: command not found` အမှား**:
   - Terminal တွင် root folder (`resturant-pos`) မှနေ၍ `npm run dev` ခေါ်ဆိုချိန်တွင် root ၌ `package.json` မရှိဘဲ `backendPos/` အောက်တွင်သာ ရှိနေခြင်းကြောင့်ဖြစ်ပါသည်။
   - **ဖြေရှင်းပြီးစီးမှု**: Root folder တွင် `package.json` အသစ်တည်ဆောက်ပေးခဲ့ပြီး `npm run dev` နှင့် `npm run build` တို့ကို `backendPos` သို့ proxy ချိတ်ဆက်ပေးထားသဖြင့် မည်သည့် directory မှမဆို အလွယ်တကူ run နိုင်ပါပြီ။
2. **`Database not found / getaddrinfo for backend-db failed` အမှား**:
   - `backendPos/.env` တွင် `DB_HOST=backend-db` ဟု Docker container အချင်းချင်း ခေါ်ဆိုသည့် အမည်ကို ထည့်သွင်းထားခဲ့သဖြင့် Mac host terminal ပေါ်မှ `php artisan serve` ပြေးသည့်အခါ Mac စက်က `backend-db` ကို ရှာမတွေ့ဘဲ connection error တက်ခြင်းဖြစ်ပါသည်။
   - **ဖြေရှင်းပြီးစီးမှု**: `docker-compose.yml` တွင် Docker container အတွက် internal network env (`DB_HOST=backend-db`) ကို သီးခြား override ပေးထားပြီး၊ `backendPos/.env` တွင် Mac host ပေါ်မှ တိုက်ရိုက်ချိတ်ဆက်နိုင်သော `DB_HOST=127.0.0.1`, `DB_PORT=3307` ကို သတ်မှတ်ပေးထားပါသည်။

---

## ၂။ စနစ်အား Local တွင် စတင်လည်ပတ်ပုံ နည်းလမ်း ၂ မျိုး (How to Run Locally)

### နည်းလမ်း (က) - Docker ဖြင့် Run ခြင်း (Standard Docker Setup - အကြံပြုသည်)

ဤနည်းလမ်းသည် MySQL, Redis, Nginx နှင့် PHP-FPM container အားလုံးကို တစ်ပြိုင်နက် မောင်းနှင်ပေးပါသည်။

```bash
# ၁။ Root directory သို့ သွားပါ
cd /Users/kyawwaiyan/Documents/my-Home-tech/resturant-pos

# ၂။ Docker container များကို နောက်ကွယ်မှ စတင်မောင်းနှင်ပါ
docker compose up -d

# ၃။ Asset များကို Build လုပ်ပါ (သို့မဟုတ် Dev Server ဖွင့်ပါ)
npm run build
# သို့မဟုတ် dev watch အတွက်:
npm run dev
```

- **Browser ဖြင့် ဝင်ရောက်ရန် URL**: `http://localhost:8085` (သို့မဟုတ် `http://localhost:8085/admin/login`)

---

### နည်းလမ်း (ခ) - Docker မပါဘဲ Mac Host ပေါ်တွင် Native Run ခြင်း (Native Mac Setup)

အကယ်၍ Docker မသုံးလိုဘဲ Mac terminal ပေါ်တွင် တိုက်ရိုက် `php artisan serve` ဖြင့် စမ်းသပ်လိုပါက:

#### အဆင့် ၁ - Database ချိတ်ဆက်မှု ရွေးချယ်ခြင်း
- **ရွေးချယ်မှု A (Docker MySQL Container ဖွင့်ထားပြီး Mac ပေါ်မှ Serve လုပ်လိုလျှင်)**:
  `backendPos/.env` တွင် လက်ရှိအတိုင်း ထားရှိပါသည်-
  ```env
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3307
  DB_DATABASE=restaurant_pos
  DB_USERNAME=pos_user
  DB_PASSWORD=pos_password
  ```
- **ရွေးချယ်မှု B (Docker လုံးဝမသုံးဘဲ Local SQLite ဖြင့် စမ်းသပ်လိုလျှင်)**:
  `backendPos/.env` တွင် အောက်ပါအတိုင်း ပြောင်းလဲပါ (database.sqlite အသင့်ပါရှိပြီးဖြစ်သည်)-
  ```env
  DB_CONNECTION=sqlite
  CACHE_STORE=file
  SESSION_DRIVER=file
  ```

#### အဆင့် ၂ - Local Server စတင်ခြင်း
```bash
cd /Users/kyawwaiyan/Documents/my-Home-tech/resturant-pos/backendPos

# Port 8000 တွင် အခြား project ရှိပါက port 8081 ဖြင့် ဖွင့်နိုင်ပါသည်
php artisan serve --port=8081
```

- **Browser ဖြင့် ဝင်ရောက်ရန် URL**: `http://127.0.0.1:8081` (သို့မဟုတ် `http://127.0.0.1:8081/admin/login`)

---

## ၃။ စနစ်စမ်းသပ်ရန် အဆင်သင့် User အကောင့်များ (Default Credentials)

| ရာထူး (Role) | အီးမေးလ် (Email) | စကားဝှက် (Password) | အသုံးပြုနိုင်သော စွမ်းဆောင်ရည် |
| :--- | :--- | :--- | :--- |
| **Owner / Superadmin** | `admin@example.com` | `password123` | Back-Office စီမံခန့်ခွဲမှု အပြည့်အစုံ၊ P&L Report၊ Stock Control |
| **Branch Manager** | `manager@example.com` | `password123` | Stock ဖြည့်သွင်းခြင်း၊ Menu ပြင်ဆင်ခြင်း၊ စားပွဲ စီမံခန့်ခွဲခြင်း |
| **Cashier** | `cashier@example.com` | `password123` | ငွေရှင်းကောင်တာ၊ ဘေလ်ထုတ်ပေးခြင်း၊ QR ရှင်းတမ်း |
| **Dining Staff / Waiter** | `staff@example.com` | `password123` | စားပွဲ အော်ဒါယူခြင်း၊ စားပွဲ အခြေအနေ စစ်ဆေးခြင်း |

---

## ၄။ အသုံးဝင်သော Terminal အမိန့်များ (Quick Troubleshooting Commands)

```bash
# Database Migration status စစ်ဆေးရန် (Mac Host ပေါ်မှ တိုက်ရိုက်)
cd backendPos && php artisan migrate:status

# Test suite စစ်ဆေးရန် (165 tests passed)
cd backendPos && php artisan test

# Docker container အားလုံး ရပ်နားရန်
docker compose down
```
