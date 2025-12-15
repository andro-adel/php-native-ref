<?php

namespace App\Domain\Examples;

/**
 * أمثلة مبسطة على مزايا PHP 8.3 ليبقى المشروع مرجعاً سريعاً.
 */
enum Status: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}

class FeatureShowcase
{
    // Typed class constant (جديد 8.3)
    public const int MAX_ITEMS = 100;

    public static function typedConstantExample(): int
    {
        return self::MAX_ITEMS;
    }

    public static function overrideExample(): array
    {
        $repo = new InMemoryRepository();
        return $repo->find(7);
    }

    public static function readonlyCloneExample(): array
    {
        $config = new ConfigSnapshot(['env' => 'dev', 'debug' => true]);
        $cloned = clone $config; // مسموح في 8.3 للـ readonly
        return [$config->data, $cloned->data];
    }

    public static function randomizerExample(): string
    {
        $rng = new \Random\Randomizer();
        return bin2hex($rng->getBytes(12)); // 24 hex chars
    }

    public static function enumExample(): array
    {
        return [
            'active' => Status::ACTIVE->value,
            'inactive' => Status::INACTIVE->value,
        ];
    }

    /**
     * مثال على NumberFormatter من intl (يدعم تنسيقات محلية متعددة).
     */
    public static function intlNumberExample(): array
    {
        $formatter = new \NumberFormatter('ar_EG', \NumberFormatter::CURRENCY);
        $formatted = $formatter->formatCurrency(12345.67, 'EGP');

        $formatterEn = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $formattedEn = $formatterEn->formatCurrency(12345.67, 'USD');

        return [
            'ar_EG' => $formatted,
            'en_US' => $formattedEn,
        ];
    }

    /**
     * مثال Intl لتنسيق التاريخ بحسب locale
     */
    public static function intlDateExample(): array
    {
        $formatterAr = new \IntlDateFormatter('ar_EG', \IntlDateFormatter::FULL, \IntlDateFormatter::MEDIUM, 'Africa/Cairo');
        $formatterEn = new \IntlDateFormatter('en_US', \IntlDateFormatter::FULL, \IntlDateFormatter::MEDIUM, 'America/New_York');

        $ts = strtotime('2025-01-15 18:30:00');

        return [
            'ar_EG' => $formatterAr->format($ts),
            'en_US' => $formatterEn->format($ts),
        ];
    }

    /**
     * مثال Streams باستخدام php://memory لقراءة/كتابة سريعة.
     */
    public static function streamsExample(): array
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, "stream-demo");
        rewind($stream);
        $content = stream_get_contents($stream);
        fclose($stream);

        return [
            'content' => $content,
            'length' => strlen($content),
        ];
    }

    /**
     * Streams مع ملف مؤقت وتصفيته (سطر يحوي كلمة demo فقط)
     */
    public static function streamsFileExample(): array
    {
        $tmp = tmpfile();
        fwrite($tmp, "first line\nsecond demo line\nthird line\n");
        rewind($tmp);

        $matched = [];
        while (($line = fgets($tmp)) !== false) {
            if (str_contains($line, 'demo')) {
                $matched[] = trim($line);
            }
        }
        fclose($tmp);

        return [
            'matched' => $matched,
            'count' => count($matched),
        ];
    }

    /**
     * مثال Fiber (ميزة من 8.1) لإظهار جدولة خفيفة.
     */
    public static function fiberExample(): array
    {
        $fiber = new \Fiber(function (): string {
            // تعليق التنفيذ ثم العودة بقيمة
            \Fiber::suspend('step-1');
            return 'done';
        });

        $first = $fiber->start();
        $second = $fiber->resume();

        return [
            'first' => $first,
            'second' => $second,
            'status' => $fiber->isTerminated() ? 'terminated' : 'running',
        ];
    }

    /**
     * Fiber بخطوتين لتمثيل pipeline
     */
    public static function fiberStepsExample(): array
    {
        $fiber = new \Fiber(function (array $input): array {
            $step1 = strtoupper($input['value']);
            $next = \Fiber::suspend(['stage' => 'upper', 'value' => $step1]);
            $step2 = $next['value'] . '_done';
            return ['stage' => 'done', 'value' => $step2];
        });

        $first = $fiber->start(['value' => 'demo']);
        $second = $fiber->resume(['value' => $first['value'] . '_step']);

        return [
            'first' => $first,
            'second' => $second,
            'status' => $fiber->isTerminated() ? 'terminated' : 'running',
        ];
    }

    /**
     * مثال Attribute مخصص مع Reflection.
     */
    public static function attributeExample(): array
    {
        $class = new \ReflectionClass(DemoWithAttribute::class);
        $attrs = $class->getAttributes(Audit::class);
        $attrData = array_map(fn($a) => $a->getArguments(), $attrs);
        return [
            'class' => $class->getName(),
            'attributes' => $attrData,
        ];
    }

    /**
     * معلومات مختصرة عن Opcache/JIT (في حال التفعيل)
     */
    public static function opcacheInfo(): array
    {
        if (!function_exists('opcache_get_status')) {
            return ['enabled' => false, 'message' => 'Opcache not available'];
        }
        $status = @opcache_get_status(false);
        if ($status === false) {
            return ['enabled' => false, 'message' => 'Opcache disabled'];
        }

        return [
            'enabled' => $status['opcache_enabled'] ?? false,
            'jit' => $status['jit'] ?? null,
            'memory_usage' => $status['memory_usage'] ?? null,
            'statistics' => [
                'hits' => $status['opcache_statistics']['hits'] ?? null,
                'misses' => $status['opcache_statistics']['misses'] ?? null,
                'oom_restarts' => $status['opcache_statistics']['oom_restarts'] ?? null,
            ],
        ];
    }
}

interface RepositoryContract
{
    public function find(int $id): array;
}

class InMemoryRepository implements RepositoryContract
{
    #[\Override] // يتحقق أن التوقيع يطابق العقد (ميزة 8.3)
    public function find(int $id): array
    {
        return [
            'id' => $id,
            'source' => 'in-memory',
            'name' => 'Sample User',
        ];
    }
}

readonly class ConfigSnapshot
{
    public function __construct(public array $data)
    {
    }
}

#[\Attribute(\Attribute::TARGET_CLASS)]
class Audit
{
    public function __construct(public string $by, public string $note)
    {
    }
}

#[Audit(by: 'system', note: 'demo attribute')]
class DemoWithAttribute
{
    public function touch(): bool
    {
        return true;
    }
}
