<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Customer;
use Astacode\Kernel\Support\CompanyMatcher;
use Astacode\Kernel\Support\PhoneNormalizer;

final class CustomerDuplicateDetector
{
    /**
     * Check for duplicate customers/contacts by phone (E.164), email, NPWP, or fuzzy company name (>= 85%).
     * Fulfills PRD FR-CRM-01, BR-CRM-09, and AC-CRM-02.
     */
    public function check(array $input, ?int $excludeCustomerId = null, float $fuzzyThreshold = 85.0): array
    {
        $rawPhone = trim((string) ($input['phone_wa'] ?? $input['phone'] ?? ''));
        $normalizedPhone = $rawPhone !== '' ? PhoneNormalizer::toE164($rawPhone) : null;

        $email = isset($input['email']) && trim((string) $input['email']) !== ''
            ? strtolower(trim((string) $input['email']))
            : null;

        $companyName = trim((string) ($input['legal_name'] ?? $input['company_name'] ?? $input['name'] ?? ''));
        $brandName = trim((string) ($input['brand_name'] ?? ''));

        $rawNpwp = trim((string) ($input['npwp'] ?? ''));
        $npwpDigits = $rawNpwp !== '' ? (preg_replace('/[^\d]/', '', $rawNpwp) ?? '') : '';
        $npwpHash = $npwpDigits !== '' ? hash('sha256', $npwpDigits) : null;

        $query = Customer::query()->with('contacts')->where('is_anonymized', false);
        if ($excludeCustomerId !== null) {
            $query->where('id', '!=', $excludeCustomerId);
        }

        $candidates = $query->get();
        $matches = [];

        foreach ($candidates as $customer) {
            $reasons = [];
            $maxScore = 0.0;
            $matchedContacts = [];

            // 1. Check Customer or Contact Phone WA (E.164)
            if ($normalizedPhone !== null) {
                $custPhone = $customer->phone ? PhoneNormalizer::toE164($customer->phone) : null;
                if ($custPhone === $normalizedPhone) {
                    $reasons[] = [
                        'type' => 'PHONE_WA_MATCH',
                        'field' => 'phone',
                        'matched_value' => $normalizedPhone,
                        'score' => 100.0,
                    ];
                    $maxScore = max($maxScore, 100.0);
                }

                foreach ($customer->contacts as $contact) {
                    $cPhone = ($contact->phone_wa ?? $contact->phone)
                        ? PhoneNormalizer::toE164((string) ($contact->phone_wa ?? $contact->phone))
                        : null;
                    if ($cPhone === $normalizedPhone) {
                        $reasons[] = [
                            'type' => 'PHONE_WA_MATCH',
                            'field' => 'contact.phone_wa',
                            'matched_value' => $normalizedPhone,
                            'contact_id' => $contact->id,
                            'contact_name' => $contact->name,
                            'score' => 100.0,
                        ];
                        $matchedContacts[] = [
                            'id' => $contact->id,
                            'name' => $contact->name,
                            'phone_wa' => $cPhone,
                            'email' => $contact->email,
                        ];
                        $maxScore = max($maxScore, 100.0);
                    }
                }
            }

            // 2. Check Customer or Contact Email
            if ($email !== null) {
                if ($customer->email && strtolower(trim($customer->email)) === $email) {
                    $reasons[] = [
                        'type' => 'EMAIL_MATCH',
                        'field' => 'email',
                        'matched_value' => $email,
                        'score' => 100.0,
                    ];
                    $maxScore = max($maxScore, 100.0);
                }

                foreach ($customer->contacts as $contact) {
                    if ($contact->email && strtolower(trim($contact->email)) === $email) {
                        $reasons[] = [
                            'type' => 'EMAIL_MATCH',
                            'field' => 'contact.email',
                            'matched_value' => $email,
                            'contact_id' => $contact->id,
                            'contact_name' => $contact->name,
                            'score' => 100.0,
                        ];
                        $matchedContacts[] = [
                            'id' => $contact->id,
                            'name' => $contact->name,
                            'phone_wa' => $contact->phone_wa ?? $contact->phone,
                            'email' => $contact->email,
                        ];
                        $maxScore = max($maxScore, 100.0);
                    }
                }
            }

            // 3. Check NPWP exact match
            if ($npwpHash !== null) {
                $custNpwpDigits = $customer->npwp ? (preg_replace('/[^\d]/', '', $customer->npwp) ?? '') : '';
                if ($customer->npwp_hash === $npwpHash || ($custNpwpDigits !== '' && $custNpwpDigits === $npwpDigits)) {
                    $reasons[] = [
                        'type' => 'NPWP_MATCH',
                        'field' => 'npwp',
                        'score' => 100.0,
                    ];
                    $maxScore = max($maxScore, 100.0);
                }
            }

            // 4. Check Company Name Fuzzy Similarity >= 85%
            if ($companyName !== '' && strtolower($companyName) !== 'perorangan') {
                $simLegal = CompanyMatcher::similarity($companyName, (string) $customer->legal_name);
                $simBrand = $customer->brand_name
                    ? CompanyMatcher::similarity($companyName, (string) $customer->brand_name)
                    : 0.0;
                $bestSim = max($simLegal, $simBrand);

                if ($bestSim >= $fuzzyThreshold) {
                    $reasons[] = [
                        'type' => 'COMPANY_NAME_FUZZY_MATCH',
                        'field' => $simLegal >= $simBrand ? 'legal_name' : 'brand_name',
                        'matched_value' => $simLegal >= $simBrand ? $customer->legal_name : $customer->brand_name,
                        'similarity_pct' => $bestSim,
                        'score' => $bestSim,
                    ];
                    $maxScore = max($maxScore, $bestSim);
                }
            }

            if ($brandName !== '' && $customer->brand_name) {
                $brandSim = CompanyMatcher::similarity($brandName, (string) $customer->brand_name);
                if ($brandSim >= $fuzzyThreshold && !collect($reasons)->contains('type', 'COMPANY_NAME_FUZZY_MATCH')) {
                    $reasons[] = [
                        'type' => 'COMPANY_NAME_FUZZY_MATCH',
                        'field' => 'brand_name',
                        'matched_value' => $customer->brand_name,
                        'similarity_pct' => $brandSim,
                        'score' => $brandSim,
                    ];
                    $maxScore = max($maxScore, $brandSim);
                }
            }

            if (!empty($reasons)) {
                $matches[] = [
                    'customer_id' => $customer->id,
                    'code' => $customer->code ?? $customer->customer_code,
                    'legal_name' => $customer->legal_name,
                    'brand_name' => $customer->brand_name,
                    'type' => $customer->type,
                    'status' => $customer->status,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'match_score' => round($maxScore, 2),
                    'match_reasons' => $reasons,
                    'matched_contacts' => collect($matchedContacts)->unique('id')->values()->all(),
                ];
            }
        }

        // Sort matches by highest score first
        usort($matches, fn (array $a, array $b) => $b['match_score'] <=> $a['match_score']);

        $hasDuplicates = count($matches) > 0;

        return [
            'has_duplicates' => $hasDuplicates,
            'duplicate_count' => count($matches),
            'dialog_prompt' => $hasDuplicates ? 'Customer sudah ada: Tautkan / Buat baru / Gabung' : null,
            'available_actions' => $hasDuplicates ? ['LINK_EXISTING', 'CREATE_NEW', 'MERGE'] : ['CREATE_NEW'],
            'normalized_input' => [
                'phone_e164' => $normalizedPhone,
                'email' => $email,
                'company_name' => $companyName ?: null,
            ],
            'matches' => $matches,
        ];
    }
}

