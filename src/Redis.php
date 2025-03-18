<?php
require_once __DIR__ . '/config.php';

class Redis {
    private $redis;

    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect(REDIS_HOST, REDIS_PORT);
    }

    public function set($key, $value) {
        $this->redis->set($key, $value);
    }

    public function get($key) {
        return $this->redis->get($key);
    }

    public function delete($key) {
        $this->redis->del($key);
    }
}