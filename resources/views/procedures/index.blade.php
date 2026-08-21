@extends('layouts.header')
@section('content')
    <div class="">
        <div class="card mb-3">
            <div class="card-body">
                <h3 class="card-title fs-5">Lista de Procedimentos</h3>
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Tipo de pacote</label>
                        <select id="filter-is-package" class="form-select">
                            <option value="">Todos</option>
                            <option value="1">Pacote</option>
                            <option value="0">Avulso</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo de procedimento</label>
                        <select id="filter-procedure-type" class="form-select">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select id="filter-status" class="form-select">
                            <option value="">Todos</option>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button id="btn-apply-filters" class="btn btn-primary">Filtrar</button>
                        <button id="btn-clear-filters" class="btn btn-secondary">Limpar</button>
                    </div>
                </div>
                <div class="p-1 table-responsive">
                    <table id="datatable-procedure" class="table table-bordered table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th>Atualizado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let proceduresTable;

        async function loadProcedureTypes() {
            const select = document.getElementById('filter-procedure-type');
            try {
                const res = await fetch(`{{ route('procedureTypes.index') }}?limit=1000`, { credentials: 'same-origin' });
                if (!res.ok) {
                    return;
                }
                const data = await res.json();
                const items = Array.isArray(data?.data) ? data.data : [];

                items.forEach((item) => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name;
                    select.appendChild(option);
                });
            } catch (e) {
                console.error(e);
            }
        }

        async function toggleProcedureStatus(id, inputEl) {
            const nextStatus = inputEl.checked ? 1 : 0;
            inputEl.disabled = true;

            try {
                const res = await fetch(`{{ url('/') }}/procedure-update-status/${id}`, {
                    method: 'PUT',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ status: nextStatus })
                });

                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    throw new Error(err.message || 'Erro ao atualizar status do procedimento');
                }

                showToast('Status do procedimento atualizado.', 'success');
                if (proceduresTable) {
                    proceduresTable.ajax.reload(null, false);
                }
            } catch (error) {
                inputEl.checked = !inputEl.checked;
                showToast(error.message || 'Erro ao atualizar status do procedimento', 'danger');
            } finally {
                inputEl.disabled = false;
            }
        }

        $(function () {
            proceduresTable = $('#datatable-procedure').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: function(data, callback, settings) {
                    const page = Math.floor(data.start / data.length) + 1;
                    const search = data.search.value;
                    const limit = data.length === -1 ? 0 : data.length;

                    const columnMap = {
                        0: 'id',
                        1: 'name',
                        2: 'procedure_type_name',
                        3: 'price',
                        4: 'status',
                        5: 'updated_at',
                        6: 'id'
                    };

                    const orderColumnIndex = data.order[0].column;
                    const orderDir = data.order[0].dir;
                    const orderBy = columnMap[orderColumnIndex] || 'id';

                    $.ajax({
                        url: '{{ route("procedures.index") }}',
                        method: 'GET',
                        data: {
                            limit: limit,
                            orderBy: orderBy,
                            sortedBy: orderDir,
                            page: page,
                            search: search,
                            is_package: document.getElementById('filter-is-package').value || null,
                            procedure_type_id: document.getElementById('filter-procedure-type').value || null,
                            status: document.getElementById('filter-status').value || null,
                        },
                        success: function(response) {
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
                    { data: 'name' },
                    { data: 'procedure_type_name',
                        orderable: false,
                        searchable: false
                    },
                    { data: 'credit_price',
                       render: function(data) {
                            return 'R$'+ data;
                       },
                      orderable: false,
                      searchable: false
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            const checked = Number(row.status_enum) === 1 ? 'checked' : '';
                            return `<div class="form-check form-switch ms-2 mb-2">
                                    <input class="form-check-input procedure-status-switch" type="checkbox" id="procedure-status-${row.id}" data-id="${row.id}" ${checked}>
                                    <label class="form-check-label" for="procedure-status-${row.id}">${row.status}</label>
                                </div>`;
                        }
                    },
                    {
                        data: 'updated_at',
                        render: function(data) {
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            return `
                            <button class="btn btn-sm btn-primary me-1" onclick="viewProcedure(${data})">
                              <i class="ph ph-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProcedure(${data})">
                              <i class="ph ph-trash"></i>
                            </button>
                          `;
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                }
            });

            document.getElementById('btn-apply-filters').addEventListener('click', function () {
                proceduresTable.ajax.reload();
            });

            document.getElementById('btn-clear-filters').addEventListener('click', function () {
                document.getElementById('filter-is-package').value = '';
                document.getElementById('filter-procedure-type').value = '';
                document.getElementById('filter-status').value = '';
                proceduresTable.ajax.reload();
            });

            document.getElementById('datatable-procedure').addEventListener('change', function (event) {
                const input = event.target.closest('.procedure-status-switch');
                if (!input) {
                    return;
                }
                toggleProcedureStatus(input.dataset.id, input);
            });

            loadProcedureTypes();
        });

        function viewProcedure(id) {
            window.location.href = '{{ route("panel.procedure.show", ":id") }}'.replace(':id', id);
        }

        function deleteProcedure(id) {
            console.log('Deletar procedimento com ID:', id);
        }
    </script>
@endpush
