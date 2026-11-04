<?php

declare(strict_types=1);

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\GoogleIntegrationController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadSourceController;
use App\Http\Controllers\PublicLeadCaptureController;
use App\Http\Controllers\RabController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\SavedViewController;
use App\Http\Controllers\TopController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'UP',
        'service' => 'astacode-crm',
        'port' => 8002,
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Public Lead Capture (Web Forms & Public Inbound)
Route::post('/public/lead-capture', [PublicLeadCaptureController::class, 'capture']);

$registerCrmRoutes = function () {
    // Public capture under API prefix as well
    Route::post('/public/lead-capture', [PublicLeadCaptureController::class, 'capture']);

    // Pipeline Stages Config (Probabilities & SLA)
    Route::get('/pipeline-stages', [LeadController::class, 'pipelineStages']);
    Route::match(['put', 'patch'], '/pipeline-stages/{code}', [LeadController::class, 'updatePipelineStage']);

    // Lead utility, bulk, and export endpoints (must precede {id} wildcard)
    Route::get('/leads/export', [LeadController::class, 'export']);
    Route::post('/leads/bulk-assign', [LeadController::class, 'bulkAssign']);
    Route::post('/leads/bulk-tags', [LeadController::class, 'bulkTags']);
    Route::post('/leads/bulk-delete', [LeadController::class, 'bulkDelete']);

    // Leads CRUD, Stage Transition, Checklists, Next Actions, Audit Logs & Reopen
    Route::get('/leads', [LeadController::class, 'index']);
    Route::post('/leads', [LeadController::class, 'store']);
    Route::get('/leads/{id}', [LeadController::class, 'show'])->whereNumber('id');
    Route::match(['put', 'patch'], '/leads/{id}', [LeadController::class, 'update'])->whereNumber('id');
    Route::delete('/leads/{id}', [LeadController::class, 'destroy'])->whereNumber('id');
    Route::match(['patch', 'post'], '/leads/{id}/stage', [LeadController::class, 'transitionStage'])->whereNumber('id');
    Route::post('/leads/{id}/reopen', [LeadController::class, 'reopen'])->whereNumber('id');
    Route::get('/leads/{id}/checklists', [LeadController::class, 'checklists'])->whereNumber('id');
    Route::match(['post', 'patch'], '/leads/{id}/checklists/{checklistId}/toggle', [LeadController::class, 'toggleChecklist'])
        ->whereNumber(['id', 'checklistId']);
    Route::match(['get', 'post'], '/leads/{id}/next-actions', [LeadController::class, 'nextActions'])->whereNumber('id');
    Route::get('/leads/{id}/audit-logs', [LeadController::class, 'auditLogs'])->whereNumber('id');
    Route::get('/leads/{id}/activities', [ActivityController::class, 'index'])->whereNumber('id');
    Route::post('/leads/{id}/activities', [ActivityController::class, 'store'])->whereNumber('id');

    // Requirements & Async AI RAB Generation (FR-CRM-05, BR-CRM-10, AC-CRM-03)
    Route::get('/leads/{id}/requirements', [RequirementController::class, 'index'])->whereNumber('id');
    Route::post('/leads/{id}/requirements', [RequirementController::class, 'store'])->whereNumber('id');
    Route::post('/leads/{id}/requirements/generate', [RequirementController::class, 'generate'])->whereNumber('id');
    Route::post('/leads/{id}/requirements/{requirementId}/use-as-rab', [RequirementController::class, 'useAsRab'])
        ->whereNumber(['id', 'requirementId']);
    Route::post('/leads/{id}/requirements/{requirementId}/merge-to-rab', [RequirementController::class, 'mergeToRab'])
        ->whereNumber(['id', 'requirementId']);

    // RAB Engine: Editable Spreadsheet, Versioning, Optimistic Lock, Recalculate, Dual-View & Approvals (FR-CRM-06, BR-CRM-07, AC-CRM-04, AC-CRM-07, FR-CRM-17)
    Route::post('/rab/recalculate', [RabController::class, 'recalculate']);
    Route::get('/leads/{id}/rab', [RabController::class, 'show'])->whereNumber('id');
    Route::match(['put', 'post'], '/leads/{id}/rab', [RabController::class, 'upsert'])->whereNumber('id');
    Route::patch('/leads/{id}/rab/autosave', [RabController::class, 'autosave'])->whereNumber('id');
    Route::post('/leads/{id}/rab/recalculate', [RabController::class, 'recalculate'])->whereNumber('id');
    Route::get('/leads/{id}/rab/versions', [RabController::class, 'versions'])->whereNumber('id');
    Route::get('/leads/{id}/rab/diff', [RabController::class, 'diff'])->whereNumber('id');
    Route::post('/leads/{id}/rab/import-catalog', [RabController::class, 'importCatalog'])->whereNumber('id');
    Route::post('/leads/{id}/rab/import-excel', [RabController::class, 'importExcel'])->whereNumber('id');
    Route::get('/leads/{id}/rab/export-excel', [RabController::class, 'exportExcel'])->whereNumber('id');
    Route::post('/leads/{id}/rab/items/{itemId}/duplicate', [RabController::class, 'duplicateItem'])
        ->whereNumber(['id', 'itemId']);
    Route::post('/leads/{id}/rab/reorder', [RabController::class, 'reorderItems'])->whereNumber('id');
    Route::post('/leads/{id}/rab/approve', [RabController::class, 'decideApproval'])->whereNumber('id');

    // TOP / Termin Pembayaran (FR-CRM-07, BR-CRM-02, BR-CRM-18, AC-CRM-05, AC-CRM-12)
    Route::post('/top/lock', [TopController::class, 'lock']);
    Route::post('/top/approve-change', [TopController::class, 'approveChange']);
    Route::get('/leads/{id}/top', [TopController::class, 'show'])->whereNumber('id');
    Route::match(['put', 'post'], '/leads/{id}/top', [TopController::class, 'upsert'])->whereNumber('id');
    Route::post('/leads/{id}/top/lock', [TopController::class, 'lock'])->whereNumber('id');
    Route::post('/leads/{id}/top/approve-change', [TopController::class, 'approveChange'])->whereNumber('id');
    Route::get('/leads/{id}/top/receivable-schedule', [TopController::class, 'receivableSchedule'])->whereNumber('id');

    // Activities & Calendar (FR-CRM-04)
    Route::get('/activities/calendar', [ActivityController::class, 'calendar']);
    Route::get('/activities', [ActivityController::class, 'index']);
    Route::post('/activities', [ActivityController::class, 'store']);
    Route::get('/activities/{id}', [ActivityController::class, 'show'])->whereNumber('id');
    Route::match(['put', 'patch'], '/activities/{id}', [ActivityController::class, 'update'])->whereNumber('id');
    Route::delete('/activities/{id}', [ActivityController::class, 'destroy'])->whereNumber('id');
    Route::post('/activities/{id}/complete', [ActivityController::class, 'complete'])->whereNumber('id');
    Route::post('/activities/{id}/reschedule', [ActivityController::class, 'reschedule'])->whereNumber('id');
    Route::post('/activities/{id}/recording', [ActivityController::class, 'uploadRecording'])->whereNumber('id');
    Route::post('/activities/{id}/ai-notes', [ActivityController::class, 'generateAiNotes'])->whereNumber('id');
    Route::post('/activities/{id}/push-to-requirement', [ActivityController::class, 'pushToRequirement'])->whereNumber('id');

    // Google Calendar & Meet Integrations (PRD §14, FR-CRM-04)
    Route::get('/integrations/google/status', [GoogleIntegrationController::class, 'status']);
    Route::get('/integrations/google/auth-url', [GoogleIntegrationController::class, 'authUrl']);
    Route::post('/integrations/google/callback', [GoogleIntegrationController::class, 'callback']);
    Route::post('/integrations/google/disconnect', [GoogleIntegrationController::class, 'disconnect']);
    Route::post('/webhooks/google-calendar', [GoogleIntegrationController::class, 'webhook']);

    // Saved Views
    Route::get('/saved-views', [SavedViewController::class, 'index']);
    Route::post('/saved-views', [SavedViewController::class, 'store']);
    Route::delete('/saved-views/{id}', [SavedViewController::class, 'destroy'])->whereNumber('id');

    // Customer utility & bulk endpoints (must precede {id} wildcard)
    Route::get('/customers/export', [CustomerController::class, 'export']);
    Route::post('/customers/import', [CustomerController::class, 'import']);
    Route::match(['get', 'post'], '/customers/duplicates-check', [CustomerController::class, 'duplicatesCheck']);
    Route::post('/customers/merge', [CustomerController::class, 'merge']);

    // Customer CRUD & 360°
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->whereNumber('id');
    Route::match(['put', 'patch'], '/customers/{id}', [CustomerController::class, 'update'])->whereNumber('id');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->whereNumber('id');

    Route::get('/customers/{id}/360', [CustomerController::class, 'show360'])->whereNumber('id');
    Route::post('/customers/{id}/merge', [CustomerController::class, 'merge'])->whereNumber('id');
    Route::match(['post', 'patch'], '/customers/{id}/verify-npwp', [CustomerController::class, 'verifyNpwp'])->whereNumber('id');
    Route::post('/customers/{id}/anonymize', [CustomerController::class, 'anonymize'])->whereNumber('id');
    Route::post('/customers/{id}/health-score', [CustomerController::class, 'recalculateHealthScore'])->whereNumber('id');

    // Nested Contacts under Customer
    Route::get('/customers/{customerId}/contacts', [ContactController::class, 'index'])->whereNumber('customerId');
    Route::post('/customers/{customerId}/contacts', [ContactController::class, 'store'])->whereNumber('customerId');
    Route::match(['put', 'patch'], '/customers/{customerId}/contacts/{contactId}', [ContactController::class, 'update'])
        ->whereNumber(['customerId', 'contactId']);
    Route::delete('/customers/{customerId}/contacts/{contactId}', [ContactController::class, 'destroy'])
        ->whereNumber(['customerId', 'contactId']);

    // Flat Contacts endpoints
    Route::get('/contacts', [ContactController::class, 'index']);
    Route::post('/contacts', [ContactController::class, 'store']);
    Route::match(['put', 'patch'], '/contacts/{id}', [ContactController::class, 'update'])->whereNumber('id');
    Route::delete('/contacts/{id}', [ContactController::class, 'destroy'])->whereNumber('id');

    // Lead Sources Master
    Route::get('/lead-sources', [LeadSourceController::class, 'index']);
    Route::post('/lead-sources', [LeadSourceController::class, 'store']);
    Route::match(['put', 'patch'], '/lead-sources/{id}', [LeadSourceController::class, 'update'])->whereNumber('id');
    Route::delete('/lead-sources/{id}', [LeadSourceController::class, 'destroy'])->whereNumber('id');

    // Custom Field Definitions
    Route::get('/custom-fields', [CustomFieldController::class, 'index']);
    Route::post('/custom-fields', [CustomFieldController::class, 'store']);
    Route::match(['put', 'patch'], '/custom-fields/{id}', [CustomFieldController::class, 'update'])->whereNumber('id');
    Route::delete('/custom-fields/{id}', [CustomFieldController::class, 'destroy'])->whereNumber('id');
};

// Direct /api/* routes (for Gateway Reverse Proxy /api/v1/crm/* -> /api/*)
$registerCrmRoutes();

// Also support /api/v1/crm/* directly for test convenience
Route::prefix('v1/crm')->group($registerCrmRoutes);
