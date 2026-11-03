<?php

declare(strict_types=1);

namespace App\Services\Media;

class MediaPipelineService
{
    /**
     * Transcribes audio into text (STT) with Indonesian and tech jargon support.
     */
    public function transcribeAudio(string $audioPath, string $language = 'id'): array
    {
        // Mock deterministic / whisper integration
        $sampleText = "Klien PT Maju Mandiri membutuhkan sistem absensi mobile dengan geofencing radius 50 meter dan penggajian terintegrasi PPh 21.";

        return [
            'text' => $sampleText,
            'language' => $language,
            'duration_seconds' => 45.2,
            'segments' => [
                [
                    'start' => 0.0,
                    'end' => 15.0,
                    'speaker' => 'Speaker 1',
                    'text' => 'Klien PT Maju Mandiri membutuhkan sistem absensi mobile dengan geofencing radius 50 meter.',
                ],
                [
                    'start' => 15.1,
                    'end' => 45.2,
                    'speaker' => 'Speaker 2',
                    'text' => 'Dan penggajian terintegrasi PPh 21 serta BPJS Ketenagakerjaan.',
                ],
            ],
        ];
    }

    /**
     * Extracts text from document or image OCR.
     */
    public function extractText(string $filePath, string $mimeType): string
    {
        if (str_contains($mimeType, 'text/')) {
            return file_exists($filePath) ? (string) file_get_contents($filePath) : '';
        }

        // Stub OCR text for receipt / invoice / PDF
        return "INVOICE PEMBAYARAN JASA IT\nTotal: Rp 66.433.500\nTanggal: 03 November 2026\nStatus: LUNAS";
    }

    /**
     * Chunks text into approximate token windows with overlap.
     *
     * @return string[]
     */
    public function chunkText(string $text, int $chunkSizeChars = 1000, int $overlapChars = 100): array
    {
        $chunks = [];
        $length = strlen($text);
        $start = 0;

        while ($start < $length) {
            $chunk = substr($text, $start, $chunkSizeChars);
            $chunks[] = trim($chunk);
            $start += ($chunkSizeChars - $overlapChars);
        }

        return array_values(array_filter($chunks));
    }
}

