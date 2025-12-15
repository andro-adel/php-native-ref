# PHP 8.3 — مرجع سريع مختصر

> المصدر الرسمي: [php.net](https://www.php.net/manual/en/appendices.php) (قسم الـ migration من 8.2 إلى 8.3).

## الميزات الأهم
- **Typed Class Constants**: يمكن تعريف الثوابت بأنواع صريحة داخل الأصناف.
- **Attribute `#[\Override]`**: يتحقق من صحة تجاوز الدوال (يحمي من الأخطاء الإملائية أو التواقيع غير المتطابقة).
- **`json_validate()`**: يتحقق من صحة JSON بسرعة دون فك التشفير الكامل.
- **تحسينات `Random\Randomizer`**: واجهة حديثة للعشوائية الآمنة.
- **نسخ عميق للـ `readonly`**: يسمح بإنشاء نسخ من كائنات readonly دون كسر عدم التغيّر.
- **تحسينات أداء/JIT/Opcache**: أداء أفضل وضبط JIT tracing.
- **تحذيرات تتحول إلى Exceptions**: استمرار تشديد الأخطاء لرفع موثوقية الكود.

## أمثلة كود سريعة

### Typed Class Constants
```php
class Limits {
    public const int PAGE_SIZE = 50; // ثابت بنوع صريح
}
```

### Attribute #[\Override]
```php
interface Repo { public function find(int $id): array; }

class MyRepo implements Repo {
    #[\Override] // يتحقق أن التوقيع يطابق الواجهة
    public function find(int $id): array {
        return ['id' => $id];
    }
}
```

### json_validate()
```php
$raw = '{"name":"A","age":30}';
if (json_validate($raw)) {
    $data = json_decode($raw, true);
}
```

### Random\Randomizer
```php
use Random\Randomizer;
$rng = new Randomizer();
$token = bin2hex($rng->getBytes(16)); // توكن عشوائي آمن
```

### Copy لكائن readonly
```php
readonly class Config {
    public function __construct(public array $data) {}
}
$c1 = new Config(['env' => 'prod']);
$c2 = clone $c1; // نسخ عميق مدعوم في 8.3
```

## نصائح ضبط بيئة (تم تطبيقها في المشروع)
- php.ini: `display_errors=1`, `error_reporting=E_ALL`, تفعيل Opcache + JIT tracing.
- Docker: تثبيت composer داخل الصورة، healthchecks لخدمات DB/Redis، تمكين mod_rewrite.
- كود: Router يدعم كل الأفعال الأساسية، طبقة Request/Response موحدة، CORS/OPTIONS جاهزة.

## مسارات مفيدة في المشروع
- الجذر `/` مقدمة.
- `/users` (GET/POST) و `/users/{id}` (GET/PUT/DELETE) لعرض/إنشاء/تحديث/حذف.
- التكوين والـ Docker في `Dockerfile` و `docker-compose.yml`.
