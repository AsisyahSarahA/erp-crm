<?php

namespace Tests\Feature;

use App\Models\NumberingSequence;
use App\Services\NumberingService;
use Tests\TestCase;

class NumberingTest extends TestCase
{
    private NumberingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NumberingService();
    }

    public function test_generates_lampiran_c_formats(): void
    {
        $year = date('Y');
        $month = date('m');

        // Lead: LD-{YYYY}-{NNNN}
        $leadCode = $this->service->next('lead');
        $this->assertMatchesRegularExpression("/^LD-{$year}-\d{4}$/", $leadCode);

        // Quotation: QT/{YYYY}/{MM}/{NNNN}-R{REV}
        $qtCode = $this->service->next('quotation', ['revision' => '1']);
        $this->assertMatchesRegularExpression("/^QT\/{$year}\/{$month}\/\d{4}-R1$/", $qtCode);

        // Invoice: INV/{YYYY}/{MM}/{NNNN}
        $invCode = $this->service->next('invoice');
        $this->assertMatchesRegularExpression("/^INV\/{$year}\/{$month}\/\d{4}$/", $invCode);

        // Project: PRJ-{YYYY}-{NNNN}
        $prjCode = $this->service->next('project');
        $this->assertMatchesRegularExpression("/^PRJ-{$year}-\d{4}$/", $prjCode);

        // Customer: CUS-{NNNN}
        $cusCode = $this->service->next('customer');
        $this->assertMatchesRegularExpression('/^CUS-\d{4}$/', $cusCode);
    }

    public function test_sequence_continuity_without_duplicates(): void
    {
        $generated = [];
        for ($i = 0; $i < 10; $i++) {
            $code = $this->service->next('lead');
            $generated[] = $code;
        }

        // All 10 codes must be unique and sequential
        $this->assertCount(10, array_unique($generated));

        $year = date('Y');
        $this->assertStringEndsWith('-0001', $generated[0]);
        $this->assertStringEndsWith('-0010', $generated[9]);
    }

    public function test_reset_frequency_resets_sequence_counter(): void
    {
        // Simulate a past period
        $seq = NumberingSequence::create([
            'code_type' => 'test_reset',
            'format_pattern' => 'TST-{YYYY}-{NNNN}',
            'current_number' => 999,
            'current_period' => '2025', // Past year
            'reset_frequency' => 'yearly',
            'padding' => 4,
        ]);

        $nextCode = $this->service->next('test_reset');
        $currentYear = date('Y');

        $this->assertEquals("TST-{$currentYear}-0001", $nextCode);
    }
}

