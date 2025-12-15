# ملخص سريع للهجرة بين الإصدارات (مرتّب من الأحدث)

المصدر الرسمي: https://www.php.net/manual/en/appendices.php

## 8.4 (ملامح مبكرة متوقعة)
- تحسينات لاحقة على JIT/Opcache.
- صقل إضافي لـ Random و Intl.
- استمرار تحويل التحذيرات إلى استثناءات.

## 8.3
- Typed Class Constants.
- `#[\Override]` للتحقق من صحة تجاوز الدوال.
- `json_validate()` للتحقق السريع من JSON.
- دعم النسخ العميق لكائنات `readonly`.
- تحسينات Random\Randomizer.

## 8.2
- `readonly class`.
- Disjunctive Normal Form types (DNF).
- تعطيل الخصائص الديناميكية افتراضياً.
- `true`, `false`, `null` نوعية مستقلة.

## 8.1
- Enums.
- Fibers (جدولة خفيفة).
- `readonly` properties.
- Intersection types.
- `never` return type.

## 8.0
- JIT (الإصدار الأول).
- Union types.
- Attributes (تعليقات وصفية رسمية).
- Constructor property promotion.
- match expression.
- Nullsafe operator.

## 7.4
- Typed properties.
- Arrow functions.
- Preloading.
- FFI (واجهة استدعاء الدوال الأجنبية).
- تحسينات على spread operator في arrays.

## ملاحظات عامة
- الانتقال بين الإصدارات يتطلب مراجعة قسم BC breaks لكل نسخة (راجع الروابط الرسمية).
- عند الترقية: فعّل `error_reporting=E_ALL` في بيئة اختبار لرصد تحذيرات/استثناءات جديدة.
- استخدم أدوات التحليل الثابت (Psalm/PhpStan) لمساعدة الانتقال، خصوصاً مع التغييرات النوعية.
