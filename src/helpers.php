<?php
// Helpers: response shortcuts, env reader
if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $val = getenv($key);
        if ($val === false) {
            return $default;
        }
        $lower = strtolower($val);
        if (in_array($lower, ['true', '(true)', 'yes', '1'], true)) return true;
        if (in_array($lower, ['false', '(false)', 'no', '0'], true)) return false;
        if (is_numeric($val)) return $val + 0;
        return $val;
    }
}

if (!function_exists('json_response')) {
    /**
     * رد JSON موحد (للتوافق مع الكود القديم)
     */
    function json_response($data, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
