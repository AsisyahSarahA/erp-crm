<?php

declare(strict_types=1);

namespace Astacode\Kernel\Tests\Unit;

use Astacode\Kernel\Document\DocumentHasher;
use PHPUnit\Framework\TestCase;

class DocumentHasherTest extends TestCase
{
    public function test_hash_generates_valid_sha256(): void
    {
        $content = 'ASTACODE QUOTATION QT-2026-11-0001';
        $expected = hash('sha256', $content);

        $hash = DocumentHasher::hash($content);

        $this->assertSame($expected, $hash);
        $this->assertSame(64, strlen($hash));
    }

    public function test_verify_checks_content_integrity(): void
    {
        $content = 'Klausul MoU Pasal 1: Ruang Lingkup Pekerjaan';
        $hash = DocumentHasher::hash($content);

        $this->assertTrue(DocumentHasher::verify($content, $hash));
        $this->assertFalse(DocumentHasher::verify($content . ' (tampered)', $hash));
    }

    public function test_format_fingerprint(): void
    {
        $hash = 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855';
        $fingerprint = DocumentHasher::formatFingerprint($hash);

        $this->assertStringStartsWith('SHA256:E3B0C442-98FC1C14...', $fingerprint);
        $this->assertStringEndsWith('7852B855', $fingerprint);
    }

    public function test_hash_file(): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'asta_doc_');
        file_put_contents($tmp, 'Dokumen MoU Final Ber-TTD');

        $hash = DocumentHasher::hashFile($tmp);
        $this->assertSame(hash_file('sha256', $tmp), $hash);

        unlink($tmp);
    }
}

