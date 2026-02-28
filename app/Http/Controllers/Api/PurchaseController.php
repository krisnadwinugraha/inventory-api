<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StorePurchaseRequest;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;

class PurchaseController extends Controller
{
    public function __construct(
        protected PurchaseService $purchaseService
    ) {}

    public function store(StorePurchaseRequest $request): JsonResponse
    {
        $purchase = $this->purchaseService->store($request->validated());

        return response()->json([
            'message' => 'Purchase created successfully.',
            'data'    => $purchase,
        ], 201);
    }
}
