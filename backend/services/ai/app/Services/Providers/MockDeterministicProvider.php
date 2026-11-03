<?php

declare(strict_types=1);

namespace App\Services\Providers;

class MockDeterministicProvider implements AiProviderInterface
{
    private bool $simulateFailure = false;

    public function __construct(bool $simulateFailure = false)
    {
        $this->simulateFailure = $simulateFailure;
    }

    public function setSimulateFailure(bool $fail): void
    {
        $this->simulateFailure = $fail;
    }

    public function getName(): string
    {
        return 'mock';
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function generate(
        string $systemPrompt,
        string $userPrompt,
        ?array $jsonSchema = null,
        array $options = []
    ): array {
        if ($this->simulateFailure) {
            throw new \RuntimeException('Mock provider simulated network/rate-limit failure.');
        }

        $schemaName = $options['schema_name'] ?? 'generic';
        $content = $this->buildMockResponse($schemaName, $userPrompt);

        return [
            'content' => $content,
            'tokens_in' => 350,
            'tokens_out' => 620,
            'cost' => 0.0015,
            'latency_ms' => 45,
            'model' => 'mock-deterministic-v1',
            'provider' => 'mock',
        ];
    }

    private function buildMockResponse(string $schemaName, string $userPrompt): array
    {
        return match ($schemaName) {
            'rab' => [
                'summary' => [
                    'total_mandays' => 84.0,
                    'duration_weeks' => 9.0,
                    'assumptions' => [
                        'Klien menyediakan akses server staging dan dokumentasi API pihak ketiga.',
                        'Tim QA klien aktif melakukan testing selama sprint review.',
                    ],
                    'risks' => [
                        'Potensi keterlambatan bila API payment gateway eksternal belum siap.',
                    ],
                    'clarifying_questions' => [
                        'Apakah sistem membutuhkan integrasi SSO dengan Microsoft Azure AD?',
                    ],
                    'tech_stack' => [
                        'backend' => 'Laravel 12 / PHP 8.3',
                        'frontend' => 'React 18 / TypeScript / Tailwind CSS',
                        'database' => 'MySQL 8.0 & Redis',
                    ],
                ],
                'items' => [
                    [
                        'category' => 'Core Feature',
                        'layer' => 'Backend & Frontend',
                        'name' => 'Autentikasi & RBAC Multi-Peran',
                        'description' => 'Login, 2FA TOTP, session device management, dan matriks permission granular.',
                        'role' => 'Fullstack',
                        'mandays' => 12.0,
                        'complexity' => 'MEDIUM',
                        'confidence' => 0.95,
                        'notes' => 'Menggunakan shared kernel Astacode.',
                    ],
                    [
                        'category' => 'Core Feature',
                        'layer' => 'Mobile',
                        'name' => 'Presensi Absensi Geofencing GPS',
                        'description' => 'Deteksi koordinat GPS radius 50m kantor + anti fake-GPS & selfie liveness.',
                        'role' => 'Mobile',
                        'mandays' => 20.0,
                        'complexity' => 'HIGH',
                        'confidence' => 0.90,
                        'notes' => 'Tervalidasi di proyek serupa sebelumnya.',
                    ],
                    [
                        'category' => 'Payroll',
                        'layer' => 'Backend',
                        'name' => 'Payroll Engine & Generate Slip Gaji PDF',
                        'description' => 'Perhitungan gaji pokok, lembur, BPJS, PPh 21 TER, dan cetak e-slip.',
                        'role' => 'Backend',
                        'mandays' => 28.0,
                        'complexity' => 'HIGH',
                        'confidence' => 0.88,
                        'notes' => 'Perlu verifikasi rumus PPh 21 dari Finance klien.',
                    ],
                    [
                        'category' => 'QA & Testing',
                        'layer' => 'Testing',
                        'name' => 'End-to-End Regression & Load Test',
                        'description' => 'Automated test suite pest/playwright + stress testing 500 CCU.',
                        'role' => 'QA',
                        'mandays' => 24.0,
                        'complexity' => 'MEDIUM',
                        'confidence' => 0.95,
                        'notes' => 'Standar kualitas ASTACODE.',
                    ],
                ],
                'out_of_scope' => [
                    'Pengadaan perangkat hardware fingerprint fisik.',
                    'Migrasi data legacy dari server on-premise klien sebelum tahun 2020.',
                ],
            ],
            'voicenote' => [
                'executive_summary' => 'Klien PT Maju Mandiri membutuhkan sistem absensi mobile dan penggajian terintegrasi.',
                'core_needs' => [
                    'Aplikasi presensi Android/iOS dengan geofence radius 50 meter.',
                    'Perhitungan otomatis payroll bulanan dengan potongan BPJS dan PPh 21.',
                    'Approval cuti berjenjang dari Supervisor ke HR Manager.',
                ],
                'technical_scope' => [
                    'Mobile app (React Native / Flutter)',
                    'Backend API RESTful',
                    'Integrasi payment BCA Virtual Account',
                ],
                'action_items' => [
                    'Sales mengirimkan draf estimasi RAB kasar dalam 2x24 jam.',
                    'Presales menyusun arsitektur sistem dan spesifikasi teknis.',
                ],
            ],
            'wa_reply' => [
                'intent' => 'inquiry_pricing',
                'suggested_reply' => 'Halo Bapak Budi! Terima kasih telah menghubungi ASTACODE. Terkait pengembangan aplikasi enterprise, estimasi awal kami mulai dari 80-120 mandays. Bisakah kami menjadwalkan konsultasi teknis via Google Meet besok jam 14.00 WIB?',
                'sentiment' => 'POSITIVE',
                'followup_needed' => true,
                'urgency' => 'HIGH',
            ],
            'lead_summary' => [
                'company_profile' => 'Perusahaan logistik skala menengah dengan 250 armada armada truk.',
                'estimated_budget' => 200000000,
                'qualification_score' => 85,
                'recommended_next_step' => 'Kirimkan proposal penawaran dan jadwalkan sesi demo produk.',
            ],
            'mou_clause' => [
                'clause_title' => 'Klausul Garansi & Perbaikan Bug Pasca Go-Live',
                'clause_text' => 'Pihak Kedua memberikan masa garansi perbaikan cacat sistem selama 90 (sembilan puluh) hari kalender sejak tanggal Berita Acara Serah Terima (BAST).',
                'risk_level' => 'LOW',
                'deviation_from_standard' => false,
            ],
            'content' => [
                'format' => 'carousel',
                'headlines' => [
                    '5 Alasan Kenapa Software House Butuh ERP Terintegrasi',
                    'Stop Input Ganda antara Sales & Project Management!',
                ],
                'body' => 'Banyak agensi perangkat lunak kehilangan margin profit karena RAB tidak sinkron dengan sprint backlog...',
                'hashtags' => ['#SoftwareHouse', '#ERP', '#AgencyLife', '#TechBusiness'],
                'call_to_action' => 'Kunjungi astacode.id untuk konsultasi gratis sistem operasional software house Anda!',
            ],
            default => [
                'status' => 'success',
                'generated_text' => 'Mock AI generated output based on prompt context.',
                'timestamp' => now()->toIso8601String(),
            ],
        };
    }
}

