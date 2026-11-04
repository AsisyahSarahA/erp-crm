<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Customer360Resource;
use App\Http\Resources\CustomerResource;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\CustomerAttribution;
use App\Models\CustomFieldDefinition;
use App\Models\CustomFieldValue;
use App\Models\LeadSource;
use App\Policies\CustomerPolicy;
use App\Services\CustomerDuplicateDetector;
use App\Services\CustomerMerger;
use App\Services\CustomerPdpAnonymizer;
use App\Support\UserContext;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Astacode\Kernel\Services\AuditClient;
use Astacode\Kernel\Support\PhoneNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerDuplicateDetector $duplicateDetector = new CustomerDuplicateDetector(),
        private readonly CustomerMerger $customerMerger = new CustomerMerger(),
        private readonly CustomerPdpAnonymizer $pdpAnonymizer = new CustomerPdpAnonymizer(),
        private readonly CustomerPolicy $policy = new CustomerPolicy(),
        private readonly AuditClient $auditClient = new AuditClient()
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);

        $query = Customer::query()
            ->with(['primaryContact', 'firstSource', 'latestSource'])
            ->accessibleBy($userContext);

        // Search
        $search = trim((string) ($request->query('search') ?? $request->query('q') ?? ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('legal_name', 'like', "%{$search}%")
                    ->orWhere('brand_name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('customer_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filters
        $filters = (array) $request->query('filter', []);
        $statusFilter = $filters['status'] ?? $request->query('status');
        if (!empty($statusFilter)) {
            $query->where('status', strtolower((string) $statusFilter));
        }

        $typeFilter = $filters['type'] ?? $request->query('type');
        if (!empty($typeFilter)) {
            $query->where('type', strtolower((string) $typeFilter));
        }

        $industryFilter = $filters['industry'] ?? $request->query('industry');
        if (!empty($industryFilter)) {
            $query->where('industry', (string) $industryFilter);
        }

        $ownerFilter = $filters['owner_id'] ?? $request->query('owner_id');
        if ($ownerFilter !== null && $ownerFilter !== '') {
            $query->where('owner_id', (int) $ownerFilter);
        }

        // Sorting
        $sortParam = (string) $request->query('sort', '-created_at');
        $allowedSorts = ['id', 'code', 'legal_name', 'status', 'health_score', 'created_at', 'updated_at'];
        foreach (explode(',', $sortParam) as $sortCol) {
            $sortCol = trim($sortCol);
            if ($sortCol === '') {
                continue;
            }
            $dir = str_starts_with($sortCol, '-') ? 'desc' : 'asc';
            $colName = ltrim($sortCol, '-+');
            if (in_array($colName, $allowedSorts, true)) {
                $query->orderBy($colName, $dir);
            }
        }

        $perPage = min(100, max(1, (int) $request->query('per_page', 15)));
        $paginator = $query->paginate($perPage);

        return ApiResponse::paginated(
            CustomerResource::collection($paginator->getCollection())->resolve($request),
            $paginator->currentPage(),
            $paginator->perPage(),
            $paginator->total()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);

        $validator = Validator::make($request->all(), [
            'legal_name' => ['required', 'string', 'min:2', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'in:perusahaan,perorangan,instansi,startup,umkm,PERUSAHAAN,PERORANGAN,INSTANSI,STARTUP,UMKM'],
            'industry' => ['nullable', 'string', 'max:64'],
            'size' => ['nullable', 'string', 'max:64'],
            'website' => ['nullable', 'string', 'max:255'],
            'npwp' => ['nullable', 'string', function (string $attribute, mixed $value, \Closure $fail) {
                $digits = preg_replace('/[^\d]/', '', (string) $value) ?? '';
                if ($digits !== '' && !in_array(strlen($digits), [15, 16], true)) {
                    $fail('NPWP harus terdiri dari 15 atau 16 digit angka.');
                }
            }],
            'nib' => ['nullable', 'string', 'max:64'],
            'is_pkp' => ['nullable', 'boolean'],
            'is_pph23_withholder' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string', 'in:prospect,active,inactive,churned,blacklist,PROSPECT,ACTIVE,INACTIVE,CHURNED,BLACKLIST'],
            'phone' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'billing_address' => ['nullable'],
            'office_address' => ['nullable'],
            'owner_id' => ['nullable', 'integer'],
            'team_id' => ['nullable', 'integer'],
            'first_source_id' => ['nullable', 'integer'],
            'latest_source_id' => ['nullable', 'integer'],
            'source_code' => ['nullable', 'string', 'max:64'],
            'utm' => ['nullable', 'array'],
            'contacts' => ['nullable', 'array'],
            'contacts.*.name' => ['required_with:contacts', 'string', 'min:2', 'max:255'],
            'contacts.*.role' => ['nullable', 'string', 'in:decision_maker,technical_lead,finance_lead,influencer'],
            'contacts.*.title' => ['nullable', 'string', 'max:128'],
            'contacts.*.email' => ['nullable', 'email', 'max:255'],
            'contacts.*.phone_wa' => ['nullable', 'string', 'max:32'],
            'contacts.*.phone' => ['nullable', 'string', 'max:32'],
            'contacts.*.is_primary' => ['nullable', 'boolean'],
            'contacts.*.consent_comm' => ['nullable', 'boolean'],
            'contacts.*.preferred_channel' => ['nullable', 'string', 'in:whatsapp,email,phone,meet'],
            'custom_fields' => ['nullable', 'array'],
            'duplicate_resolution' => ['nullable', 'string', 'in:CHECK,LINK_EXISTING,CREATE_NEW,MERGE'],
            'force_create' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: 'Data customer tidak valid.',
                invalidParams: $validator->errors()->toArray()
            );
        }

        $data = $validator->validated();
        $primaryContactInput = $data['contacts'][0] ?? [];
        $checkPayload = [
            'legal_name' => $data['legal_name'],
            'brand_name' => $data['brand_name'] ?? null,
            'phone_wa' => $data['phone'] ?? $primaryContactInput['phone_wa'] ?? $primaryContactInput['phone'] ?? null,
            'email' => $data['email'] ?? $primaryContactInput['email'] ?? null,
            'npwp' => $data['npwp'] ?? null,
        ];

        $resolution = strtoupper((string) ($data['duplicate_resolution'] ?? ($request->boolean('force_create') ? 'CREATE_NEW' : 'CHECK')));
        $dupCheck = $this->duplicateDetector->check($checkPayload);

        if ($dupCheck['has_duplicates'] && $resolution !== 'CREATE_NEW') {
            $topMatchId = $dupCheck['matches'][0]['customer_id'];
            $existingCustomer = Customer::with(['contacts', 'firstSource', 'latestSource'])->find($topMatchId);

            if ($resolution === 'LINK_EXISTING' && $existingCustomer) {
                return ApiResponse::success(
                    (new CustomerResource($existingCustomer))->resolve($request),
                    ['resolution' => 'LINKED_EXISTING', 'duplicate_check' => $dupCheck],
                    200
                );
            }

            if ($resolution === 'MERGE' && $existingCustomer) {
                $tempCustomer = $this->createCustomerRecord($data, $userContext);
                $mergeHistory = $this->customerMerger->merge(
                    $existingCustomer,
                    $tempCustomer,
                    $userContext['id'] ?? null,
                    'Auto-merge saat input customer/lead (AC-CRM-02)'
                );
                $existingCustomer->refresh()->load(['contacts', 'firstSource', 'latestSource']);

                return ApiResponse::success(
                    (new CustomerResource($existingCustomer))->resolve($request),
                    [
                        'resolution' => 'MERGED',
                        'merge_history_id' => $mergeHistory->id,
                    ],
                    200
                );
            }

            // Default: return 409 Conflict with duplicate dialog options (AC-CRM-02)
            return ApiResponse::rfcProblem(
                title: 'Duplicate Customer Detected',
                status: 409,
                detail: 'Customer sudah ada: Tautkan / Buat baru / Gabung',
                type: 'https://astacode.com/errors/duplicate-customer',
                extra: [
                    'duplicate_check' => $dupCheck,
                ]
            );
        }

        $customer = $this->createCustomerRecord($data, $userContext);
        $customer->load(['contacts', 'primaryContact', 'firstSource', 'latestSource']);

        return ApiResponse::success(
            (new CustomerResource($customer))->resolve($request),
            ['message' => 'Customer berhasil dibuat.'],
            201
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $customer = Customer::with(['contacts', 'primaryContact', 'firstSource', 'latestSource'])->find($id);

        if (!$customer) {
            return ApiResponse::rfcProblem('Customer Not Found', 404, "Customer #{$id} tidak ditemukan.");
        }

        if (!$this->policy->view($userContext, $customer)) {
            return ApiResponse::rfcProblem(
                title: 'Forbidden',
                status: 403,
                detail: 'Anda hanya memiliki akses ke data customer milik Anda sendiri atau tim Anda.'
            );
        }

        return ApiResponse::success((new CustomerResource($customer))->resolve($request));
    }

    public function show360(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $customer = Customer::with([
            'contacts',
            'primaryContact',
            'firstSource',
            'latestSource',
            'attributions.source',
            'leads.quotations',
            'quotations',
            'contracts',
            'customFieldValues.definition',
            'mergeHistories',
        ])->find($id);

        if (!$customer) {
            return ApiResponse::rfcProblem('Customer Not Found', 404, "Customer #{$id} tidak ditemukan.");
        }

        if (!$this->policy->view($userContext, $customer)) {
            return ApiResponse::rfcProblem(
                title: 'Forbidden',
                status: 403,
                detail: 'Anda hanya memiliki akses ke Customer 360 milik Anda sendiri atau tim Anda.'
            );
        }

        if ($customer->health_breakdown === null) {
            $customer->calculateHealthScore();
            $customer->saveQuietly();
        }

        return ApiResponse::success((new Customer360Resource($customer))->resolve($request));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $customer = Customer::with('contacts')->find($id);

        if (!$customer) {
            return ApiResponse::rfcProblem('Customer Not Found', 404, "Customer #{$id} tidak ditemukan.");
        }

        if (!$this->policy->update($userContext, $customer)) {
            return ApiResponse::rfcProblem(
                title: 'Forbidden',
                status: 403,
                detail: 'Anda tidak memiliki izin untuk mengubah customer ini.'
            );
        }

        $validator = Validator::make($request->all(), [
            'legal_name' => ['sometimes', 'string', 'min:2', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'in:perusahaan,perorangan,instansi,startup,umkm,PERUSAHAAN,PERORANGAN,INSTANSI,STARTUP,UMKM'],
            'industry' => ['nullable', 'string', 'max:64'],
            'size' => ['nullable', 'string', 'max:64'],
            'website' => ['nullable', 'string', 'max:255'],
            'npwp' => ['nullable', 'string', function (string $attribute, mixed $value, \Closure $fail) {
                $digits = preg_replace('/[^\d]/', '', (string) $value) ?? '';
                if ($digits !== '' && !in_array(strlen($digits), [15, 16], true)) {
                    $fail('NPWP harus terdiri dari 15 atau 16 digit angka.');
                }
            }],
            'nib' => ['nullable', 'string', 'max:64'],
            'is_pkp' => ['nullable', 'boolean'],
            'is_pph23_withholder' => ['nullable', 'boolean'],
            'status' => ['sometimes', 'string', 'in:prospect,active,inactive,churned,blacklist,PROSPECT,ACTIVE,INACTIVE,CHURNED,BLACKLIST'],
            'phone' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'billing_address' => ['nullable'],
            'office_address' => ['nullable'],
            'account_plan' => ['nullable', 'array'],
            'financial_snapshot' => ['nullable', 'array'],
            'external_snapshots' => ['nullable', 'array'],
            'nps_score' => ['nullable', 'integer', 'min:-100', 'max:100'],
            'csat_score' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'owner_id' => ['nullable', 'integer'],
            'team_id' => ['nullable', 'integer'],
            'custom_fields' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: 'Data perubahan customer tidak valid.',
                invalidParams: $validator->errors()->toArray()
            );
        }

        $validated = $validator->validated();
        $customFields = $validated['custom_fields'] ?? null;
        unset($validated['custom_fields']);

        $customer->fill($validated);
        $customer->calculateHealthScore();
        $customer->save();

        if (is_array($customFields)) {
            $this->syncCustomFields($customer, $customFields);
        }

        $customer->load(['contacts', 'primaryContact', 'firstSource', 'latestSource']);

        return ApiResponse::success((new CustomerResource($customer))->resolve($request));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $customer = Customer::find($id);

        if (!$customer) {
            return ApiResponse::rfcProblem('Customer Not Found', 404, "Customer #{$id} tidak ditemukan.");
        }

        if (!$this->policy->delete($userContext, $customer)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Anda tidak memiliki izin menghapus customer.');
        }

        $customer->delete();

        return ApiResponse::success(['deleted' => true, 'id' => $id]);
    }

    public function duplicatesCheck(Request $request): JsonResponse
    {
        $excludeId = $request->input('exclude_customer_id') ? (int) $request->input('exclude_customer_id') : null;
        $threshold = (float) $request->input('threshold', 85.0);

        $result = $this->duplicateDetector->check($request->all(), $excludeId, $threshold);

        return ApiResponse::success($result);
    }

    public function merge(Request $request, ?int $id = null): JsonResponse
    {
        $userContext = UserContext::resolve($request);

        $masterId = $id ?? (int) $request->input('master_customer_id');
        $sourceId = (int) $request->input('source_customer_id');

        if (!$masterId || !$sourceId) {
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: 'master_customer_id dan source_customer_id wajib diisi.'
            );
        }

        $masterCustomer = Customer::find($masterId);
        $sourceCustomer = Customer::find($sourceId);

        if (!$masterCustomer || !$sourceCustomer) {
            return ApiResponse::rfcProblem('Customer Not Found', 404, 'Master atau source customer tidak ditemukan.');
        }

        if (!$this->policy->merge($userContext, $masterCustomer)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Anda tidak memiliki izin menggabungkan customer ini.');
        }

        try {
            $history = $this->customerMerger->merge(
                $masterCustomer,
                $sourceCustomer,
                $userContext['id'] ?? null,
                $request->input('reason')
            );
        } catch (InvalidArgumentException $e) {
            return ApiResponse::rfcProblem('Invalid Merge Operation', 422, $e->getMessage());
        }

        $masterCustomer->refresh()->load(['contacts', 'primaryContact', 'firstSource', 'latestSource', 'mergeHistories']);

        return ApiResponse::success([
            'master_customer' => (new CustomerResource($masterCustomer))->resolve($request),
            'merge_history' => [
                'id' => $history->id,
                'master_customer_id' => $history->master_customer_id,
                'merged_customer_id' => $history->merged_customer_id,
                'merged_customer_code' => $history->merged_customer_code,
                'migrated_records_summary' => $history->migrated_records_summary,
                'reason' => $history->reason,
                'merged_at' => $history->merged_at?->toIso8601String(),
            ],
        ]);
    }

    public function verifyNpwp(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $customer = Customer::find($id);

        if (!$customer) {
            return ApiResponse::rfcProblem('Customer Not Found', 404, "Customer #{$id} tidak ditemukan.");
        }

        if (!$this->policy->verifyNpwp($userContext, $customer)) {
            return ApiResponse::rfcProblem(
                title: 'Forbidden',
                status: 403,
                detail: 'Hanya peran Finance / Finance AR Specialist yang berwenang memverifikasi NPWP customer.'
            );
        }

        if ($request->filled('npwp')) {
            $customer->npwp = (string) $request->input('npwp');
        }

        $digits = preg_replace('/[^\d]/', '', (string) $customer->npwp) ?? '';
        if (!in_array(strlen($digits), [15, 16], true)) {
            return ApiResponse::rfcProblem(
                title: 'Invalid NPWP',
                status: 422,
                detail: 'Customer belum memiliki NPWP valid (15/16 digit) untuk diverifikasi.'
            );
        }

        if ($request->has('is_pkp')) {
            $customer->is_pkp = $request->boolean('is_pkp');
        }
        if ($request->has('is_pph23_withholder')) {
            $customer->is_pph23_withholder = $request->boolean('is_pph23_withholder');
        }

        $customer->npwp_verified_by = $userContext['id'] ?? 1;
        $customer->npwp_verified_at = now();
        $customer->save();

        $this->auditClient->log(
            action: 'customer.npwp_verified',
            entityType: 'customer',
            entityId: (string) $customer->id,
            oldValues: ['is_npwp_verified' => false],
            newValues: [
                'is_npwp_verified' => true,
                'npwp_verified_by' => $customer->npwp_verified_by,
                'npwp_verified_at' => $customer->npwp_verified_at->toIso8601String(),
                'is_pkp' => $customer->is_pkp,
                'is_pph23_withholder' => $customer->is_pph23_withholder,
            ],
            notes: (string) $request->input('notes', 'NPWP verified by Finance AR'),
            userId: isset($userContext['id']) ? (string) $userContext['id'] : null
        );

        return ApiResponse::success((new CustomerResource($customer))->resolve($request));
    }

    public function anonymize(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $customer = Customer::find($id);

        if (!$customer) {
            return ApiResponse::rfcProblem('Customer Not Found', 404, "Customer #{$id} tidak ditemukan.");
        }

        if (!$this->policy->anonymize($userContext, $customer)) {
            return ApiResponse::rfcProblem(
                title: 'Forbidden',
                status: 403,
                detail: 'Anda tidak memiliki izin untuk melakukan anonimisasi data pribadi (UU PDP).'
            );
        }

        $reason = trim((string) $request->input('reason', 'Permintaan subjek data sesuai UU PDP'));
        if (mb_strlen($reason) < 5) {
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: 'Alasan anonimisasi wajib diisi minimal 5 karakter.'
            );
        }

        $anonymized = $this->pdpAnonymizer->anonymize($customer, $userContext['id'] ?? null, $reason);

        return ApiResponse::success((new CustomerResource($anonymized))->resolve($request));
    }

    public function recalculateHealthScore(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $customer = Customer::find($id);

        if (!$customer) {
            return ApiResponse::rfcProblem('Customer Not Found', 404, "Customer #{$id} tidak ditemukan.");
        }

        if (!$this->policy->view($userContext, $customer)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Akses ditolak.');
        }

        if ($request->has('financial_snapshot')) {
            $customer->financial_snapshot = array_merge($customer->financial_snapshot ?? [], (array) $request->input('financial_snapshot'));
        }
        if ($request->has('external_snapshots')) {
            $customer->external_snapshots = array_merge($customer->external_snapshots ?? [], (array) $request->input('external_snapshots'));
        }
        if ($request->has('nps_score')) {
            $customer->nps_score = (int) $request->input('nps_score');
        }
        if ($request->has('csat_score')) {
            $customer->csat_score = (float) $request->input('csat_score');
        }

        $breakdown = $customer->calculateHealthScore($request->all());
        $customer->save();

        return ApiResponse::success([
            'customer_id' => $customer->id,
            'health_score' => $customer->health_score,
            'breakdown' => $breakdown,
        ]);
    }

    public function export(Request $request): Response|JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $customers = Customer::with('primaryContact')
            ->accessibleBy($userContext)
            ->orderBy('id')
            ->get();

        $canViewUnmasked = $this->policy->canViewUnmaskedNpwp($userContext);

        if ($request->query('format') === 'json') {
            return ApiResponse::success(CustomerResource::collection($customers)->resolve($request));
        }

        $headers = ['code', 'legal_name', 'brand_name', 'type', 'industry', 'size', 'npwp', 'status', 'health_score', 'phone', 'email', 'primary_contact_name', 'primary_contact_phone_wa'];
        $lines = [implode(',', $headers)];

        foreach ($customers as $c) {
            $res = (new CustomerResource($c))->resolve($request);
            $npwpVal = $canViewUnmasked ? ($c->npwp ?? '') : ($res['npwp'] ?? '');
            $row = [
                $c->code ?? $c->customer_code,
                '"' . str_replace('"', '""', (string) $c->legal_name) . '"',
                '"' . str_replace('"', '""', (string) ($c->brand_name ?? '')) . '"',
                $c->type,
                $c->industry ?? '',
                $c->size ?? '',
                $npwpVal,
                $c->status,
                $c->health_score,
                $c->phone ?? '',
                $c->email ?? '',
                '"' . str_replace('"', '""', (string) ($c->primaryContact?->name ?? '')) . '"',
                $c->primaryContact?->phone_wa ?? $c->primaryContact?->phone ?? '',
            ];
            $lines[] = implode(',', $row);
        }

        $csv = "\xEF\xBB\xBF" . implode("\n", $lines);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="customers_export.csv"',
        ]);
    }

    public function import(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $rows = $request->input('rows', []);

        if (empty($rows) && $request->filled('csv_content')) {
            $lines = preg_split('/\r\n|\r|\n/', trim((string) $request->input('csv_content')));
            if (count($lines) > 1) {
                $headerLine = preg_replace('/^\xEF\xBB\xBF/', '', (string) array_shift($lines));
                $headers = str_getcsv((string) $headerLine);
                foreach ($lines as $line) {
                    if (trim($line) === '') {
                        continue;
                    }
                    $values = str_getcsv($line);
                    if (count($values) === count($headers)) {
                        $rows[] = array_combine($headers, $values);
                    }
                }
            }
        }

        $importedCount = 0;
        $skippedDuplicates = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            $legalName = trim((string) ($row['legal_name'] ?? ''));
            if ($legalName === '') {
                $errors[] = ['row' => $index + 1, 'message' => 'legal_name wajib diisi.'];
                continue;
            }

            $dupCheck = $this->duplicateDetector->check($row);
            if ($dupCheck['has_duplicates'] && !$request->boolean('force_create')) {
                $skippedDuplicates[] = [
                    'row' => $index + 1,
                    'legal_name' => $legalName,
                    'matched_customer_id' => $dupCheck['matches'][0]['customer_id'],
                ];
                continue;
            }

            $this->createCustomerRecord([
                'legal_name' => $legalName,
                'brand_name' => $row['brand_name'] ?? null,
                'type' => $row['type'] ?? 'perusahaan',
                'industry' => $row['industry'] ?? null,
                'size' => $row['size'] ?? null,
                'npwp' => $row['npwp'] ?? null,
                'phone' => $row['phone'] ?? $row['primary_contact_phone_wa'] ?? null,
                'email' => $row['email'] ?? null,
                'contacts' => !empty($row['primary_contact_name']) ? [[
                    'name' => $row['primary_contact_name'],
                    'phone_wa' => $row['primary_contact_phone_wa'] ?? $row['phone'] ?? null,
                    'email' => $row['primary_contact_email'] ?? $row['email'] ?? null,
                    'role' => $row['primary_contact_role'] ?? 'decision_maker',
                    'is_primary' => true,
                ]] : [],
            ], $userContext);

            $importedCount++;
        }

        return ApiResponse::success([
            'imported_count' => $importedCount,
            'skipped_duplicates_count' => count($skippedDuplicates),
            'skipped_duplicates' => $skippedDuplicates,
            'errors' => $errors,
        ]);
    }

    private function createCustomerRecord(array $data, array $userContext): Customer
    {
        return DB::transaction(function () use ($data, $userContext) {
            $sourceId = $data['first_source_id'] ?? $data['latest_source_id'] ?? null;
            if (!$sourceId && !empty($data['source_code'])) {
                $sourceId = LeadSource::where('code', strtoupper((string) $data['source_code']))->value('id');
            }

            $customer = new Customer([
                'legal_name' => $data['legal_name'],
                'brand_name' => $data['brand_name'] ?? null,
                'type' => $data['type'] ?? 'perusahaan',
                'industry' => $data['industry'] ?? null,
                'size' => $data['size'] ?? null,
                'website' => $data['website'] ?? null,
                'npwp' => $data['npwp'] ?? null,
                'nib' => $data['nib'] ?? null,
                'is_pkp' => (bool) ($data['is_pkp'] ?? false),
                'is_pph23_withholder' => (bool) ($data['is_pph23_withholder'] ?? false),
                'status' => $data['status'] ?? 'prospect',
                'phone' => !empty($data['phone']) ? PhoneNormalizer::toE164((string) $data['phone']) : null,
                'email' => $data['email'] ?? null,
                'billing_address' => $data['billing_address'] ?? null,
                'office_address' => $data['office_address'] ?? null,
                'first_source_id' => $sourceId,
                'latest_source_id' => $data['latest_source_id'] ?? $sourceId,
                'owner_id' => $data['owner_id'] ?? $userContext['id'] ?? 1,
                'team_id' => $data['team_id'] ?? $userContext['team_id'] ?? 1,
                'company_id' => $userContext['company_id'] ?? 1,
                'created_by' => isset($userContext['id']) ? (string) $userContext['id'] : '1',
            ]);

            $customer->calculateHealthScore();
            $customer->save();

            // Create Contacts
            $contactsInput = $data['contacts'] ?? [];
            foreach ($contactsInput as $idx => $contactInput) {
                $phoneWa = $contactInput['phone_wa'] ?? $contactInput['phone'] ?? $customer->phone;
                Contact::create([
                    'customer_id' => $customer->id,
                    'name' => $contactInput['name'],
                    'role' => $contactInput['role'] ?? 'decision_maker',
                    'title' => $contactInput['title'] ?? $contactInput['position'] ?? null,
                    'email' => $contactInput['email'] ?? null,
                    'phone_wa' => $phoneWa ? PhoneNormalizer::toE164((string) $phoneWa) : null,
                    'is_primary' => $contactInput['is_primary'] ?? ($idx === 0),
                    'consent_comm' => $contactInput['consent_comm'] ?? true,
                    'preferred_channel' => $contactInput['preferred_channel'] ?? 'whatsapp',
                ]);
            }

            // Create Customer Attribution if source is known
            if ($sourceId) {
                $utm = (array) ($data['utm'] ?? []);
                CustomerAttribution::create([
                    'customer_id' => $customer->id,
                    'source_id' => $sourceId,
                    'touch_type' => 'first_touch',
                    'sub_source' => $data['sub_source'] ?? null,
                    'campaign_name' => $utm['campaign'] ?? $data['campaign_name'] ?? null,
                    'utm_source' => $utm['source'] ?? null,
                    'utm_medium' => $utm['medium'] ?? null,
                    'utm_campaign' => $utm['campaign'] ?? null,
                    'utm_term' => $utm['term'] ?? null,
                    'utm_content' => $utm['content'] ?? null,
                    'touched_at' => now(),
                ]);
            }

            // Sync Custom Fields
            if (!empty($data['custom_fields']) && is_array($data['custom_fields'])) {
                $this->syncCustomFields($customer, $data['custom_fields']);
            }

            return $customer;
        });
    }

    private function syncCustomFields(Customer $customer, array $customFields): void
    {
        foreach ($customFields as $key => $val) {
            if (is_array($val) && isset($val['field_key'])) {
                $fieldKey = (string) $val['field_key'];
                $fieldValue = $val['value'] ?? null;
            } else {
                $fieldKey = (string) $key;
                $fieldValue = $val;
            }

            $definition = CustomFieldDefinition::where('entity_type', 'customer')
                ->where('field_key', $fieldKey)
                ->first();

            if (!$definition) {
                continue;
            }

            CustomFieldValue::updateOrCreate(
                [
                    'definition_id' => $definition->id,
                    'entity_type' => 'customer',
                    'entity_id' => $customer->id,
                ],
                [
                    'value_text' => is_scalar($fieldValue) ? (string) $fieldValue : null,
                    'value_json' => is_array($fieldValue) ? $fieldValue : null,
                ]
            );
        }
    }
}

