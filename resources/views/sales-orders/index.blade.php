@extends('layouts.header')
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <h3 class="card-title fs-5">Lista de Pedidos</h3>

            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Paciente</label>
                    <input type="text" id="filter-patient-search" class="form-control" placeholder="Nome ou telefone" autocomplete="off">
                    <input type="hidden" id="filter-patient-id">
                    <div id="filter-patient-results" class="list-group position-absolute w-25 z-3"></div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Procedimento</label>
                    <input type="text" id="filter-procedure-search" class="form-control" placeholder="Nome do procedimento" autocomplete="off">
                    <input type="hidden" id="filter-procedure-id">
                    <div id="filter-procedure-results" class="list-group position-absolute w-25 z-3"></div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select id="filter-status" class="form-select">
                        <option value="">Todos</option>
                        <option value="0">Inicial</option>
                        <option value="1">Pago</option>
                        <option value="2">Cancelado</option>
                        <option value="3">Parcial</option>
                        <option value="4">Finalizado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Pagamento</label>
                    <select id="filter-type-payment" class="form-select">
                        <option value="">Todos</option>
                        <option value="1">PIX</option>
                        <option value="2">Cartao de Credito</option>
                        <option value="3">Cartao de Debito</option>
                        <option value="4">Dinheiro</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Inicio</label>
                    <input type="date" id="filter-start-date" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fim</label>
                    <input type="date" id="filter-final-date" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button id="btn-apply-filters" class="btn btn-primary">Filtrar</button>
                    <button id="btn-clear-filters" class="btn btn-secondary">Limpar</button>
                </div>
            </div>

            <div class="mb-2 fw-semibold">Total pago filtrado: <span id="orders-total-price">R$ 0,00</span></div>

            <div class="p-1 table-responsive">
                <table id="datatable-sales-orders" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Paciente</th>
                        <th>Valor</th>
                        <th>Pagamento</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Acao</th>
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
                        <div class="d-flex justify-content-between align-items-center">
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
                table = $('#datatable-sales-orders').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    ajax: function (data, callback) {
                        const page = Math.floor(data.start / data.length) + 1;
                        const search = data.search.value;
                        const limit = data.length === -1 ? 15 : data.length;

                        const columnMap = {
                            0: 'id',
                            1: 'id',
                            2: 'amount',
                            3: 'type_payment',
                            4: 'status',
                            5: 'created_at'
                        };

                        const orderColumnIndex = data.order[0].column;
                        const orderDir = data.order[0].dir;
                        const orderBy = columnMap[orderColumnIndex] || 'id';

                        $.ajax({
                            url: '{{ route("salesOrders.index") }}',
                            method: 'GET',
                            data: {
                                limit: limit,
                                page: page,
                                search: search,
                                orderBy: orderBy,
                                sortedBy: orderDir,
                                patient_id: state.patientId || null,
                                procedure_id: state.procedureId || null,
                                status: document.getElementById('filter-status').value || null,
                                type_payment: document.getElementById('filter-type-payment').value || null,
                                start_date: document.getElementById('filter-start-date').value || null,
                                final_date: document.getElementById('filter-final-date').value || null,
                            },
                            success: function (response) {
                                document.getElementById('orders-total-price').innerText = 'R$ ' + (response.total?.total_price || '0,00');
                                callback({
                                    recordsTotal: response.meta.pagination.total,
                                    recordsFiltered: response.meta.pagination.total,
                                    data: response.data
                                });
                            }
                        });
                    },
                    columns: [
                        { data: 'id' },
                        { data: 'patient_name', orderable: false },
                        {
                            data: 'amount',
                            orderable: false,
                            searchable: false,
                            render: function (data) {
                                return `R$ ${data}`;
                            }
                        },
                        {
                            data: 'type_payment_title',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status_title',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'date',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data) {
                                return `<a href="/panel-sales-orders-edit/${data}" class="btn btn-sm btn-outline-primary"><i class="ph ph-pencil-simple-line"></i></a>`;
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
                document.getElementById('filter-status').value = '';
                document.getElementById('filter-type-payment').value = '';
                document.getElementById('filter-start-date').value = '';
                document.getElementById('filter-final-date').value = '';
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

            initializeTable();
        })();
    </script>
@endpush

