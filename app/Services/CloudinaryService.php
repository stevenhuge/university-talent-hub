<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    /**
     * Upload a file to Cloudinary and return the secure URL.
     *
     * @param UploadedFile $file
     * @return string
     * @throws \Exception
     */
    public static function upload(UploadedFile $file): string
    {
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');

        if (!$cloudName || !$apiKey || !$apiSecret) {
            throw new \Exception('Cloudinary credentials are not fully configured in environment variables.');
        }

        $timestamp = time();
        $signature = sha1("timestamp={$timestamp}{$apiSecret}");

        $response = Http::asMultipart()->post("https://api.cloudinary.com/v1_1/{$cloudName}/auto/upload", [
            'file' => fopen($file->getRealPath(), 'r'),
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature,
        ]);

        if ($response->failed()) {
            throw new \Exception('Cloudinary upload failed: ' . $response->body());
        }

        return $response->json('secure_url');
    }
}
