<?php
require_once __DIR__ . '/config.php';

class RedisClient {
    private $redis;

    public function __construct() {
        try {
            $this->redis = new \Redis();
            if (!$this->redis->isConnected()) {
                $this->redis->connect(REDIS_HOST, REDIS_PORT);
            }
        } catch (Exception $e) {
            error_log("Redis connection failed: " . $e->getMessage());
            $this->redis = null;
        }
    }

    public function set($key, $value, $ttl = 3600) {
        if (!$this->redis) return false;

        try {
            return $this->redis->setex($key, $ttl, $value); 
        } catch (Exception $e) {
            error_log("Redis set error: " . $e->getMessage());
            return false;
        }
    }

    public function get($key) {
        if (!$this->redis) return null;

        try {
            return $this->redis->get($key);
        } catch (Exception $e) {
            error_log("Redis get error: " . $e->getMessage());
            return null;
        }
    }

    public function delete($key) {
        if (!$this->redis) return false;

        try {
            return $this->redis->del($key) > 0;
        } catch (Exception $e) {
            error_log("Redis delete error: " . $e->getMessage());
            return false;
        }
    }

    public function close() {
        if ($this->redis) {
            $this->redis->close();
        }
    }
}
