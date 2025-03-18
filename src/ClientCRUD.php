<?php
require_once __DIR__ . '/Db.php';
require_once __DIR__ . '/Redis.php';
require_once __DIR__ . '/S3.php';

class ClientCRUD {
    private $db;
    private $redis;
    private $s3;

    public function __construct() {
        $this->db = new Db();
        $this->redis = new Redis();
        $this->s3 = new S3();
    }

    public function createClient($data, $file) {
        $sql = "INSERT INTO my_client (name, slug, is_project, self_capture, client_prefix, client_logo, address, phone_number, city, created_at, updated_at) 
                VALUES (:name, :slug, :is_project, :self_capture, :client_prefix, :client_logo, :address, :phone_number, :city, NOW(), NOW()) 
                RETURNING id";

        $client_logo = $file ? $this->s3->uploadFile($file) : 'no-image.jpg';
        $data['client_logo'] = $client_logo;

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

        if ($file) {
            $data['client_logo'] = $this->s3->uploadFile($file);
        } else {
            $data['client_logo'] = $client['client_logo'];
        }

        $sql = "UPDATE my_client SET name=:name, is_project=:is_project, self_capture=:self_capture, client_prefix=:client_prefix, 
                client_logo=:client_logo, address=:address, phone_number=:phone_number, city=:city, updated_at=NOW() WHERE slug=:slug";

        $data['slug'] = $slug;
        $updated = $this->db->query($sql, $data);
        if ($updated) {
            $this->redis->delete($slug);
            $this->redis->set($slug, json_encode($data));
            return $data;
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