@extends('layouts.header')
@section('content')
    @php
        $cards = $dashboardData['cards'] ?? [];
        $charts = $dashboardData['charts'] ?? [];
        $meta = $dashboardData['meta'] ?? [];
        $hasSchedulesByDay = collect($charts['schedules_by_day'] ?? [])->sum('total') > 0;
        $hasScheduleStatus = collect($charts['schedule_status'] ?? [])->sum('total') > 0;
        $hasRevenueByMonth = collect($charts['revenue_by_month'] ?? [])->sum('total') > 0;
        $hasPaymentMethods = collect($charts['payment_methods'] ?? [])->sum('total') > 0;
        $hasSalesStatus = collect($charts['sales_status'] ?? [])->sum('total') > 0;
        $hasTopProcedures = collect($charts['top_procedures'] ?? [])->sum('total_qty') > 0;
    @endphp

    <div class="g-3 lh-1 mb-3 row row-cols-1 row-cols-lg-5 row-cols-sm-2">
        <div class="col">
            <div class="card flex-row p-5">
                <i class="align-items-center bg-active d-flex flex-shrink-0 fs-2 h-11 justify-content-center me-4 ph ph-calendar rounded text-body-emphasis w-11"></i>
                <div class="flex-grow-1">
                    <div class="fs-5 fw-medium mb-1 me-2 text-body-emphasis">{{ number_format($cards['schedules_today'] ?? 0, 0, ',', '.') }}</div>
                    Agendamentos hoje
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card flex-row p-5">
                <i class="align-items-center bg-active d-flex flex-shrink-0 fs-2 h-11 justify-content-center me-4 ph ph-calendar-check rounded text-body-emphasis w-11"></i>
                <div class="flex-grow-1">
                    <div class="fs-5 fw-medium mb-1 me-2 text-body-emphasis">{{ number_format($cards['schedules_period'] ?? 0, 0, ',', '.') }}</div>
                    Agendamentos no periodo
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card flex-row p-5">
                <i class="align-items-center bg-active d-flex flex-shrink-0 fs-2 h-11 justify-content-center me-4 ph ph-currency-circle-dollar rounded text-body-emphasis w-11"></i>
                <div class="flex-grow-1">
                    <div class="fs-5 fw-medium mb-1 me-2 text-body-emphasis">R$ {{ number_format($cards['revenue_period'] ?? 0, 2, ',', '.') }}</div>
                    Faturamento no periodo
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card flex-row p-5">
                <i class="align-items-center bg-active d-flex flex-shrink-0 fs-2 h-11 justify-content-center me-4 ph ph-receipt rounded text-body-emphasis w-11"></i>
                <div class="flex-grow-1">
                    <div class="fs-5 fw-medium mb-1 me-2 text-body-emphasis">R$ {{ number_format($cards['ticket_average'] ?? 0, 2, ',', '.') }}</div>
                    Ticket medio
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card flex-row p-5">
                <i class="align-items-center bg-active d-flex flex-shrink-0 fs-2 h-11 justify-content-center me-4 ph ph-user-plus rounded text-body-emphasis w-11"></i>
                <div class="flex-grow-1">
                    <div class="fs-5 fw-medium mb-1 me-2 text-body-emphasis">{{ number_format($cards['new_patients_period'] ?? 0, 0, ',', '.') }}</div>
                    Novos pacientes no periodo
                </div>
            </div>
        </div>
    </div>

    <div class="g-3 mb-3 row row-cols-1 row-cols-lg-2">
        <div class="col">
            <div class="card p-4">
                <h3 class="fs-6 mb-3 text-body-emphasis">Agendamentos por dia</h3>
                @if($hasSchedulesByDay)
                    <div class="position-relative" style="height: 260px;">
                        <canvas id="chart-schedules-by-day"></canvas>
                    </div>
                @else
                    <p class="mb-0 text-muted">Sem dados no periodo selecionado.</p>
                @endif
            </div>
        </div>
        <div class="col">
            <div class="card p-4">
                <h3 class="fs-6 mb-3 text-body-emphasis">Status dos agendamentos</h3>
                @if($hasScheduleStatus)
                    <div class="position-relative" style="height: 260px;">
                        <canvas id="chart-schedule-status"></canvas>
                    </div>
                @else
                    <p class="mb-0 text-muted">Sem dados no periodo selecionado.</p>
                @endif
            </div>
        </div>
        <div class="col">
            <div class="card p-4">
                <h3 class="fs-6 mb-3 text-body-emphasis">Faturamento ultimos 6 meses</h3>
                @if($hasRevenueByMonth)
                    <div class="position-relative" style="height: 260px;">
                        <canvas id="chart-revenue-by-month"></canvas>
                    </div>
                @else
                    <p class="mb-0 text-muted">Sem dados no periodo selecionado.</p>
                @endif
            </div>
        </div>
        <div class="col">
            <div class="card p-4">
                <h3 class="fs-6 mb-3 text-body-emphasis">Metodos de pagamento</h3>
                @if($hasPaymentMethods)
                    <div class="position-relative" style="height: 260px;">
                        <canvas id="chart-payment-methods"></canvas>
                    </div>
                @else
                    <p class="mb-0 text-muted">Sem dados no periodo selecionado.</p>
                @endif
            </div>
        </div>
        <div class="col">
            <div class="card p-4">
                <h3 class="fs-6 mb-3 text-body-emphasis">Status das vendas</h3>
                @if($hasSalesStatus)
                    <div class="position-relative" style="height: 260px;">
                        <canvas id="chart-sales-status"></canvas>
                    </div>
                @else
                    <p class="mb-0 text-muted">Sem dados no periodo selecionado.</p>
                @endif
            </div>
        </div>
        <div class="col">
            <div class="card p-4">
                <h3 class="fs-6 mb-3 text-body-emphasis">Top procedimentos (quantidade)</h3>
                @if($hasTopProcedures)
                    <div class="position-relative" style="height: 260px;">
                        <canvas id="chart-top-procedures"></canvas>
                    </div>
                @else
                    <p class="mb-0 text-muted">Sem dados no periodo selecionado.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="card p-4">
        <h3 class="fs-6 mb-3 text-body-emphasis">Top procedimentos por faturamento</h3>
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                <tr>
                    <th>Procedimento</th>
                    <th>Quantidade</th>
                    <th>Faturamento</th>
                </tr>
                </thead>
                <tbody>
                @forelse(($charts['top_procedures'] ?? []) as $item)
                    <tr>
                        <td>{{ $item['label'] }}</td>
                        <td>{{ number_format($item['total_qty'] ?? 0, 0, ',', '.') }}</td>
                        <td>R$ {{ number_format($item['total_value'] ?? 0, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Sem dados para o periodo selecionado.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <small class="text-muted">Periodo: {{ $meta['start'] ?? '-' }} ate {{ $meta['end'] ?? '-' }}</small>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const dashboardData = @json($dashboardData ?? []);
            const charts = dashboardData.charts || {};

            if (typeof Chart === 'undefined') {
                console.warn('Chart.js nao foi carregado.');
                return;
            }

            function createChart(canvasId, type, labels, datasetLabel, data, options = {}) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) {
                    return;
                }

                const existingChart = Chart.getChart(canvas);
                if (existingChart) {
                    existingChart.destroy();
                }

                new Chart(canvas, {
                    type,
                    data: {
                        labels,
                        datasets: [{
                            label: datasetLabel,
                            data,
                            backgroundColor: [
                                'rgba(74, 108, 247, 0.75)',
                                'rgba(16, 185, 129, 0.75)',
                                'rgba(245, 158, 11, 0.75)',
                                'rgba(239, 68, 68, 0.75)',
                                'rgba(139, 92, 246, 0.75)',
                                'rgba(14, 165, 233, 0.75)',
                                'rgba(34, 197, 94, 0.75)',
                                'rgba(249, 115, 22, 0.75)',
                                'rgba(236, 72, 153, 0.75)',
                                'rgba(107, 114, 128, 0.75)'
                            ],
                            borderColor: 'rgba(74, 108, 247, 1)',
                            borderWidth: 1,
                            tension: 0.25,
                            fill: false,
                        }],
                    },
                    options: Object.assign({
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {display: type !== 'bar' && type !== 'line'}
                        }
                    }, options),
                });
            }

            createChart(
                'chart-schedules-by-day',
                'line',
                (charts.schedules_by_day || []).map((item) => item.label),
                'Agendamentos',
                (charts.schedules_by_day || []).map((item) => item.total)
            );

            createChart(
                'chart-schedule-status',
                'doughnut',
                (charts.schedule_status || []).map((item) => item.label),
                'Status',
                (charts.schedule_status || []).map((item) => item.total)
            );

            createChart(
                'chart-revenue-by-month',
                'bar',
                (charts.revenue_by_month || []).map((item) => item.label),
                'Faturamento (R$)',
                (charts.revenue_by_month || []).map((item) => item.total)
            );

            createChart(
                'chart-payment-methods',
                'doughnut',
                (charts.payment_methods || []).map((item) => item.label),
                'Pagamentos',
                (charts.payment_methods || []).map((item) => item.total)
            );

            createChart(
                'chart-sales-status',
                'doughnut',
                (charts.sales_status || []).map((item) => item.label),
                'Status de vendas',
                (charts.sales_status || []).map((item) => item.total)
            );

            createChart(
                'chart-top-procedures',
                'bar',
                (charts.top_procedures || []).map((item) => item.label),
                'Quantidade',
                (charts.top_procedures || []).map((item) => item.total_qty),
                {
                    indexAxis: 'y',
                    plugins: {
                        legend: {display: false}
                    }
                }
            );
        })();
    </script>
@endpush

