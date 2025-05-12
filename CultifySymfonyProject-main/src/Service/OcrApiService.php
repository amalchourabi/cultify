<?php
// src/Service/OcrApiService.php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

class OcrApiService
{
    private string $apiUrl;

    public function __construct(string $apiUrl = 'http://localhost:5000/extract-text')
    {
        $this->apiUrl = $apiUrl;
    }

    public function extractTextFromImage(File $imageFile): array
    {
        $client = HttpClient::create();

        try {
            // Ensure the file is valid
            if ($imageFile instanceof UploadedFile && !$imageFile->isValid()) {
                throw new \RuntimeException('Uploaded file is not valid');
            }

            // Prepare file data for multipart request
            $fileData = [
                'name' => 'file', // Field name must match what Flask expects
                'filename' => $imageFile instanceof UploadedFile 
                    ? $imageFile->getClientOriginalName() 
                    : $imageFile->getFilename(),
                'content_type' => $imageFile->getMimeType(),
                'content' => fopen($imageFile->getRealPath(), 'r'),
            ];

            // Send request to Flask API
            $response = $client->request('POST', $this->apiUrl, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'body' => [
                    $fileData, // Send as multipart form-data
                ],
            ]);

            // Handle non-200 responses
            if ($response->getStatusCode() !== 200) {
                return [
                    'success' => false,
                    'error' => 'API request failed with status code: ' . $response->getStatusCode(),
                ];
            }

            // Parse response
            $data = $response->toArray();

            return [
                'success' => $data['success'] ?? false,
                'texts' => $data['texts'] ?? [],
                'imagePath' => $data['image_path'] ?? null,
                'error' => $data['error'] ?? null,
            ];

        } catch (ExceptionInterface $e) {
            return [
                'success' => false,
                'error' => 'API communication error: ' . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Processing error: ' . $e->getMessage(),
            ];
        }
    }
}