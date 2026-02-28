<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PurchaseSummaryRequest;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function purchaseSummary(PurchaseSummaryRequest $request): JsonResponse
    {
        $results = $this->reportService->purchaseSummary($request->validated());

        return response()->json([
            'message' => 'Purchase summary retrieved successfully.',
            'filters' => [
                'start_date' => $request->start_date,
                'end_date'   => $request->end_date,
                'vendor_id'  => $request->vendor_id,
            ],
            'total_vendors' => count($results),
            'data'          => $results,
        ]);
    }
}
