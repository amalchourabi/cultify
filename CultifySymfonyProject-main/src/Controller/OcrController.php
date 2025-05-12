<?php
// src/Controller/OcrController.php

namespace App\Controller;

use App\Service\OcrApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OcrController extends AbstractController
{
    #[Route('/ocr/upload', name: 'ocr_upload', methods: ['GET', 'POST'])]
    public function upload(Request $request, OcrApiService $ocrService): Response
    {
        if ($request->isMethod('POST')) {
            /** @var UploadedFile|null $uploadedFile */
            $uploadedFile = $request->files->get('image');

            // Validate file upload
            if (!$uploadedFile || !$uploadedFile->isValid()) {
                $this->addFlash('error', 'No file uploaded or file is invalid');
                return $this->redirectToRoute('ocr_upload');
            }

            // Validate MIME type
            $allowedMimes = [
                'image/jpeg' => true,
                'image/png' => true,
                'image/jfif' => true,
            ];

            if (!isset($allowedMimes[$uploadedFile->getMimeType()])) {
                $this->addFlash('error', 'Unsupported file type. Please upload a JPG, PNG, or JFIF image.');
                return $this->redirectToRoute('ocr_upload');
            }

            // Process the image
            try {
                $result = $ocrService->extractTextFromImage($uploadedFile);

                if (!$result['success']) {
                    $this->addFlash('error', $result['error'] ?? 'Text extraction failed');
                    return $this->redirectToRoute('ocr_upload');
                }

                return $this->render('ocr/result.html.twig', [
                    'texts' => $result['texts'],
                    'imagePath' => $result['imagePath'],
                ]);

            } catch (\Exception $e) {
                $this->addFlash('error', 'Processing error: ' . $e->getMessage());
                return $this->redirectToRoute('ocr_upload');
            }
        }

        return $this->render('ocr/upload.html.twig');
    }
}