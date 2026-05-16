<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Entities\Patient;
use App\Entities\SalesOrder;
use App\Entities\SalesOrderItem;
use App\Entities\Schedule;
use App\Services\AppService;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Class PainelController.
 *
 * @package namespace App\Http\Controllers;
 */
class PanelController extends Controller
{

    /**
     */
    public function __construct()
    {
    }

    /**
     * @return View|Factory|Application
     */
    public function login(): View|Factory|Application
    {
        return view('login');
    }

    /**
     * @return View|Factory|Application
     */
    public function dashboard(Request $request): View|Factory|Application
    {

        $dashboardData = Cache::store('redis')->tags('dashboard-chart')->remember($request->fullUrl(), 12000, function () use ($request) {

            [$startDate, $endDate] = $this->resolveDashboardRange($request);
            $paidStatuses = [1, 3, 4];

            $schedulesToday = Schedule::query()
                ->whereDate('date', now()->toDateString())
                ->count();

            $schedulesMonth = Schedule::query()
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->count();

            $revenueMonth = SalesOrder::query()
                ->whereIn('status', $paidStatuses)
                ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->sum('amount');

            $ticketAverage = SalesOrder::query()
                ->whereIn('status', $paidStatuses)
                ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->avg('amount') ?? 0;

            $newPatientsMonth = Patient::query()
                ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->count();

            $rawSchedulesByDay = Schedule::query()
                ->selectRaw("DATE_FORMAT(date, '%Y-%m-%d') as label, COUNT(*) as total")
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->groupBy('label')
                ->orderBy('label')
                ->get();

            $schedulesByDay = $this->buildDateSeries($startDate, $endDate, $rawSchedulesByDay);

            $scheduleStatusLabels = ['Marcado', 'Confirmado', 'Adiado', 'Cancelado'];
            $rawScheduleStatus = Schedule::query()
                ->selectRaw('status as label, COUNT(*) as total')
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->groupBy('status')
                ->pluck('total', 'label');

            $scheduleStatus = collect($scheduleStatusLabels)
                ->map(fn ($status) => [
                    'label' => $status,
                    'total' => (int) ($rawScheduleStatus[$status] ?? 0),
                ])
                ->values();

            $revenueStart = $endDate->copy()->startOfMonth()->subMonths(5);
            $rawRevenueByMonth = SalesOrder::query()
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as label, SUM(amount) as total")
                ->whereIn('status', $paidStatuses)
                ->whereBetween('created_at', [$revenueStart->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->groupBy('label')
                ->orderBy('label')
                ->pluck('total', 'label');

            $revenueByMonth = collect();
            $cursorMonth = $revenueStart->copy();
            while ($cursorMonth->lte($endDate)) {
                $key = $cursorMonth->format('Y-m');
                $revenueByMonth->push([
                    'label' => $cursorMonth->format('m/Y'),
                    'total' => (float) ($rawRevenueByMonth[$key] ?? 0),
                ]);
                $cursorMonth->addMonth();
            }

            $rawPaymentMethods = SalesOrder::query()
                ->selectRaw('type_payment, COUNT(*) as total')
                ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->groupBy('type_payment')
                ->orderBy('type_payment')
                ->get();

            $paymentMethods = $rawPaymentMethods
                ->map(fn ($item) => [
                    'label' => $this->getPaymentTypeTitle((int) $item->type_payment),
                    'total' => (int) $item->total,
                ])
                ->values();

            $topProcedures = SalesOrderItem::query()
                ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_items.sales_order_id')
                ->selectRaw(
                    "sales_order_items.procedure_name as label,
                SUM(sales_order_items.qty) as total_qty,
                SUM(sales_order_items.qty * CAST(sales_order_items.price AS DECIMAL(10,2))) as total_value"
                )
                ->whereIn('sales_orders.status', $paidStatuses)
                ->whereBetween('sales_orders.created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->groupBy('sales_order_items.procedure_name')
                ->orderByDesc('total_qty')
                ->limit(10)
                ->get()
                ->map(fn ($item) => [
                    'label' => $item->label,
                    'total_qty' => (int) $item->total_qty,
                    'total_value' => (float) $item->total_value,
                ])
                ->values();

            $salesStatusMap = [
                0 => 'Inicial',
                1 => 'Pago',
                2 => 'Cancelado',
                3 => 'Parcial',
                4 => 'Finalizado',
            ];
            $rawSalesStatus = SalesOrder::query()
                ->selectRaw('status, COUNT(*) as total')
                ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->groupBy('status')
                ->pluck('total', 'status');

            $salesStatus = collect($salesStatusMap)
                ->map(fn ($label, $status) => [
                    'label' => $label,
                    'total' => (int) ($rawSalesStatus[(string) $status] ?? 0),
                ])
                ->values();

            return [
                'meta' => [
                    'start' => $startDate->toDateString(),
                    'end' => $endDate->toDateString(),
                ],
                'cards' => [
                    'schedules_today' => $schedulesToday,
                    'schedules_period' => $schedulesMonth,
                    'revenue_period' => (float) $revenueMonth,
                    'ticket_average' => (float) $ticketAverage,
                    'new_patients_period' => $newPatientsMonth,
                ],
                'charts' => [
                    'schedules_by_day' => $schedulesByDay->all(),
                    'schedule_status' => $scheduleStatus->all(),
                    'revenue_by_month' => $revenueByMonth->all(),
                    'payment_methods' => $paymentMethods->all(),
                    'top_procedures' => $topProcedures->all(),
                    'sales_status' => $salesStatus->all(),
                ],
            ];
        });


        return view('dashboard', [
            'title'       => 'Dashboard',
            'subtitle'    => 'Painel de Controle',
            'routeCreate' => route('dashboard'),
            'dashboardData' => $dashboardData,
        ]);
    }

    private function resolveDashboardRange(Request $request): array
    {
        $defaultStart = now()->subMonth()->startOfMonth();
        $defaultEnd = now()->subMonth()->endOfMonth();

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

        foreach (['Y-m-d', 'm/d/Y', 'd/m/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value);
            } catch (\Throwable $exception) {
                // Continue until a supported date format is matched.
            }
        }

        return null;
    }

    private function buildDateSeries(Carbon $startDate, Carbon $endDate, Collection $rawRows): Collection
    {
        $indexedRows = $rawRows->pluck('total', 'label');
        $series = collect();

        $cursor = $startDate->copy()->startOfDay();
        while ($cursor->lte($endDate)) {
            $key = $cursor->format('Y-m-d');
            $series->push([
                'label' => $key,
                'total' => (int) ($indexedRows[$key] ?? 0),
            ]);
            $cursor->addDay();
        }

        return $series;
    }

    private function getPaymentTypeTitle(int $typePayment): string
    {
        return match ($typePayment) {
            1 => 'PIX',
            2 => 'Cartao de Credito',
            3 => 'Cartao de Debito',
            4 => 'Dinheiro',
            default => 'Nao informado',
        };
    }

    /**
     * @return View|Factory|Application
     */
    public function scheduleIndex(): View|Factory|Application
    {
        return view('schedules.index', [
            'title'       => 'Agendamentos',
            'subtitle'    => 'Lista de Agendamentos',
            'routeCreate' => '#',
        ]);
    }

    public function scheduleCalendar(): View|Factory|Application
    {
        return view('schedules.calendar', [
            'title'       => 'Agendamentos',
            'subtitle'    => 'Calendario de Agendamentos',
            'routeCreate' => '#',
        ]);
    }

    public function schedulesPrintPage(Request $request): View|Factory|Application
    {
        $query = Schedule::query()
            ->with(['patient', 'procedure']);

        // Aplicar filtros se existirem
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->input('patient_id'));
        }

        if ($request->filled('procedure_id')) {
            $query->where('procedure_id', $request->input('procedure_id'));
        }

        if ($request->filled('procedure_type_id')) {
            $query->whereHas('procedure', function ($q) {
                $q->where('procedure_type_id', request('procedure_type_id'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('start')) {
            $query->whereDate('date', '>=', $request->input('start'));
        }

        if ($request->filled('end')) {
            $query->whereDate('date', '<=', $request->input('end'));
        }

        $schedules = $query->orderBy('date', 'desc')->orderBy('time', 'asc')->get();

        $filterInfo = null;
        if ($request->filled('start') && $request->filled('end')) {
            $start = \Carbon\Carbon::parse($request->input('start'))->format('d/m/Y');
            $end = \Carbon\Carbon::parse($request->input('end'))->format('d/m/Y');
            $filterInfo = "{$start} a {$end}";
        }

        return view('schedules.print', [
            'schedules' => $schedules,
            'filterInfo' => $filterInfo,
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function procedureIndex(): View|Factory|Application
    {
        return view('procedures.index', [
            'title'       => 'Procedimentos',
            'subtitle'    => 'Lista de Procedimentos',
            'routeCreate' => route('panel.procedure.create'),
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function campaignIndex(): View|Factory|Application
    {
        return view('campaigns.index', [
            'title'       => 'Campanhas WhatsApp',
            'subtitle'    => 'Lista de Campanhas',
            'routeCreate' => route('panel.campaign.create'),
        ]);
    }

    public function patientIndex(): View|Factory|Application
    {
        return view('patients.index', [
            'title'       => 'Pacientes',
            'subtitle'    => 'Lista de Pacientes',
            'routeCreate' => route('panel.patient.create'),
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function patientCreate(): View|Factory|Application
    {
        return view('patients.create', [
            'title'       => 'Paciente',
            'subtitle'    => 'Criação Paciente',
            'routeCreate' => route('panel.patient.create'),
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function procedureCreate(): View|Factory|Application
    {
        return view('procedures.create', [
            'title'       => 'Novo Procedimento',
            'subtitle'    => 'Criação Procedimento',
            'routeCreate' => route('panel.procedure.create'),
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function salesOrderIndex(): View|Factory|Application
    {
        return view('sales-orders.index', [
            'title'       => 'Pedidos',
            'subtitle'    => 'Lista de Pedidos',
            'routeCreate' => route('panel.sales-order.create'),
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function salesOrderCreate(Request $request): View|Factory|Application
    {
        return view('sales-orders.create', [
            'title'       => 'Pedidos',
            'subtitle'    => 'Novo Pedido',
            'routeCreate' => route('panel.sales-order.create'),
            'userId'      => $request->attributes->get('user_jwt')?->id,
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function salesOrderEdit(Request $request, int $id): View|Factory|Application
    {
        return view('sales-orders.edit', [
            'title'    => 'Pedidos',
            'subtitle' => 'Editar Pedido',
            'orderId'  => $id,
        ]);
    }

    /**
     * @return View|Factory|Application
     */
    public function salesOrderInvoice(Request $request): View|Factory|Application
    {
        $items = json_decode((string) $request->input('items', '[]'), true);
        if (!is_array($items)) {
            $items = [];
        }

        $socialName = $request->input('social_name', 'Paciente');
        $date = $request->input('date', now()->format('d/m/Y'));

        return view('sales-orders.invoice', [
            'documentTitle' => $socialName . ' - ' . $date,
            'socialName' => $socialName,
            'patientName' => $request->input('patient_name', $socialName),
            'phone' => $request->input('phone', '-'),
            'date' => $date,
            'paymentLabel' => $request->input('payment_label', 'Nao informado'),
            'brandLabel' => $request->input('brand_label', 'Nao informado'),
            'qtyInstallments' => (int)$request->input('qty_installments', 1),
            'subtotal' => (float)$request->input('subtotal', 0),
            'pixAmount' => (float)$request->input('pix_amount', 0),
            'debitAmount' => (float)$request->input('debit_amount', 0),
            'creditTotal' => (float)$request->input('credit_total', 0),
            'installmentAmount' => (float)$request->input('installment_amount', 0),
            'items' => $items,
        ]);
    }

    public function sendSalesOrderInvoiceWhatsapp(int $id): JsonResponse
    {
        $order = SalesOrder::query()
            ->with(['patient', 'salesOrderItems'])
            ->find($id);

        if (!$order instanceof SalesOrder || !$order->patient) {
            return response()->json(['error' => true, 'message' => 'Pedido nao encontrado.'], 404);
        }

        /** @var SalesOrder $order */

        $phone = (string) ($order->patient->chat_id ?? '');
        if (trim($phone) === '') {
            return response()->json(['error' => true, 'message' => 'Paciente sem telefone cadastrado.'], 422);
        }

        $invoiceData = $this->buildInvoicePayloadFromOrder($order);
        $pdfContent  = $this->buildSimplePdfInvoice($invoiceData);
        $fileName    = 'Pedido-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) . '.pdf';
        $path        = 'invoices/' . $fileName;

        $appService = app(AppService::class);
        $fileUrl = $appService->putFileS3($path, $pdfContent);
        if (!str_starts_with((string) $fileUrl, 'http')) {
            $fileUrl = url((string) $fileUrl);
        }

        $chatId = $order->patient->chat_id ?: $appService->getContactIdByPhone($phone);
        $caption = 'Segue seu Pedido #' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
        $response = $appService->sendFileToWhatsApp($chatId, $fileUrl, $fileName, $caption);

        $responseData = is_array($response) ? $response : (array) $response;
        if (!empty($responseData['error'])) {
            return response()->json(['error' => true, 'message' => 'Falha ao enviar invoice no WhatsApp.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice enviada para o WhatsApp do paciente.',
            'file_url' => $fileUrl,
        ]);
    }

    private function buildInvoicePayloadFromOrder($order): array
    {
        $paymentLabelMap = [
            1 => 'PIX',
            2 => 'Cartao de Credito',
            3 => 'Cartao de Debito',
            4 => 'Dinheiro',
        ];
        $brandLabelMap = [
            1 => 'MasterCard',
            2 => 'Visa',
            3 => 'Elo',
        ];

        $installments      = max(1, (int) $order->qty_installments);
        $subtotal          = (float) $order->amount;
//        $pixAmount         = $subtotal >= 250 ? $subtotal - ($subtotal * 0.05) : $subtotal;
        $debitAmount       = (float) $order->getDebitAmount();
        $installmentBase   = $subtotal / $installments;
        $installmentTax    = (float) $order->getInstallmentTax();
        $installmentAmount = $installmentBase + ($installmentBase * $installmentTax);
        $creditTotal       = $installmentAmount * $installments;

        $items = [];
        foreach ($order->salesOrderItems as $item) {
            $items[] = [
                'name'  => (string) ($item->procedure_name ?? '-'),
                'qty'   => (int) ($item->qty ?? 0),
                'price' => (float) ($item->price ?? 0),
            ];
        }

        return [
            'number'             => str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
            'social_name'        => (string) ($order->patient->social_name ?: $order->patient->name ?: 'Paciente'),
            'patient_name'       => (string) ($order->patient->name ?: 'Paciente'),
            'phone'              => (string) ($order->patient->phone ?: '-'),
            'date'               => optional($order->created_at)->format('d/m/Y') ?: now()->format('d/m/Y'),
            'payment_label'      => $paymentLabelMap[(int) $order->type_payment] ?? 'Nao informado',
            'brand_label'        => $brandLabelMap[(int) $order->brand_card] ?? 'Nao informado',
            'qty_installments'   => $installments,
            'subtotal'           => $subtotal,
            'pix_amount'         => $subtotal,
            'debit_amount'       => $debitAmount,
            'credit_total'       => $creditTotal,
            'installment_amount' => $installmentAmount,
            'items'              => $items,
        ];
    }

    private function buildSimplePdfInvoice(array $invoice): string
    {
        $html = view('sales-orders.invoice-pdf', [
            'documentTitle'     => 'Pedido de Venda #' . ($invoice['number'] ?? '-'),
            'socialName'        => $invoice['social_name'] ?? 'Paciente',
            'patientName'       => $invoice['patient_name'] ?? 'Paciente',
            'phone'             => $invoice['phone'] ?? '-',
            'date'              => $invoice['date'] ?? now()->format('d/m/Y'),
            'paymentLabel'      => $invoice['payment_label'] ?? 'Nao informado',
            'brandLabel'        => $invoice['brand_label'] ?? 'Nao informado',
            'qtyInstallments'   => (int) ($invoice['qty_installments'] ?? 1),
            'subtotal'          => (float) ($invoice['subtotal'] ?? 0),
            'pixAmount'         => (float) ($invoice['pix_amount'] ?? 0),
            'debitAmount'       => (float) ($invoice['debit_amount'] ?? 0),
            'creditTotal'       => (float) ($invoice['credit_total'] ?? 0),
            'installmentAmount' => (float) ($invoice['installment_amount'] ?? 0),
            'items'             => $invoice['items'] ?? [],
            'logoDataUri'       => $this->getInvoiceLogoDataUri(),
        ])->render();

        return Pdf::loadHTML($html)
            ->setPaper('a4')
            ->output();
    }

    private function getInvoiceLogoDataUri(): ?string
    {
        return Cache::rememberForever('invoice_logo_data_uri', function () {
            return config('logo.uri');
        });
    }

    public function usersIndex(): View|Factory|Application
    {
        return view('users.index', [
            'title'       => 'Colaboradores',
            'subtitle'    => 'Lista de Colaboradores',
            'routeCreate' => '#',
        ]);
    }

    public function usersEdit(int $id): View|Factory|Application
    {
        return view('users.edit', [
            'title'    => 'Colaboradores',
            'subtitle' => 'Editar Colaborador',
            'userId'   => $id,
        ]);
    }



}
