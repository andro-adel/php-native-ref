<?php

namespace App\Http;

/**
 * كائن طلب بسيط يدعم قراءة المسار، الهيدر، والبيانات
 */
class Request
{
    public string $method;
    public string $path;
    public array $headers = [];
    public array $query = [];
    public array $body = [];
    public array $server = [];
    public string $rawBody = '';

    public function __construct(array $server = null)
    {
        $this->server = $server ?? $_SERVER;
        $this->method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
        $this->path = parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $this->headers = $this->parseHeaders();
        $this->query = $_GET;
        $this->body = $this->parseBody();
    }

    protected function parseHeaders(): array
    {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }

    protected function parseBody(): array
    {
        $contentType = $this->headers['Content-Type']
            ?? $this->server['CONTENT_TYPE']
            ?? '';
        $this->rawBody = file_get_contents('php://input') ?: '';

        // حاول دائماً قراءة JSON إذا كان الجسم موجوداً
        if ($this->rawBody !== '') {
            $decoded = json_decode($this->rawBody, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        if (stripos($contentType, 'application/json') !== false) {
            return [];
        }

        if (in_array($this->method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $_POST ?: [];
        }

        return [];
    }
}
