<?php

namespace App\Services\Cache;

use App\Services\Logger;

class RedisCache
{
    private $client = null;
    private $logger;

    public function __construct()
    {
        $this->logger = Logger::get('cache');
        $host = env('REDIS_HOST', 'redis');
        $port = env('REDIS_PORT', 6379);

        // نحاول استخدام phpredis extension
        if (extension_loaded('redis')) {
            $redis = new \Redis();
            try {
                $redis->connect($host, $port, 2.5);
                $this->client = $redis;
                $this->logger->info("Connected to Redis via phpredis at {$host}:{$port}");
            } catch (\Exception $e) {
                $this->logger->error("Redis connection error: " . $e->getMessage());
            }
        } else {
            // هنا يمكننا fallback إلى Predis (composer) لو أردنا؛ لكن في هذا المشروع نوضح phpredis
            $this->logger->warning("phpredis extension not loaded. Redis cache unavailable.");
        }
    }

    public function set(string $key, $value, int $ttl = 3600)
    {
        if (!$this->client) return false;
        // استخدام setex لضمان TTL
        return $this->client->setex($key, $ttl, serialize($value));
    }

    public function get(string $key)
    {
        if (!$this->client) return null;
        $val = $this->client->get($key);
        return $val === false ? null : unserialize($val);
    }

    public function delete(string $key)
    {
        if (!$this->client) return null;
        return $this->client->del($key);
    }
}
