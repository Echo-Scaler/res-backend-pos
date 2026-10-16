# Vercel Deployment & Cloud Architecture Guide (မြန်မာဘာသာဖြင့် အဆင့်ဆင့် လမ်းညွှန်ချက်)

ဤမှတ်တမ်းသည် Restaurant POS Backend (`backendPos` / Laravel 11) စနစ်အား **Vercel** (`https://vercel.com/kyaw-wai-yans-projects`) ပေါ်တွင် တင်၍ စမ်းသပ်စစ်ဆေးနိုင်ရန် လိုအပ်သော အဆင့်များ၊ ဗိသုကာသဘောတရားများ၊ ကန့်သတ်ချက်များနှင့် Cloud Database ချိတ်ဆက်ပုံတို့ကို အသေးစိတ် ရှင်းလင်းထားသော လမ်းညွှန်ဖြစ်ပါသည်။

---

## ၁။ Vercel ပေါ်တွင် Laravel တင်ခြင်း သဘောတရားနှင့် အဘယ်ကြောင့် ခွဲခြားသိမြင်ရမည်နည်း (Architecture & Why)

### (က) Vercel ၏ သဘောသဘာဝ (Serverless Architecture)
1. **Serverless Execution**:
   - Vercel သည် Node.js, Next.js, React နှင့် Frontend Static UI များအတွက် အဓိက ဒီဇိုင်းထုတ်ထားသော Serverless Platform ဖြစ်ပါသည်။
   - User တစ်ဦး request လာမှသာ Serverless Function ခေတ္တနိုးထလာပြီး အလုပ်လုပ်ကာ ပြန်ပိတ်သွားပါသည် (Stateless / Cold Start)။
2. **Read-Only Filesystem**:
   - Vercel Serverless Function အတွင်းရှိ Code ဖိုင်များသည် Read-only ဖြစ်ပြီး `/tmp` directory တစ်ခုတည်းသာ ယာယီ ဖိုင်ရေးခွင့် (Writeable) ရှိပါသည်။
   - ထို့ကြောင့် Laravel ၏ Compiled Blade Views, Framework Cache များကို `/tmp` သို့ ညွှန်ပေးရပါသည် (`VIEW_COMPILED_PATH=/tmp/storage/framework/views`)။
3. **Database မပါဝင်ခြင်း**:
   - Vercel ပေါ်တွင် Local MySQL Container run ၍ မရပါ။ ထို့ကြောင့် အပြင် Cloud Database (Free TiDB Cloud, Aiven MySQL, သို့မဟုတ် Railway MySQL) တစ်ခုခုနှင့် ချိတ်ဆက်ရပါမည်။

### (ခ) Restaurant POS စနစ်အတွက် သတိပြုရန် (Production vs Testing)
- **စမ်းသပ်ရန် (Testing & Preview)**: Vercel ပေါ်တွင် Web Admin UI, Settings, Security, Reports များကို အလွယ်တကူ URL share ၍ စမ်းသပ်ကြည့်ရှုနိုင်ပါသည်။
- **တကယ့်ဆိုင်တွင်း Production လုပ်ငန်းခွင် (Live Restaurant POS)**:
  - ဆိုင်တွင်း Thermal Receipt Printer (LAN/USB) ချိတ်ဆက်ခြင်း၊ Kitchen Display System (WebSocket / Long Polling)၊ Cash Drawer kick-out signal များနှင့် နှောင့်နှေးမှုမရှိသော Offline-resilient operation များအတွက် **Docker Container VPS (DigitalOcean / AWS EC2 / Railway / On-premise Mini PC)** ကို သုံးစွဲရန် အကြံပြုပါသည်။

---

## ၂။ ပရောဂျက်တွင် ကြိုတင်ထည့်သွင်းပေးထားသော Configuration ဖိုင်များ

Vercel ပေါ်တွင် Laravel အလုပ်လုပ်နိုင်ရန် အောက်ပါဖိုင် ၃ ခုကို repository တွင် ဖန်တီးပေးထားပါသည်-

1. **`backendPos/vercel.json`**:
   - PHP runtime engine အဖြစ် `vercel-php@0.7.3` ကို အသုံးပြုသည်။
   - Static asset လမ်းကြောင်းများ (`/css`, `/js`, `/images`, `/assets`) ကို `public/` သို့ လမ်းကြောင်းလွှဲပြီး အခြား route အားလုံးကို `api/index.php` သို့ ပေးပို့သည်။
   - Environment variable များ (Cache & Storage ကို `/tmp` သို့ သတ်မှတ်ခြင်း) ပါဝင်သည်။
2. **`backendPos/api/index.php`**:
   - Serverless entrypoint ဖြစ်ပြီး `/tmp/storage/framework/views` စသည့် လိုအပ်သော folder များကို auto ဖန်တီးပေးကာ Laravel `public/index.php` ကို ခေါ်ယူသည်။
3. **`backendPos/.vercelignore`**:
   - မလိုအပ်သော `tests/`, `docs/`, local caches များကို build upload ထဲမပါအောင် ဖယ်ထုတ်ပေးသည်။

---

## ၃။ အဆင့်ဆင့် Deploy ပြုလုပ်နည်း (Step-by-Step Instructions)

### အဆင့် (၁) - အခမဲ့ Cloud MySQL Database ပြင်ဆင်ခြင်း
Vercel ပေါ်တွင် MySQL database မရှိသဖြင့် အောက်ပါ အခမဲ့ ဝန်ဆောင်မှုတစ်ခုခုတွင် MySQL database တည်ဆောက်ပါ-
- **TiDB Cloud Serverless** (အကြံပြုချက် - 5GB Free MySQL-compatible, Highly Reliable):
  1. [https://tidbcloud.com](https://tidbcloud.com) သို့ ဝင်ပြီး အခမဲ့ အကောင့်ဖွင့်ပါ။
  2. "Create Cluster" -> "Serverless" (Free Tier) ကို ရွေးပါ။
  3. ရရှိလာသော `Host`, `Port (4000)`, `User`, `Password`, `Database` များကို မှတ်ထားပါ။
- *(သို့မဟုတ် Aiven Cloud MySQL, Clever Cloud, Railway MySQL ကိုလည်း အသုံးပြုနိုင်ပါသည်)*။

> **Note**: Database ရလျှင် local မှနေ၍ `php artisan migrate --seed` ဖြင့် Table များနှင့် Owner အကောင့်ကို အရင် run ထားရပါမည်-
> ```bash
> php artisan migrate --force --seed
> ```

---

### အဆင့် (၂) - Vercel Dashboard တွင် Project Import ပြုလုပ်ခြင်း
1. Browser တွင် [https://vercel.com/kyaw-wai-yans-projects](https://vercel.com/kyaw-wai-yans-projects) သို့ ဝင်ရောက်ပါ။
2. **"Add New..."** ခလုတ်ကို နှိပ်ပြီး **"Project"** ကို ရွေးချယ်ပါ။
3. **"Import Git Repository"** အောက်တွင် သင်၏ GitHub Repository ဖြစ်သော **`Echo-Scaler/res-backend-pos`** ကို ရှာပြီး **"Import"** ခလုတ်ကို နှိပ်ပါ။
4. **Project Settings**:
   - **Framework Preset**: `Other` (သို့မဟုတ် အလိုအလျောက် သတ်မှတ်ထားသည့်အတိုင်း ထားပါ)။
   - **Root Directory**: အကယ်၍ repository ထဲတွင် `backendPos` folder သီးသန့်ဖြစ်နေပါက "Edit" နှိပ်၍ `backendPos` ကို ရွေးပေးပါ (Root တစ်ခုလုံး repository ဖြစ်နေပါက `./` အတိုင်း ထားပါ)။

---

### အဆင့် (၃) - Vercel Environment Variables ထည့်သွင်းခြင်း (မဖြစ်မနေ လိုအပ်သည်)
Vercel Project Setup စာမျက်နှာရှိ **"Environment Variables"** အကွက်တွင် အောက်ပါ key/value များကို တစ်ခုချင်း ထည့်သွင်းပေးပါ-

| Variable Name | Sample Value | ရှင်းလင်းချက် |
|---|---|---|
| `APP_NAME` | `Restaurant POS` | စနစ်အမည် |
| `APP_ENV` | `production` | Deployment Environment |
| `APP_KEY` | `base64:XFq52+4j7n...` | သင်၏ Local `.env` မှ `APP_KEY` ကို ကူးထည့်ပါ |
| `APP_DEBUG` | `true` | စမ်းသပ်ချိန် Error တွေ့လျှင် မြင်ရစေရန် |
| `APP_URL` | `https://your-app.vercel.app` | Vercel မှ ပေးမည့် Project URL |
| `DB_CONNECTION` | `mysql` | MySQL ချိတ်ဆက်မှု |
| `DB_HOST` | `gateway01.ap-southeast-1.prod.aws.tidbcloud.com` | Cloud DB Host |
| `DB_PORT` | `4000` | Cloud DB Port (TiDB=4000, Normal=3306) |
| `DB_DATABASE` | `restaurant_pos` | Database အမည် |
| `DB_USERNAME` | `xxxxxx.root` | Database Username |
| `DB_PASSWORD` | `your_secret_password` | Database Password |
| `MYSQL_ATTR_SSL_CA` | `/etc/ssl/certs/ca-certificates.crt` | TiDB/Aiven အတွက် SSL အသုံးပြုခြင်း |
| `SESSION_DRIVER` | `cookie` | Serverless တွင် Session ပျောက်မသွားစေရန် |
| `CACHE_STORE` | `array` | Serverless ယာယီ Cache |
| `QUEUE_CONNECTION` | `sync` | တန်းစီစနစ် ချက်ချင်း run ရန် |

---

### အဆင့် (၄) - Deploy ခလုတ်ကို နှိပ်ခြင်း
1. အချက်အလက်များ ပြည့်စုံပါက **"Deploy"** ခလုတ်ကို နှိပ်ပါ။
2. Vercel က ကုဒ်များကို build လုပ်ပြီး မိနစ်ပိုင်းအတွင်း Deployment URL ထုတ်ပေးပါမည် (ဥပမာ `https://res-backend-pos.vercel.app`)။

---

## ၄။ Deployment စမ်းသပ်စစ်ဆေးခြင်း (Verification)

Vercel URL ရရှိပါက အောက်ပါ လမ်းကြောင်းများကို စမ်းသပ်နိုင်ပါသည်-
1. **Health Check & Welcome Page**:
   - `https://<your-project>.vercel.app/`
2. **Owner Login Page**:
   - `https://<your-project>.vercel.app/login`
3. **Account Security Dashboard**:
   - `https://<your-project>.vercel.app/admin/account/security`
4. **Restaurant Settings**:
   - `https://<your-project>.vercel.app/admin/settings/restaurant`
5. **Audit Logs & Reports**:
   - `https://<your-project>.vercel.app/admin/audit-logs`
   - `https://<your-project>.vercel.app/admin/reports`

---

## ၅။ အမေးများသော ပြဿနာများနှင့် ဖြေရှင်းနည်း (Troubleshooting)

| ပြဿနာ | ဖြစ်ရသည့် အကြောင်းရင်း | ဖြေရှင်းနည်း |
|---|---|---|
| **500 Server Error** | `APP_KEY` မထည့်ရသေးခြင်း သို့မဟုတ် DB ချိတ်မရခြင်း | Vercel Settings -> Environment Variables တွင် `APP_KEY` ပြန်စစ်ပါ။ `APP_DEBUG=true` ဖွင့်၍ အသေးစိတ် အမှားကြည့်ပါ။ |
| **Login ဝင်ပြီး ပြန်ထွက်သွားခြင်း** | Serverless ဖိုင်စနစ် ပြောင်းလဲသဖြင့် Session ပျောက်ခြင်း | `SESSION_DRIVER=cookie` သတ်မှတ်ထားခြင်း ရှိ/မရှိ စစ်ဆေးပါ။ |
| **Table doesn't exist** | Cloud DB ပေါ်တွင် Migration မ run ရသေးခြင်း | Local မှနေ၍ Cloud DB သို့ `.env` ပြောင်းချိတ်ပြီး `php artisan migrate --force --seed` run ပါ။ |
| **CSS/JS Styling ပျက်နေခြင်း** | Static Asset path များ မရောက်ရှိခြင်း | `vercel.json` ရှိ `routes` mapping မှန်ကန်မှု ရှိ/မရှိ ပြန်လည်စစ်ဆေးပါ။ |
