<?php
namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

class PdfGenerator {
    private $logger;
    
    public function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    
    public function generatePdf(string $html, string $filename): string {
        // Configuration de Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        
        $this->logger->info('Initialisation de Dompdf');
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $this->logger->info('HTML chargé dans Dompdf');
        
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $this->logger->info('PDF rendu avec succès');
        
        // Utiliser DIRECTORY_SEPARATOR pour garantir la compatibilité avec Windows
        $tempDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
        $filePath = $tempDir . DIRECTORY_SEPARATOR . $filename;
        
        $this->logger->info('Chemin du fichier PDF: ' . $filePath);
        
        $output = $dompdf->output();
        $result = file_put_contents($filePath, $output);
        
        if ($result === false) {
            $this->logger->error('Impossible d\'écrire le fichier PDF: ' . $filePath);
            throw new \Exception('Erreur lors de l\'écriture du fichier PDF');
        }
        
        $this->logger->info('PDF enregistré avec succès: ' . $filePath);
        return $filePath;
    }
}