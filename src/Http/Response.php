<?php

namespace App\Http;

/**
 * استجابة HTTP موحدة مع JSON
 */
class Response
{
    public static function json($data, int $status = 200, array $headers = []): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        foreach ($headers as $key => $value) {
            header("{$key}: {$value}");
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
