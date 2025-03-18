<?php
require_once __DIR__ . '/config.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class S3 {
    private $s3;

    public function __construct() {
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region'  => AWS_REGION,
            'credentials' => [
                'key'    => AWS_ACCESS_KEY_ID,
                'secret' => AWS_SECRET_ACCESS_KEY,
            ],
        ]);
    }

    public function uploadFile($file) {
        $fileName = uniqid() . "-" . basename($file['name']);
        $filePath = $file['tmp_name'];

        try {
            $this->s3->putObject([
                'Bucket' => AWS_BUCKET_NAME,
                'Key'    => $fileName,
                'SourceFile' => $filePath,
                'ACL'    => 'public-read',
            ]);
            return "https://" . AWS_BUCKET_NAME . ".s3." . AWS_REGION . ".amazonaws.com/" . $fileName;
        } catch (AwsException $e) {
            return "no-image.jpg";
        }
    }
}