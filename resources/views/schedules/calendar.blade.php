@extends('layouts.header')
@section('content')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css" rel="stylesheet">

    <div class="card mb-3">
        <div class="card-body">
            <h3 class="card-title fs-5 mb-3">Calendario de Agendamentos</h3>
            <div id="schedules-calendar"></div>
        </div>
    </div>

    <div class="modal fade" id="modal-calendar-event" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalhes do Agendamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2"><strong>Paciente:</strong> <span id="event-patient">-</span></div>
                    <div class="mb-2"><strong>Procedimento:</strong> <span id="event-procedure">-</span></div>
                    <div class="mb-2"><strong>Horario:</strong> <span id="event-range">-</span></div>
                    <div class="mb-2"><strong>Status Agendamento:</strong> <span id="event-status">-</span></div>
                    <div class="mb-0"><strong>Status Pedido:</strong> <span id="event-sale-status">-</span></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <a id="event-open-schedules" href="{{ route('panel.schedules.index') }}" class="btn btn-primary">Ir para Lista</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        (function () {
            const calendarEl = document.getElementById('schedules-calendar');
            const detailsModalEl = document.getElementById('modal-calendar-event');
            const detailsModal = bootstrap.Modal.getOrCreateInstance(detailsModalEl);
            const calendarApiUrl = `{{ url('/schedule-calendar') }}`;

            function normalizeModalLayering() {
                if (detailsModalEl.parentElement !== document.body) {
                    document.body.appendChild(detailsModalEl);
                }
                detailsModalEl.style.zIndex = '1060';
                document.querySelectorAll('.modal-backdrop').forEach((backdrop) => {
                    backdrop.style.zIndex = '1050';
                });
            }

            function cleanupModalArtifacts() {
                document.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }

            detailsModalEl.addEventListener('shown.bs.modal', normalizeModalLayering);
            detailsModalEl.addEventListener('hidden.bs.modal', cleanupModalArtifacts);

            const calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'pt-br',
                initialView: 'dayGridMonth',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                nowIndicator: true,
                slotMinTime: '07:00:00',
                slotMaxTime: '20:00:00',
                scrollTime: '07:00:00',
                businessHours: {
                    daysOfWeek: [1, 2, 3, 4, 5, 6],
                    startTime: '07:00',
                    endTime: '20:00',
                },
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
                events: async function (info, successCallback, failureCallback) {
                    try {
                        const start = info.startStr.slice(0, 10);
                        const end = info.endStr.slice(0, 10);
                        const res = await fetch(`${calendarApiUrl}?start=${start}&end=${end}&limit=9999`, {
                            credentials: 'same-origin',
                            headers: { Accept: 'application/json' }
                        });

                        if (!res.ok) {
                            throw new Error('Erro ao carregar agendamentos');
                        }

                        const payload = await res.json();
                        const items = Array.isArray(payload)
                            ? payload
                            : (Array.isArray(payload?.data) ? payload.data : []);

                        const events = items
                            .filter((item) => item?.start && item?.end)
                            .map((item) => ({
                            id: item.id,
                            title: `${item.title || 'Paciente'} - ${item.description || 'Procedimento'}`,
                            start: item.start,
                            end: item.end,
                            color: item.color || '#6f42c1',
                            extendedProps: {
                                patient: item.title || '-',
                                procedure: item.description || '-',
                                range_time: item.range_time || '-',
                                status_title: item.status_title || '-',
                                saleStatus: item.saleStatus || '-',
                            }
                        }));

                        successCallback(events);
                    } catch (error) {
                        showToast(error.message || 'Erro ao carregar calendário', 'danger');
                        failureCallback(error);
                    }
                },
                eventClick: function (info) {
                    const event = info.event;
                    document.getElementById('event-patient').innerText = event.extendedProps.patient || '-';
                    document.getElementById('event-procedure').innerText = event.extendedProps.procedure || '-';
                    document.getElementById('event-range').innerText = event.extendedProps.range_time || '-';
                    document.getElementById('event-status').innerText = event.extendedProps.status_title || '-';
                    document.getElementById('event-sale-status').innerText = event.extendedProps.saleStatus || '-';

                    normalizeModalLayering();
                    detailsModal.show();
                    normalizeModalLayering();
                }
            });

            calendar.render();
        })();
    </script>
@endpush

