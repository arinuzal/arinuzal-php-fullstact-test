<?php
require_once __DIR__ . '/Db.php';
require_once __DIR__ . '/Redis.php';
require_once __DIR__ . '/S3.php';

class ClientCRUD {
    private $db;
    private $redis;
    private $s3;

    public function __construct() {
        $this->db = new Database();
        $this->redis = new RedisClient();
        $this->s3 = new S3Storage();
    }

    public function createClient($data, $file) {
        $client_logo = $file ? $this->s3->uploadFile($file) : 'no-image.jpg';
        $data['client_logo'] = $client_logo;

        $sql = "INSERT INTO my_client (name, slug, is_project, self_capture, client_prefix, client_logo, address, phone_number, city, created_at, updated_at) 
                VALUES (:name, :slug, :is_project, :self_capture, :client_prefix, :client_logo, :address, :phone_number, :city, NOW(), NOW()) 
                RETURNING id";

        $stmt = $this->db->query($sql, $data);
        if ($stmt) {
            $data['id'] = $stmt->fetchColumn();
            $this->redis->set($data['slug'], json_encode($data));
            return $data;
        }
        return false;
    }

    public function getClient($slug) {
        $cachedData = $this->redis->get($slug);
        if ($cachedData) {
            return json_decode($cachedData, true);
        }

        $sql = "SELECT * FROM my_client WHERE slug = :slug AND deleted_at IS NULL";
        $client = $this->db->query($sql, ['slug' => $slug])->fetch(PDO::FETCH_ASSOC);
        if ($client) {
            $this->redis->set($slug, json_encode($client));
        }
        return $client;
    }

    public function updateClient($slug, $data, $file) {
        $client = $this->getClient($slug);
        if (!$client) return false;

        $data = array_merge($client, $data);

        if ($file) {
            $data['client_logo'] = $this->s3->uploadFile($file);
        }

        $sql = "UPDATE my_client SET name=:name, is_project=:is_project, self_capture=:self_capture, client_prefix=:client_prefix, 
                client_logo=:client_logo, address=:address, phone_number=:phone_number, city=:city, updated_at=NOW() WHERE slug=:slug";

        $updated = $this->db->query($sql, $data);
        if ($updated) {
            $newClient = $this->getClient($slug);
            $this->redis->delete($slug);
            $this->redis->set($slug, json_encode($newClient));
            return $newClient;
        }
        return false;
    }

    public function deleteClient($slug) {
        $sql = "UPDATE my_client SET deleted_at = NOW() WHERE slug = :slug";
        $deleted = $this->db->query($sql, ['slug' => $slug]);
        if ($deleted) {
            $this->redis->delete($slug);
            return true;
        }
        return false;
    }
}