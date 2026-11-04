<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Models\Customer;
use App\Policies\CustomerPolicy;
use App\Services\CustomerDuplicateDetector;
use App\Support\UserContext;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Astacode\Kernel\Support\PhoneNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function __construct(
        private readonly CustomerPolicy $policy = new CustomerPolicy(),
        private readonly CustomerDuplicateDetector $duplicateDetector = new CustomerDuplicateDetector()
    ) {}

    public function index(Request $request, ?int $customerId = null): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $targetCustomerId = $customerId ?? ($request->query('customer_id') ? (int) $request->query('customer_id') : null);

        if ($targetCustomerId !== null) {
            $customer = Customer::find($targetCustomerId);
            if (!$customer) {
                return ApiResponse::rfcProblem('Customer Not Found', 404, "Customer #{$targetCustomerId} tidak ditemukan.");
            }
            if (!$this->policy->view($userContext, $customer)) {
                return ApiResponse::rfcProblem('Forbidden', 403, 'Akses ke kontak customer ini ditolak.');
            }

            $contacts = Contact::where('customer_id', $targetCustomerId)
                ->orderByDesc('is_primary')
                ->orderBy('name')
                ->get();

            return ApiResponse::success(ContactResource::collection($contacts)->resolve($request));
        }

        $contacts = Contact::query()
            ->whereHas('customer', fn ($q) => $q->accessibleBy($userContext))
            ->orderByDesc('is_primary')
            ->orderBy('name')
            ->paginate((int) $request->query('per_page', 15));

        return ApiResponse::paginated(
            ContactResource::collection($contacts->getCollection())->resolve($request),
            $contacts->currentPage(),
            $contacts->perPage(),
            $contacts->total()
        );
    }

    public function store(Request $request, ?int $customerId = null): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $targetCustomerId = $customerId ?? (int) $request->input('customer_id');

        $customer = Customer::find($targetCustomerId);
        if (!$customer) {
            return ApiResponse::rfcProblem('Customer Not Found', 404, 'Customer tidak ditemukan.');
        }

        if (!$this->policy->update($userContext, $customer)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Anda tidak memiliki izin menambah kontak pada customer ini.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'role' => ['nullable', 'string', 'in:decision_maker,technical_lead,finance_lead,influencer'],
            'title' => ['nullable', 'string', 'max:128'],
            'position' => ['nullable', 'string', 'max:128'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_wa' => ['nullable', 'string', 'max:32'],
            'phone' => ['nullable', 'string', 'max:32'],
            'is_primary' => ['nullable', 'boolean'],
            'consent_comm' => ['nullable', 'boolean'],
            'preferred_channel' => ['nullable', 'string', 'in:whatsapp,email,phone,meet'],
            'department' => ['nullable', 'string', 'max:128'],
            'notes' => ['nullable', 'string'],
            'force_create' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: 'Data kontak tidak valid.',
                invalidParams: $validator->errors()->toArray()
            );
        }

        $data = $validator->validated();
        $rawPhone = $data['phone_wa'] ?? $data['phone'] ?? null;
        $normalizedPhone = $rawPhone ? PhoneNormalizer::toE164((string) $rawPhone) : null;
        $email = isset($data['email']) ? strtolower(trim((string) $data['email'])) : null;

        // BR-CRM-09: Unique phone_wa / email per customer-contact; check duplicates
        $existingSameCustomer = Contact::where('customer_id', $customer->id)
            ->where(function ($q) use ($normalizedPhone, $email) {
                if ($normalizedPhone) {
                    $q->orWhere('phone_wa', $normalizedPhone)->orWhere('phone', $normalizedPhone);
                }
                if ($email) {
                    $q->orWhere('email', $email);
                }
            })
            ->first();

        if ($existingSameCustomer && !$request->boolean('force_create')) {
            return ApiResponse::rfcProblem(
                title: 'Duplicate Contact Detected',
                status: 409,
                detail: 'Nomor WA atau Email sudah terdaftar pada kontak customer ini (BR-CRM-09).',
                extra: [
                    'existing_contact' => (new ContactResource($existingSameCustomer))->resolve($request),
                ]
            );
        }

        // Cross-customer duplicate check
        $crossDup = $this->duplicateDetector->check([
            'phone_wa' => $normalizedPhone,
            'email' => $email,
        ], $customer->id);

        if ($crossDup['has_duplicates'] && !$request->boolean('force_create')) {
            return ApiResponse::rfcProblem(
                title: 'Duplicate Contact Across Customer',
                status: 409,
                detail: 'Customer sudah ada: Tautkan / Buat baru / Gabung',
                extra: [
                    'duplicate_check' => $crossDup,
                ]
            );
        }

        if (!empty($data['is_primary'])) {
            Contact::where('customer_id', $customer->id)->update(['is_primary' => false]);
        }

        $contact = Contact::create([
            'customer_id' => $customer->id,
            'name' => $data['name'],
            'role' => $data['role'] ?? 'decision_maker',
            'title' => $data['title'] ?? $data['position'] ?? null,
            'email' => $email,
            'phone_wa' => $normalizedPhone,
            'is_primary' => (bool) ($data['is_primary'] ?? false),
            'consent_comm' => (bool) ($data['consent_comm'] ?? true),
            'preferred_channel' => $data['preferred_channel'] ?? 'whatsapp',
            'department' => $data['department'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return ApiResponse::success(
            (new ContactResource($contact))->resolve($request),
            ['message' => 'Kontak berhasil ditambahkan.'],
            201
        );
    }

    public function update(Request $request, int $customerId, ?int $contactId = null): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $targetContactId = $contactId ?? $customerId;

        $contact = Contact::find($targetContactId);
        if (!$contact || ($contactId !== null && $contact->customer_id !== $customerId)) {
            return ApiResponse::rfcProblem('Contact Not Found', 404, 'Kontak tidak ditemukan.');
        }

        $customer = Customer::find($contact->customer_id);
        if (!$customer || !$this->policy->update($userContext, $customer)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Anda tidak memiliki izin mengubah kontak ini.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'min:2', 'max:255'],
            'role' => ['sometimes', 'string', 'in:decision_maker,technical_lead,finance_lead,influencer'],
            'title' => ['nullable', 'string', 'max:128'],
            'position' => ['nullable', 'string', 'max:128'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_wa' => ['nullable', 'string', 'max:32'],
            'phone' => ['nullable', 'string', 'max:32'],
            'is_primary' => ['nullable', 'boolean'],
            'consent_comm' => ['nullable', 'boolean'],
            'preferred_channel' => ['nullable', 'string', 'in:whatsapp,email,phone,meet'],
            'department' => ['nullable', 'string', 'max:128'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: 'Data kontak tidak valid.',
                invalidParams: $validator->errors()->toArray()
            );
        }

        $data = $validator->validated();
        if (!empty($data['is_primary'])) {
            Contact::where('customer_id', $contact->customer_id)
                ->where('id', '!=', $contact->id)
                ->update(['is_primary' => false]);
        }

        $contact->fill($data);
        $contact->save();

        return ApiResponse::success((new ContactResource($contact))->resolve($request));
    }

    public function destroy(Request $request, int $customerId, ?int $contactId = null): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $targetContactId = $contactId ?? $customerId;

        $contact = Contact::find($targetContactId);
        if (!$contact || ($contactId !== null && $contact->customer_id !== $customerId)) {
            return ApiResponse::rfcProblem('Contact Not Found', 404, 'Kontak tidak ditemukan.');
        }

        $customer = Customer::find($contact->customer_id);
        if (!$customer || !$this->policy->update($userContext, $customer)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Anda tidak memiliki izin menghapus kontak ini.');
        }

        $contact->delete();

        return ApiResponse::success(['deleted' => true, 'id' => $targetContactId]);
    }
}

