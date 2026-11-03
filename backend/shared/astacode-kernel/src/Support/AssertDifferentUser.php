<?php

declare(strict_types=1);

namespace Astacode\Kernel\Support;

use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Class AssertDifferentUser
 * Enforces Segregation of Duties (SoD) per PRD §3.3 & BR-FIN-08.
 * Prevents the creator/requester of a sensitive transaction (e.g., Quotation, Invoice, Journal, Payroll)
 * from self-approving or self-verifying the same record.
 */
final class AssertDifferentUser
{
    /**
     * Asserts that current user is distinct from creator/requester.
     * @throws HttpResponseException (403 Forbidden RFC 7807)
     */
    public static function check(
        int|string|null $creatorId,
        int|string|null $approverId,
        string $operation = 'Persetujuan / Verifikasi'
    ): void {
        if ($creatorId !== null && $approverId !== null && (string) $creatorId === (string) $approverId) {
            throw new HttpResponseException(
                ApiResponse::rfcProblem(
                    title: 'Segregation of Duties Violation (Pemisahan Tugas Wajib)',
                    status: 403,
                    detail: "Operasi '{$operation}' tidak dapat disetujui atau diverifikasi oleh pengguna yang sama dengan pembuat/pemohon transaksi (PRD §3.3 & BR-FIN-08).",
                    type: 'https://astacode.com/errors/segregation-of-duties-violation',
                    extra: [
                        'creator_id' => (string) $creatorId,
                        'approver_id' => (string) $approverId,
                    ]
                )
            );
        }
    }
}

