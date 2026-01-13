<?php

use Illuminate\Support\Facades\Route;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

Route::get('/qr-test', function () {
    $qrCode = new QrCode('https://example.com');
    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    $filePath = storage_path('app/public/qr_test.png');

    if (!file_exists(dirname($filePath))) {
        mkdir(dirname($filePath), 0777, true);
    }

    file_put_contents($filePath, $result->getString());

    return response()->json([
        'message' => 'QR generated!',
        'qr_url' => asset('storage/qr_test.png')
    ]);
});
