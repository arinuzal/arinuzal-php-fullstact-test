<?php
require_once __DIR__ . '/config.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3Storage {
    private $s3;
    private $bucket;

    public function __construct() {
        $this->s3 = new S3Client([
            'version'     => 'latest',
            'region'      => AWS_REGION,
            'credentials' => [
                'key'    => AWS_ACCESS_KEY_ID,
                'secret' => AWS_SECRET_ACCESS_KEY,
            ],
        ]);
        $this->bucket = AWS_BUCKET_NAME;
    }

    public function uploadFile($file) {
        if (!$file || !isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            return "no-image.jpg"; 
        }

        $fileName = uniqid() . "-" . basename($file['name']);
        $filePath = $file['tmp_name'];

        try {
            $this->s3->putObject([
                'Bucket'     => $this->bucket,
                'Key'        => $fileName,
                'SourceFile' => $filePath,
                'ACL'        => 'public-read',
            ]);
            return "https://{$this->bucket}.s3." . AWS_REGION . ".amazonaws.com/{$fileName}";
        } catch (AwsException $e) {
            error_log("S3 Upload Error: " . $e->getMessage()); 
            return false;
        }
    }
}