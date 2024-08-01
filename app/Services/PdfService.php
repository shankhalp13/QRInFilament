<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
// use Mpdf\Mpdf;

class PdfService
{
    // public function generatePdfWithQrCode($recordId)
    // {
    //     $qrCodeImage = QrCode::format('png')->size(256)->generate('Item-' . $recordId);
    //     $encodedQrCodeImage = base64_encode($qrCodeImage);

    //     $options = new Options();
    //     $options->set('defaultFont', 'Arial');
    //     $dompdf = new Dompdf($options);

    //     $html = '<html><body>';
    //     $html .= '<h1>QR Code for Item-' . htmlspecialchars($recordId, ENT_QUOTES, 'UTF-8') . '</h1>';
    //     $html .= '<img src="data:image/png;base64,' . htmlspecialchars($encodedQrCodeImage, ENT_QUOTES, 'UTF-8') . '">';
    //     $html .= '</body></html>';


    //     $dompdf->loadHtml($html);
    //     $dompdf->setPaper('A4', 'portrait');
    //     $dompdf->render();

    //     $pdfContent = $dompdf->output();
    //     return $pdfContent;
    //     // dd($pdfContent);

    //     // Return PDF as a direct response
    //     // return response($pdfContent, 200)
    //     //     ->header('Content-Type', 'application/pdf')
    //     //     ->header('Content-Disposition', 'attachment; filename="qr-code-item-' . $recordId . '.pdf"');
    // }

    public function generatePdfWithQrCode($recordId)
    {
        // Generate QR code image in PNG format
        $qrCodeImage = QrCode::format('png')->size(512)->generate('Item-' . $recordId);

        // Encode QR code image to base64
        $encodedQrCodeImage = base64_encode($qrCodeImage);

        // Initialize Dompdf with options
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);

        // Generate HTML content for the PDF
        $html = '<html><body>';
        $html .= '<center><h1>QR Code for Item-' . htmlspecialchars($recordId, ENT_QUOTES, 'UTF-8') . '</h1></center><br>';
        $html .= '<center><img style="width=100px;" src="data:image/png;base64,' . htmlspecialchars($encodedQrCodeImage, ENT_QUOTES, 'UTF-8') . '"></center>';
        $html .= '</body></html>';

        // Log the HTML content for debugging
        Log::info('HTML Content:', ['html' => $html]);

        // Load HTML content into Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        // Output PDF as a string
        return $dompdf->output();
    }
}
