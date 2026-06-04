<?php

namespace App\Http\Controllers\Mobile;

use App\Entities\Patient;
use App\Entities\SalesOrder;
use App\Entities\Schedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileDashboardController extends MobileController
{
    public function index(Request $request): JsonResponse
    {
        [$startDate, $endDate] = $this->resolveRange($request);
        $paidStatuses = [1, 3, 4];

        $cards = [
            'schedules_today' => Schedule::query()->whereDate('date', now()->toDateString())->count(),
            'schedules_period' => Schedule::query()->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])->count(),
            'revenue_period' => (float) SalesOrder::query()
                ->whereIn('status', $paidStatuses)
                ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->sum('amount'),
            'ticket_average' => (float) (SalesOrder::query()
                ->whereIn('status', $paidStatuses)
                ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->avg('amount') ?? 0),
            'new_patients_period' => Patient::query()
                ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->count(),
        ];

        return response()->json([
            'data' => [
                'cards' => $cards,
                'charts' => [
                    'schedules_by_day' => [],
                    'schedule_status' => [],
                    'revenue_by_month' => [],
                    'payment_methods' => [],
                    'top_procedures' => [],
                    'sales_status' => [],
                ],
            ],
            'meta' => [
                'range' => [
                    'start' => $startDate->toDateString(),
                    'end' => $endDate->toDateString(),
                ],
            ],
        ]);
    }

    private function resolveRange(Request $request): array
    {
        $defaultStart = now()->startOfMonth();
        $defaultEnd = now()->endOfMonth();

        $startDate = $this->parseDate($request->query('start')) ?? $defaultStart;
        $endDate = $this->parseDate($request->query('end')) ?? $defaultEnd;

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate->copy(), $startDate->copy()];
        }

        return [$startDate->startOfDay(), $endDate->endOfDay()];
    }

    private function parseDate(?string $value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        foreach (['Y-m-d', 'd/m/Y', 'm/d/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value);
            } catch (\Throwable $exception) {
                // Keep trying supported formats.
            }
        }

        return null;
    }
}

