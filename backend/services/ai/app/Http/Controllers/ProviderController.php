<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Providers\AiProviderManager;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ProviderController extends Controller
{
    private AiProviderManager $providerManager;

    public function __construct(AiProviderManager $providerManager)
    {
        $this->providerManager = $providerManager;
    }

    /**
     * List all registered AI providers and health/availability status.
     */
    public function index(): JsonResponse
    {
        $providers = $this->providerManager->listProviders();
        return ApiResponse::success($providers);
    }
}

