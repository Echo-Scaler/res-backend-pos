# Project Guidelines & Rules

## 1. Documentation in Burmese (မြန်မာဘာသာဖြင့် မှတ်တမ်းပြုစုခြင်း စည်းမျဉ်း)

Whenever any step, milestone, or feature is created or modified for either **Backend** (`backendPos`) or **Frontend** (`frontendPos`):
1. **Always create / update corresponding Markdown (`.md`) documentation** explaining every step in **Burmese (မြန်မာဘာသာ)**.
2. **Explain "Why" (အဘယ်ကြောင့် အသုံးပြုရသနည်း)**:
   - Provide clear reasons why each function, package, container, architecture decision, or feature is chosen.
   - Relate directly to Restaurant POS use cases (e.g. speed, offline resilience, real-time caching, table management, etc.).
3. **Location Structure**:
   - Backend: `backendPos/docs/`
   - Frontend: `frontendPos/docs/`
   - Overall Project / DevOps: `docs/`

---

## 2. Git Branch & Commit Reminder Alert (Git Branch နှင့် Commit Name အမြဲသတိပေးရန် စည်းမျဉ်း)

Whenever **ANY single change, fix, file modification, or feature** is made in the workspace:
1. **Alert the User in the IDE UI**:
   - Always include a prominent visual reminder block (e.g., using GitHub alerts or highlighted banner) reminding the user to check their active **Git Branch** before committing or making further changes.
2. **Provide Suggested Conventional Commit Message**:
   - Provide a clean, descriptive conventional commit name (e.g. `feat(...)`, `fix(...)`, `docs(...)`, `chore(...)`, `refactor(...)`).
3. **Provide Git Commit Command**:
   - Provide the ready-to-run git commit command for the user's convenience:
     ```bash
     git add .
     git commit -m "<suggested_commit_message>"
     ```

---

## 3. Strict Verification & Self-Correction Rule (Test စစ်ဆေးခြင်းနှင့် အမှားပြင်ဆင်ခြင်း စည်းမျဉ်း)

Whenever ANY feature, endpoint, bugfix, or task implementation is finished:
1. **Verify Against Request Specifications & Run Tests**:
   - Check and test whether the implementation matches **every single requirement and issue constraint** requested by the user.
   - Execute relevant automated test suites (e.g. `php artisan test`, `./vendor/bin/pint --test`, frontend test suites) and endpoint checks.
2. **Mandatory Iterative Self-Correction (အမှားတွေ့ပါက အောင်မြင်သည်အထိ ပြန်လည်ပြင်ဆင်ရန်)**:
   - If any test fails, or if the behavior deviates from the prompt's specifications, **do NOT stop or mark the task as done**.
   - Diagnose the root cause, fix the code/tests, and rerun the test suite repeatedly until all checks pass 100%.
3. **Present Verified Results**:
   - Always present the verified test output and execution evidence in the final response.

---

## 4. Mandatory Form Requests & API Resources Rule (Form Request နှင့် Resource ခွဲခြားသတ်မှတ်ခြင်း စည်းမျဉ်း)

Whenever **ANY feature, endpoint, CRUD, or module** is created or modified in `backendPos` (for current and all future features):
1. **Mandatory Form Requests (`app/Http/Requests/*`)**:
   - **NO inline validation**: Never write `$request->validate([...])` directly inside Controllers.
   - All input validation rules, custom error messages, and form authorization logic must be encapsulated in dedicated Form Request classes (e.g. `StoreEmployeeRequest`, `UpdateEmployeeRequest`, `StoreMenuRequest`).
   - Controllers must remain strictly "skinny" (Single Responsibility Principle).
2. **Mandatory API / Json Resources (`app/Http/Resources/*`)**:
   - **NO raw model exposure**: Never return raw Eloquent models directly in API or JSON responses.
   - All output data transformations, currency formatting, and field filtering must be encapsulated in dedicated API Resources (e.g. `EmployeeResource`, `MenuResource`, `OrderResource`).
   - Sensitive columns (such as `password`, `pin_code`, `remember_token`, internal credentials) must NEVER be exposed.

---

## 5. Global Typography & Modern Theme Rule (စာလုံးဖောင့်နှင့် UI အရောင် သတ်မှတ်ချက် စည်းမျဉ်း)

Whenever ANY web UI, Blade view, dashboard, component, or frontend screen is created or styled:
1. **Mandatory Global Font Standard**:
   - Always use **`font-family: "Mada", sans-serif;`** strictly across the entire project (body, headings, buttons, inputs, tables, cards, charts, tooltips, modals).
   - Never use mixed fallback fonts or browser defaults (e.g. Arial, Times, Helvetica) in views or CSS declarations.
2. **Strict Font Weight & Size Scale Standard (တူညီသော စာလုံးအရွယ်အစားနှင့် အလေးချိန် သတ်မှတ်ချက်)**:
   - **Font Weights (အလေးချိန် သတ်မှတ်ချက်)**:
     - `400` (Regular): Paragraphs, descriptions, table body content, general text.
     - `500` (Medium): Dropdown items, form inputs, secondary buttons.
     - `600` (Semi-bold): Card titles, table header columns, section labels, form labels.
     - `700` (Bold): Main headings (H1/H2), KPI metrics, primary action buttons, key badges.
     - *(Avoid arbitrary or unsupported weights like 900)*.
   - **Font Sizes (အရွယ်အစား သတ်မှတ်ချက်)**:
     - **Large KPI Metrics**: `24px - 26px` (`1.5rem - 1.625rem`)
     - **Main Page Headers (H1)**: `22px - 24px` (`1.375rem - 1.5rem`)
     - **Section Headings (H2)**: `18px - 20px` (`1.15rem - 1.25rem`)
     - **Card & Widget Titles (H3/H4)**: `16px - 17px` (`1.0rem - 1.05rem`)
     - **Standard Body & Table Cells**: `14px - 15px` (`0.875rem - 0.9375rem`)
     - **Subtitles, Buttons, Form Inputs**: `13px - 14px` (`0.8125rem - 0.875rem`)
     - **Badges, Tags, Meta & Timestamps**: `11px - 12px` (`0.7rem - 0.75rem`)
3. **Modern Dark Mode Palette (No Murky Green Tint)**:
   - When dark mode (`[data-theme="dark"]`) is active, never use a muddy green color wash for page backgrounds, sidebars, or cards.
   - Use high-end modern dark obsidian / slate surfaces (`--bg-body: #0b0f17`, `--bg-header/sidebar: #111724`, `--bg-card: #161e2e`, `--border-color: #222d42`, `--text-main: #f1f5f9`).
   - The brand primary color (`#9ec63b` lime green) must serve as vibrant accents (badges, icons, active states, chart highlights) against the dark slate surfaces.
