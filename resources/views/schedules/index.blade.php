@extends('layouts.header')
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <h3 class="card-title fs-5">Lista de Agendamentos</h3>

            <div class="row g-3 mb-3">
                <div class="col-md-3 position-relative">
                    <label class="form-label">Paciente</label>
                    <input type="text" id="filter-patient-search" class="form-control" placeholder="Nome ou telefone" autocomplete="off">
                    <input type="hidden" id="filter-patient-id">
                    <div id="filter-patient-results" class="list-group position-absolute top-100 start-0 mt-1 w-100 z-3 shadow-sm"></div>
                </div>

                <div class="col-md-3 position-relative">
                    <label class="form-label">Procedimento</label>
                    <input type="text" id="filter-procedure-search" class="form-control" placeholder="Nome do procedimento" autocomplete="off">
                    <input type="hidden" id="filter-procedure-id">
                    <div id="filter-procedure-results" class="list-group position-absolute top-100 start-0 mt-1 w-100 z-3 shadow-sm"></div>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select id="filter-procedure-type" class="form-select">
                        <option value="">Todos</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select id="filter-status" class="form-select">
                        <option value="">Todos</option>
                        <option value="Marcado">Marcado</option>
                        <option value="Confirmado">Confirmado</option>
                        <option value="Adiado">Adiado</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Data inicial</label>
                    <input type="date" id="filter-start-date" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Data final</label>
                    <input type="date" id="filter-end-date" class="form-control">
                </div>

                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button id="btn-apply-filters" class="btn btn-primary">Filtrar</button>
                    <button id="btn-clear-filters" class="btn btn-secondary">Limpar</button>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card border-0 bg-primary bg-opacity-10 h-100">
                        <div class="card-body">
                            <div class="small text-muted">Total confirmado</div>
                            <div id="schedule-total-price" class="fs-5 fw-semibold text-body-emphasis">R$ 0,00</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-success bg-opacity-10 h-100">
                        <div class="card-body">
                            <div class="small text-muted">Custo confirmado</div>
                            <div id="schedule-total-cost" class="fs-5 fw-semibold text-body-emphasis">R$ 0,00</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 bg-warning bg-opacity-10 h-100">
                        <div class="card-body">
                            <div class="small text-muted">Estimado marcado</div>
                            <div id="schedule-estimate-cost" class="fs-5 fw-semibold text-body-emphasis">R$ 0,00</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-1 table-responsive">
                <table id="datatable-schedules" class="table table-bordered table-striped align-middle" style="width: 100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Procedimento</th>
                        <th>Valor</th>
                        <th>Contato</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Profissional</th>
                        <th>Pedido</th>
                        <th>Ação</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const state = {
                patientId: '',
                procedureId: ''
            };

            let table;

            function debounce(fn, delay = 350) {
                let timer = null;
                return function (...args) {
                    clearTimeout(timer);
                    timer = setTimeout(() => fn.apply(this, args), delay);
                };
            }

            function formatCurrency(value) {
                let number;

                if (typeof value === 'number') {
                    number = value;
                } else {
                    number = Number(String(value ?? 0).replace(/\./g, '').replace(',', '.'));
                }

                if (Number.isNaN(number)) {
                    return 'R$ 0,00';
                }
                return 'R$ ' + number.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function clearResults(containerId) {
                const container = document.getElementById(containerId);
                container.innerHTML = '';
            }

            function renderPatientResults(items) {
                const container = document.getElementById('filter-patient-results');
                container.innerHTML = '';

                if (!items.length) {
                    return;
                }

                items.forEach((patient) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'list-group-item list-group-item-action';
                    button.innerHTML = `
                        <div class="d-flex align-items-center gap-2">
                            <img src="${patient.photo}" alt="foto" style="width:32px;height:32px;border-radius:999px;object-fit:cover;" />
                            <div>
                                <div class="fw-semibold">${patient.name}</div>
                                <small class="text-muted">${patient.phone || '-'}</small>
                            </div>
                        </div>
                    `;
                    button.addEventListener('click', function () {
                        state.patientId = patient.id;
                        document.getElementById('filter-patient-id').value = patient.id;
                        document.getElementById('filter-patient-search').value = `${patient.name} - ${patient.phone || ''}`;
                        clearResults('filter-patient-results');
                    });
                    container.appendChild(button);
                });
            }

            function renderProcedureResults(items) {
                const container = document.getElementById('filter-procedure-results');
                container.innerHTML = '';

                if (!items.length) {
                    return;
                }

                items.forEach((procedure) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'list-group-item list-group-item-action';
                    button.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <span>${procedure.name}</span>
                            <small class="text-muted">R$ ${procedure.price}</small>
                        </div>
                    `;
                    button.addEventListener('click', function () {
                        state.procedureId = procedure.id;
                        document.getElementById('filter-procedure-id').value = procedure.id;
                        document.getElementById('filter-procedure-search').value = procedure.name;
                        clearResults('filter-procedure-results');
                    });
                    container.appendChild(button);
                });
            }

            function updateTotals(total = {}) {
                document.getElementById('schedule-total-price').innerText = formatCurrency(total.total_price || 0);
                document.getElementById('schedule-total-cost').innerText = formatCurrency(total.total_cost || 0);
                document.getElementById('schedule-estimate-cost').innerText = formatCurrency(total.estimate_cost || 0);
            }

            async function loadProcedureTypes() {
                const select = document.getElementById('filter-procedure-type');

                try {
                    const response = await fetch(`{{ route('procedureTypes.index') }}?limit=1000`, { credentials: 'same-origin' });
                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();
                    const items = Array.isArray(data?.data) ? data.data : [];

                    items.forEach((item) => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.name;
                        select.appendChild(option);
                    });
                } catch (error) {
                    console.error(error);
                }
            }

            const searchPatients = debounce(async function () {
                const term = document.getElementById('filter-patient-search').value.trim();
                if (term.length < 2) {
                    state.patientId = '';
                    document.getElementById('filter-patient-id').value = '';
                    clearResults('filter-patient-results');
                    return;
                }

                const res = await fetch(`{{ route('patients.index') }}?search=${encodeURIComponent(term)}&limit=7`, { credentials: 'same-origin' });
                if (!res.ok) {
                    clearResults('filter-patient-results');
                    return;
                }
                const data = await res.json();
                renderPatientResults(data.data || []);
            });

            const searchProcedures = debounce(async function () {
                const term = document.getElementById('filter-procedure-search').value.trim();
                if (term.length < 2) {
                    state.procedureId = '';
                    document.getElementById('filter-procedure-id').value = '';
                    clearResults('filter-procedure-results');
                    return;
                }

                const res = await fetch(`{{ route('procedures.index') }}?search=${encodeURIComponent(term)}&limit=7`, { credentials: 'same-origin' });
                if (!res.ok) {
                    clearResults('filter-procedure-results');
                    return;
                }
                const data = await res.json();
                renderProcedureResults(data.data || []);
            });

            function initializeTable() {
                table = $('#datatable-schedules').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    ajax: function (data, callback) {
                        const page = Math.floor(data.start / data.length) + 1;
                        const search = data.search.value;
                        const limit = data.length === -1 ? 15 : data.length;

                        const columnMap = {
                            0: 'id',
                            7: 'date',
                            8: 'time'
                        };

                        const orderColumnIndex = data.order?.[0]?.column ?? 0;
                        const orderDir = data.order?.[0]?.dir ?? 'desc';
                        const orderBy = columnMap[orderColumnIndex] || 'time';

                        $.ajax({
                            url: '{{ route("schedules.index") }}',
                            method: 'GET',
                            data: {
                                limit: limit,
                                page: page,
                                search: search,
                                orderBy: orderBy,
                                sortedBy: orderDir || 'desc',
                                patient_id: state.patientId || null,
                                procedure_id: state.procedureId || null,
                                procedure_type_id: document.getElementById('filter-procedure-type').value || null,
                                status: document.getElementById('filter-status').value || null,
                                start: document.getElementById('filter-start-date').value || null,
                                end: document.getElementById('filter-end-date').value || null,
                            },
                            success: function (response) {
                                updateTotals(response.total || {});
                                callback({
                                    recordsTotal: response.meta?.pagination?.total || 0,
                                    recordsFiltered: response.meta?.pagination?.total || 0,
                                    data: response.data || []
                                });
                            },
                            error: function () {
                                updateTotals({});
                                callback({
                                    recordsTotal: 0,
                                    recordsFiltered: 0,
                                    data: []
                                });
                            }
                        });
                    },
                    order: [[0, 'desc']],
                    columns: [
                        { data: 'id' },
                        { data: 'patient_name', orderable: false },
                        { data: 'procedure_name', orderable: false },
                        {
                            data: 'item_price',
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: 'phone',
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row) {
                                if (!data) {
                                    return '-';
                                }

                                const whatsapp = row.phone_link
                                    ? `https://wa.me/${String(row.phone_link).replace(/\D/g, '')}`
                                    : null;

                                return `
                                    <div class="d-flex flex-column gap-1">
                                        <span>${data}</span>
                                        ${whatsapp ? `<a href="${whatsapp}" target="_blank" class="text-decoration-none small">WhatsApp</a>` : ''}
                                    </div>
                                `;
                            }
                        },
                        {
                            data: 'status_title',
                            orderable: false,
                            searchable: false
                        },
                        { data: 'date' },
                        { data: 'time' },
                        {
                            data: 'professional',
                            orderable: false,
                            searchable: false,
                            render: function (data) {
                                return data || '-';
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row) {
                                if (!row.sale_id) {
                                    return '-';
                                }

                                return `${row.saleStatus || '-'}`;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row) {
                                const buttons = [];

                                if (row.sale_id) {
                                    buttons.push(`<a href="/panel-sales-orders-edit/${row.sale_id}" class="btn btn-sm btn-outline-primary" title="Abrir pedido"><i class="ph ph-shopping-bag-open"></i></a>`);
                                }

                                if (row.phone_link) {
                                    buttons.push(`<a href="https://wa.me/${String(row.phone_link).replace(/\D/g, '')}" target="_blank" class="btn btn-sm btn-outline-success" title="WhatsApp"><i class="ph ph-whatsapp-logo"></i></a>`);
                                }

                                return buttons.length ? `<div class="d-flex gap-1">${buttons.join('')}</div>` : '-';
                            }
                        }
                    ],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                    }
                });
            }

            document.getElementById('filter-patient-search').addEventListener('keyup', searchPatients);
            document.getElementById('filter-procedure-search').addEventListener('keyup', searchProcedures);

            document.getElementById('btn-apply-filters').addEventListener('click', function () {
                table.ajax.reload();
            });

            document.getElementById('btn-clear-filters').addEventListener('click', function () {
                state.patientId = '';
                state.procedureId = '';
                document.getElementById('filter-patient-search').value = '';
                document.getElementById('filter-procedure-search').value = '';
                document.getElementById('filter-patient-id').value = '';
                document.getElementById('filter-procedure-id').value = '';
                document.getElementById('filter-procedure-type').value = '';
                document.getElementById('filter-status').value = '';
                document.getElementById('filter-start-date').value = '';
                document.getElementById('filter-end-date').value = '';
                clearResults('filter-patient-results');
                clearResults('filter-procedure-results');
                table.ajax.reload();
            });

            document.addEventListener('click', function (event) {
                const patientBox = document.getElementById('filter-patient-results');
                const patientInput = document.getElementById('filter-patient-search');
                if (!patientBox.contains(event.target) && event.target !== patientInput) {
                    clearResults('filter-patient-results');
                }

                const procedureBox = document.getElementById('filter-procedure-results');
                const procedureInput = document.getElementById('filter-procedure-search');
                if (!procedureBox.contains(event.target) && event.target !== procedureInput) {
                    clearResults('filter-procedure-results');
                }
            });

            loadProcedureTypes();
            initializeTable();
        })();
    </script>
@endpush

