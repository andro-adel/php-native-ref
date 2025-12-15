# ضبط Opcache و JIT (PHP 8.1+ مع مثال 8.3)

- تأكد من تفعيل Opcache في php.ini:
  - `opcache.enable=1`
  - `opcache.enable_cli=1` (عند الحاجة في CLI)
  - `opcache.validate_timestamps=1`
  - `opcache.revalidate_freq=2`
- إعدادات JIT:
  - `opcache.jit_buffer_size=64M`
  - `opcache.jit=tracing`
- تحقق سريع: استدعِ `/examples/opcache` لترى الحالة والذاكرة والإحصاءات (إن كان التفعيل متاحاً في بيئة التشغيل).
- مؤشرات عامة للأداء:
  - زد `opcache.memory_consumption` (مثلاً 256M) للمشروعات الكبيرة.
  - `opcache.max_accelerated_files` يضبط عدد الملفات المسموح تخزينها (مثل 20000).
  - في بيئات تطوير، اترك `validate_timestamps=1` لتحديث الكود سريعاً؛ في الإنتاج يمكن ضبطه إلى 0 مع نشرات متحكمة.
  - JIT يفيد غالباً في الأعمال الحسابية المكثفة؛ في أغلب تطبيقات الويب التحسن محدود لكنه غير ضار عند ضبط ذاكرة معقولة.
