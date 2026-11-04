<?php

namespace Tests\Feature;

use Tests\TestCase;

class GatewayOpenApiContractTest extends TestCase
{
    private string $specPath;
    private string $specContent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->specPath = base_path('../../docs/API-CONTRACTS/gateway.yaml');
        $this->assertFileExists($this->specPath, 'OpenAPI gateway specification file (docs/API-CONTRACTS/gateway.yaml) must exist.');
        $this->specContent = file_get_contents($this->specPath);
    }

    public function test_gateway_spec_is_valid_openapi_3_1_0(): void
    {
        $this->assertStringContainsString('openapi: 3.1.0', $this->specContent);
        $this->assertStringContainsString('title: ASTACODE ERP Orchestrator Gateway API', $this->specContent);
        $this->assertStringContainsString('paths:', $this->specContent);
        $this->assertStringContainsString('components:', $this->specContent);
    }

    public function test_all_openapi_service_prefixes_are_registered_in_gateway_service_map(): void
    {
        $servicesMap = config('services_map', []);
        $this->assertNotEmpty($servicesMap);

        // Required 8 microservices registered in Gateway
        $expectedServices = ['core', 'crm', 'marketing', 'hrd', 'pm', 'finance', 'ai', 'orchestrator'];
        foreach ($expectedServices as $svc) {
            $this->assertArrayHasKey($svc, $servicesMap, "Microservice [{$svc}] must be registered in config/services_map.php");
            $this->assertNotEmpty($servicesMap[$svc]['url'], "URL for [{$svc}] must not be empty.");
            $this->assertGreaterThan(8000, $servicesMap[$svc]['port'], "Port for [{$svc}] must be configured.");
        }
    }

    public function test_gateway_health_endpoint_matches_openapi_contract(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'service',
            'timestamp',
        ]);
        $response->assertJson([
            'status' => 'UP',
            'service' => 'astacode-gateway',
        ]);
    }

    public function test_gateway_proxy_rejects_unregistered_services_with_rfc7807_problem_details(): void
    {
        $response = $this->getJson('/api/v1/unknown-service/something');

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'type',
            'title',
            'status',
            'detail',
        ]);
        $response->assertJson([
            'status' => 404,
            'title' => 'Service Not Found',
        ]);
    }

    public function test_openapi_enforces_rfc7807_problem_details_schema(): void
    {
        $this->assertStringContainsString('ApiProblemDetails:', $this->specContent);
        $this->assertStringContainsString('correlation_id:', $this->specContent);
        $this->assertStringContainsString('application/problem+json:', $this->specContent);
    }

    public function test_openapi_documents_standard_query_conventions(): void
    {
        $this->assertStringContainsString('PageQuery:', $this->specContent);
        $this->assertStringContainsString('PerPageQuery:', $this->specContent);
        $this->assertStringContainsString('SearchQuery:', $this->specContent);
        $this->assertStringContainsString('filter[stage]', $this->specContent);
    }
}

