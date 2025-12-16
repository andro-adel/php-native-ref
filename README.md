## PHP Native Reference (PHP 8.3, Dockerized)

**هدف المشروع** أن يكون مرجعاً عملياً ومتكاملاً لـ PHP الحديثة (خاصة 8.3) بدون إطار عمل، مع أمثلة كثيرة على الميزات الجديدة، وقاعدة بيانات، و Redis، وكلّه يعمل داخل Docker مباشرة.

### 1. مكوّنات المشروع
- **PHP**: صورة `php:8.3-apache` مع:
  - امتدادات: `pdo`, `pdo_mysql`, `mysqli`, `intl`, `zip`, `opcache`, و `redis` (phpredis).
  - إعدادات مخصصة في `php.ini` (عرض الأخطاء في التطوير، تفعيل Opcache + JIT).
- **خدمات داعمة** (من خلال `docker-compose.yml`):
  - `app`: حاوية تطبيق PHP/Apache.
  - `mysql`: قاعدة بيانات MySQL 8 مع تهيئة أولية من `db/mysql/init.sql`.
  - `mariadb`: قاعدة بيانات MariaDB 11 مع تهيئة من `db/mariadb/init.sql`.
  - `redis`: كاش Redis 7.
  - `phpmyadmin_mysql`, `phpmyadmin_mariadb`: واجهات إدارة قواعد البيانات.

### 2. تشغيل المشروع
#### المتطلبات
- Docker
- Docker Compose

#### خطوات التشغيل
```bash
docker compose up --build -d
```
ثم:
- التطبيق متاح على: `http://localhost:8000`
- phpMyAdmin لـ MySQL على: `http://localhost:8081`
- phpMyAdmin لـ MariaDB على: `http://localhost:8082`

لإيقاف الخدمات:
```bash
docker compose down
```

### 3. هيكل المشروع (Site Map)
- `public/`
  - `index.php`: Front Controller، تعريف جميع المسارات + CORS + تمرير الطلب إلى الـ Router.
- `src/`
  - `Router.php`: راوتر بسيط يدعم `GET/POST/PUT/PATCH/DELETE/OPTIONS/HEAD` مع:
    - دعم بارامترات `{id}`.
    - ردود JSON موحدة للأخطاء (404/405/500).
  - `helpers.php`:
    - `env()` لقراءة المتغيرات البيئية مع تحويل ذكي (`true/false/0/1`...).
    - `json_response()` ردود JSON متوافقة مع الكود القديم.
  - `Http/`
    - `Request.php`: كائن طلب يدعم:
      - `method`, `path`, `headers`, `query`, `body`, `rawBody`.
      - قراءة JSON تلقائياً من `php://input`.
    - `Response.php`: دالة `json()` لردود JSON موحدة.
  - `Controllers/`
    - `UserController.php`: عمليات CRUD على المستخدمين (مع تحقق من البيانات وتعليقات عربية).
    - `ExamplesController.php`: مجموعة كبيرة من المسارات التعليمية لميزات PHP الحديثة.
  - `Models/`
    - `UserModel.php`: تعامل مع جدول `users` باستخدام PDO (MySQL/MariaDB)، يدعم:
      - `all()`, `find()`, `create()`, `update()`, `delete()`.
  - `Services/`
    - `Database.php`: إنشاء اتصالات PDO مع:
      - قراءة من `env()` (host/port/db/user/pass).
      - إعدادات: `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `ATTR_TIMEOUT`, retry بسيط على الإقلاع.
    - `Logger.php`: إعداد Monolog بقنوات متعددة وتسجيل إلى:
      - ملف دوَّار في `/var/log/app/app.log`.
      - `php://stdout` (لـ Docker logs).
    - `Cache/RedisCache.php`: كاش بسيط باستخدام phpredis (`setex/get/del`) مع تعليقات عربية.
  - `Domain/Examples/FeatureShowcase.php`:
    - يحتوي أمثلة عملية لمعظم الميزات الحديثة (موضحة بالأسفل).
- `db/`
  - `mysql/init.sql`, `mariadb/init.sql`: إنشاء جدول `users` + بيانات تجريبية.
- `docs/`
  - `php83-cheatsheet.md`: مرجع سريع لأهم ما في PHP 8.3 مع أمثلة.
  - `php-opcache-jit.md`: ملاحظات ضبط Opcache/JIT.
  - `php-migration-notes.md`: ملخص الهجرة من 7.4 → 8.4 (مع الإشارة لـ php.net).
  - `api-collection.json`: مجموعة جاهزة لاستيرادها في Postman/Insomnia.

### 4. المسارات (APIs)
#### عامة
- `GET /`
  صفحة ترحيب بسيطة.

#### مستخدمين (CRUD)
- `GET /users`
  يعيد قائمة المستخدمين من MySQL و MariaDB (مع كاش Redis).
- `GET /users/{id}`
  يعيد مستخدم واحد من MySQL.
- `POST /users`
  إنشاء مستخدم جديد (JSON body: `{ "name": "...", "email": "..." }`).
  - يتحقق من الحقول + صحة البريد.
- `PUT /users/{id}`
  تحديث مستخدم (يمكن إرسال `name` و/أو `email`).
- `DELETE /users/{id}`
  حذف مستخدم.

#### أمثلة PHP 8.x / 8.3
كلها موجودة في `ExamplesController` + `FeatureShowcase`:

- `GET /examples/features`
  يعيد JSON كبير يحتوي كل الأمثلة التالية في رد واحد:
  - **Typed class constant**.
  - **Enum** (Status).
  - **#[\Override]** مع Repository.
  - **Readonly + clone**.
  - **Random\Randomizer** (توكن عشوائي).
  - **json_validate()** (إن كانت متاحة في البيئة) مع Fallback إلى `json_decode`.
  - **Intl NumberFormatter** (عملات بلغتين).
  - **Intl DateFormatter** (تواريخ بلغتين).
  - **Streams** (`php://memory` + ملفات مؤقتة مع فلترة سطور).
  - **Fibers** (مثال بسيط + pipeline من مرحلتين).
  - **Attributes + Reflection**.
  - **Opcache/JIT status** (إن كانت الدوال متاحة).

- مسارات منفصلة لكل مثال (مناسبة للتجربة في Postman):
  - `POST /examples/json-validate`
    يتحقق من صحة JSON في الـ body (سريع باستخدام `json_validate` أو `json_decode`).
  - `GET /examples/random`
    يولّد توكن عشوائي آمن باستخدام `Random\Randomizer`.
  - `GET /examples/enum`
    يعرض قيم Enum `Status`.
  - `GET /examples/intl`
    تنسيق أرقام/عملات بـ `NumberFormatter` للغات مختلفة.
  - `GET /examples/intl-date`
    تنسيق تواريخ بـ `IntlDateFormatter` بأكثر من Locale.
  - `GET /examples/streams`
    مثال على `php://memory`.
  - `GET /examples/streams-file`
    مثال على التعامل مع ملف مؤقت وتصفيته بأسلوب Streams.
  - `GET /examples/fiber`
    Fiber بسيط يعلّق التنفيذ ويستأنف بقيمة.
  - `GET /examples/fiber-steps`
    Fiber يمثل pipeline (upper → append → done).
  - `GET /examples/attribute`
    Attribute مخصص + Reflection للحصول على بيانات الـ Attribute.
  - `GET /examples/opcache`
    معلومات Opcache/JIT (مفعّل/غير مفعّل، إحصاءات hits/misses، استخدام الذاكرة).

### 5. تجربة الـ APIs عبر Postman/Insomnia
1. افتح Postman أو Insomnia.
2. اختر Import.
3. استورد الملف: `docs/api-collection.json`.
4. تأكد أن Docker شغّال:
   ```bash
   docker compose up --build -d
   ```
5. جرّب الطلبات واحداً تلو الآخر (Users, Examples, …).

### 6. ملاحظات حول PHP 8.3 والمرجع الرسمي
- هذا المشروع لا يغطي *كل* ما في PHP.net، لكنه يغطي معظم الميزات الهامة من:
  - 8.0 (JIT, attributes, union types, match، nullsafe).
  - 8.1 (enums, fibers, readonly, intersection types).
  - 8.2 (readonly classes, DNF types، منع الخصائص الديناميكية افتراضياً).
  - 8.3 (typed constants, `#[\Override]`, `json_validate`, deep readonly clone، تحسينات Random).
- للمزيد من التفاصيل الرسمية، راجع:
  - `https://www.php.net/releases/`
  - `https://www.php.net/manual/en/appendices.php`
  - أقسام *Migrating from X to Y* لكل إصدار.

### 7. ملاحظات تطويرية
- الكود مكتوب بحيث يكون بسيطاً وواضحاً كمثال تعليمي، مع **تعليقات عربية** في الأماكن الحساسة.
- Logger يستخدم Monolog فقط، ويمكنك توسعته بسهولة.
- Router ليس بديلاً لإطار عمل، لكنه كافٍ لفهم المفاهيم الأساسية (Routes, Controllers, Request/Response).
- إذا أردت إضافة مزيد من الأمثلة (FFI, Preloading, match/union/intersection المتقدمة…) يمكنك إنشاء Controller/Domain جديد بنفس النمط واستدعائه من `index.php`.
