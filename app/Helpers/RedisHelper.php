<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

/**
 * Redis Helper for common Redis operations
 * 
 * Redis is now configured for:
 * - Caching (CACHE_STORE=redis)
 * - Sessions (SESSION_DRIVER=redis)  
 * - Queues (QUEUE_CONNECTION=redis)
 */
class RedisHelper
{
    /**
     * Store data in cache with TTL
     * 
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl Time to live in seconds (null = forever)
     * @return bool
     */
    public static function put(string $key, $value, ?int $ttl = 3600): bool
    {
        try {
            if ($ttl === null) {
                Cache::put($key, $value, 3153600); // ~1 year
            } else {
                Cache::put($key, $value, $ttl);
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Redis put error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get data from cache
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        try {
            return Cache::get($key, $default);
        } catch (\Exception $e) {
            \Log::error('Redis get error: ' . $e->getMessage());
            return $default;
        }
    }

    /**
     * Check if key exists in cache
     * 
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        try {
            return Cache::has($key);
        } catch (\Exception $e) {
            \Log::error('Redis has error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Forget (delete) a key from cache
     * 
     * @param string $key
     * @return bool
     */
    public static function forget(string $key): bool
    {
        try {
            Cache::forget($key);
            return true;
        } catch (\Exception $e) {
            \Log::error('Redis forget error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Increment a counter in Redis
     * 
     * @param string $key
     * @param int $value
     * @return int
     */
    public static function increment(string $key, int $value = 1): int
    {
        try {
            return Redis::incr($key);
        } catch (\Exception $e) {
            \Log::error('Redis increment error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Decrement a counter in Redis
     * 
     * @param string $key
     * @param int $value
     * @return int
     */
    public static function decrement(string $key, int $value = 1): int
    {
        try {
            return Redis::decr($key);
        } catch (\Exception $e) {
            \Log::error('Redis decrement error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Push to a Redis list
     * 
     * @param string $key
     * @param mixed $value
     * @return int
     */
    public static function push(string $key, $value): int
    {
        try {
            return Redis::rpush($key, json_encode($value));
        } catch (\Exception $e) {
            \Log::error('Redis push error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Pop from a Redis list
     * 
     * @param string $key
     * @return mixed|null
     */
    public static function pop(string $key)
    {
        try {
            $value = Redis::lpop($key);
            return $value ? json_decode($value, true) : null;
        } catch (\Exception $e) {
            \Log::error('Redis pop error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Add to a Redis set
     * 
     * @param string $key
     * @param array|string $members
     * @return int
     */
    public static function sadd(string $key, $members): int
    {
        try {
            if (is_array($members)) {
                return Redis::sadd($key, ...$members);
            }
            return Redis::sadd($key, $members);
        } catch (\Exception $e) {
            \Log::error('Redis sadd error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all members of a set
     * 
     * @param string $key
     * @return array
     */
    public static function smembers(string $key): array
    {
        try {
            return Redis::smembers($key) ?? [];
        } catch (\Exception $e) {
            \Log::error('Redis smembers error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Store key-value pairs (Redis Hash)
     * 
     * @param string $key
     * @param array $data
     * @return bool
     */
    public static function hset(string $key, array $data): bool
    {
        try {
            foreach ($data as $field => $value) {
                Redis::hset($key, $field, json_encode($value));
            }
            return true;
        } catch (\Exception $e) {
            \Log::error('Redis hset error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get value from Hash
     * 
     * @param string $key
     * @param string $field
     * @return mixed|null
     */
    public static function hget(string $key, string $field)
    {
        try {
            $value = Redis::hget($key, $field);
            return $value ? json_decode($value, true) : null;
        } catch (\Exception $e) {
            \Log::error('Redis hget error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all fields from a Hash
     * 
     * @param string $key
     * @return array
     */
    public static function hgetall(string $key): array
    {
        try {
            $data = Redis::hgetall($key);
            if (!$data) return [];
            
            $result = [];
            foreach ($data as $k => $v) {
                $result[$k] = json_decode($v, true);
            }
            return $result;
        } catch (\Exception $e) {
            \Log::error('Redis hgetall error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Clear all cache
     * 
     * @return bool
     */
    public static function flush(): bool
    {
        try {
            Cache::flush();
            return true;
        } catch (\Exception $e) {
            \Log::error('Redis flush error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Redis connection status
     * 
     * @return bool
     */
    public static function isConnected(): bool
    {
        try {
            Redis::ping();
            return true;
        } catch (\Exception $e) {
            \Log::error('Redis connection error: ' . $e->getMessage());
            return false;
        }
    }
}
