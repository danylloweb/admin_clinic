@extends('layouts.header')
@section('content')
<style>
    select option {
        background-color: #fff;
        color: #212529;
    }
</style>
    <div class="card mb-3">
        <div class="card-body">
            <h3 class="card-title fs-5">Lista de Agendamentos</h3>

            <div class="row g-3 mb-3">
                <div class="col-md-3 position-relative">
                    <label class="form-label">Paciente</label>
                    <input type="text" id="filter-patient-search" class="form-control" placeholder="Nome ou telefone" autocomplete="off">
                    <input type="hidden" id="filter-patient-id">
                    <div id="filter-patient-results" class="list-group position-absolute top-100 start-0 mt-1 w-100 z-3 shadow-sm" style="background:#2a2a3a;"></div>
                </div>

                <div class="col-md-3 position-relative">
                    <label class="form-label">Procedimento</label>
                    <input type="text" id="filter-procedure-search" class="form-control" placeholder="Nome do procedimento" autocomplete="off">
                    <input type="hidden" id="filter-procedure-id">
                    <div id="filter-procedure-results" class="list-group position-absolute top-100 start-0 mt-1 w-100 z-3 shadow-sm" style="background:#2a2a3a;"></div>
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
                    <input type="date" id="filter-start-date" class="form-control" value="{{ now()->format('Y-m-d') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">Data final</label>
                    <input type="date" id="filter-end-date" class="form-control" value="{{ now()->format('Y-m-d') }}">
                </div>

                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button id="btn-apply-filters" class="btn btn-primary">Filtrar</button>
                    <button id="btn-clear-filters" class="btn btn-secondary">Limpar</button>
                    <button id="btn-print-schedules" class="btn btn-info" title="Visualizar e imprimir agendamentos"><i class="ph ph-printer me-1"></i>Imprimir</button>
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
                        <th>Foto</th>
                        <th>Paciente</th>
                        <th>Contato</th>
                        <th>Procedimento</th>
                        <th>Valor</th>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Status</th>
                        <th>Pedido</th>
                        <th>Ação</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-print-schedules" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Imprimir Agendamentos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body" id="print-schedules-content" style="max-height: 70vh; overflow-y: auto;">
                    <!-- Conteúdo preenchido dinamicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="btn-execute-print" onclick="window.print()"><i class="ph ph-printer me-1"></i>Imprimir</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-confirm-attendance" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Atendimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="confirm-schedule-id">

                    <div class="mb-3">
                        <label class="form-label">Paciente</label>
                        <input type="text" id="confirm-schedule-patient" class="form-control" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Procedimento</label>
                        <input type="text" id="confirm-schedule-procedure" class="form-control" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Profissional</label>
                        <select id="confirm-professional-id" class="form-select" required>
                            <option value="">Selecione...</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label">Observação (opcional)</label>
                        <textarea id="confirm-observation-status" class="form-control" rows="3" placeholder="Observação da confirmação"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btn-save-confirm-attendance">Confirmar atendimento</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-edit-schedule" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Agendamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-schedule-id">

                    <div class="mb-3">
                        <label class="form-label">Paciente</label>
                        <input type="text" id="edit-schedule-patient-search" class="form-control" placeholder="Nome ou telefone" autocomplete="off">
                        <input type="hidden" id="edit-schedule-patient-id">
                        <div id="edit-schedule-patient-results" class="list-group position-absolute mt-1 w-100 z-3 shadow-sm" style="background:#2a2a3a;"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Procedimento</label>
                        <input type="text" id="edit-schedule-procedure-search" class="form-control" placeholder="Buscar procedimento" autocomplete="off">
                        <input type="hidden" id="edit-schedule-procedure-id">
                        <div id="edit-schedule-procedure-results" class="list-group position-absolute mt-1 w-100 z-3 shadow-sm" style="background:#2a2a3a;"></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Data</label>
                            <input type="date" id="edit-schedule-date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hora</label>
                            <input type="time" id="edit-schedule-time" class="form-control">
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select id="edit-schedule-status" class="form-select">
                                <option value="Marcado">Marcado</option>
                                <option value="Confirmado">Confirmado</option>
                                <option value="Adiado">Adiado</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Profissional</label>
                            <select id="edit-schedule-professional-id" class="form-select">
                                <option value="">Selecione...</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Observação</label>
                        <textarea id="edit-schedule-observation-status" class="form-control" rows="3" placeholder="Observação do agendamento"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-edit-schedule">Salvar alterações</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-edit-payment" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Pagamento do Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
                    <input type="hidden" id="edit-payment-order-id">
                    <input type="hidden" id="edit-payment-schedule-id">

                    <div class="row g-4">
                        <!-- Left side: Order info and items -->
                        <div class="col-lg-6">
                            <h6 class="fw-semibold mb-3">Informações do Pedido</h6>

                            <div class="mb-3">
                                <label class="form-label">Paciente</label>
                                <input type="text" id="edit-payment-patient-name" class="form-control" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select id="edit-payment-status" class="form-select">
                                    <option value="0">Inicial</option>
                                    <option value="1">Pago</option>
                                    <option value="2">Cancelado</option>
                                    <option value="3">Parcial</option>
                                    <option value="4">Finalizado</option>
                                </select>
                            </div>

                            <h6 class="fw-bold mb-3 mt-4">Itens do Pedido</h6>
                            <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                                <table class="table table-sm table-bordered">
                                    <thead class="table">
                                        <tr>
                                            <th>Procedimento</th>
                                            <th>Qtd</th>
                                            <th>Valor</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="edit-payment-items-body">
                                        <tr><td colspan="4" class="text-center text-muted">Carregando...</td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-light mt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <strong>Subtotal:</strong>
                                    <span id="edit-payment-subtotal">R$ 0,00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right side: Payment editor -->
                        <div class="col-lg-6">
                            <h6 class="fw-semibold mb-3">Dados de Pagamento</h6>

                            <div class="mb-3">
                                <label class="form-label">Tipo de Pagamento</label>
                                <select id="edit-payment-type" class="form-select" onchange="recalculateSalePayment()">
                                    <option value="1">PIX</option>
                                    <option value="2">Cartão de Crédito</option>
                                    <option value="3">Cartão de Débito</option>
                                    <option value="4">Dinheiro</option>
                                </select>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Bandeira do Cartão</label>
                                    <select id="edit-payment-brand" class="form-select" onchange="recalculateSalePayment()">
                                        <option value="1">MasterCard</option>
                                        <option value="2">Visa</option>
                                        <option value="3">Elo</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Parcelas</label>
                                    <select id="edit-payment-installments" class="form-select" onchange="onPaymentInstallmentsChange()">
                                        <option value="1">1x</option>
                                        <option value="2">2x</option>
                                        <option value="3">3x</option>
                                        <option value="4">4x</option>
                                        <option value="5">5x</option>
                                        <option value="6">6x</option>
                                        <option value="7">7x</option>
                                        <option value="8">8x</option>
                                        <option value="9">9x</option>
                                        <option value="10">10x</option>
                                    </select>
                                </div>
                            </div>

                            <div class="card mt-4 bg-light border-0">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3 text-black">Valores Calculados</h6>
                                    <div class="row g-2 text-black">
                                        <div class="col-6">
                                            <small class="text-black">PIX:</small>
                                            <div class="fw-bold" id="edit-payment-pix">R$ 0,00</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-black">Débito:</small>
                                            <div class="fw-bold" id="edit-payment-debit">R$ 0,00</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-black">Total Crédito:</small>
                                            <div class="fw-bold text-primary" id="edit-payment-credit-total">R$ 0,00</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-black">Valor da Parcela:</small>
                                            <div class="fw-bold text-success" id="edit-payment-installment">R$ 0,00</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-edit-payment">Salvar Alterações</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const state = {
                patientId: '',
                procedureId: '',
                editProcedureId: '',
                editPatientId: ''
            };

            let table;
            let attendanceModal;
            let editScheduleModal;

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
                if (container) {
                    container.innerHTML = '';
                }
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
                                <div class="text-white">${patient.name}</div>
                                <small class="text-white">${patient.phone || '-'}</small>
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

            function renderProcedureResults(items, options = {}) {
                const {
                    containerId = 'filter-procedure-results',
                    onSelect = null,
                } = options;

                const container = document.getElementById(containerId);
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
                            <span class="text-white">${procedure.name}</span>
                            <small class="text-white">R$ ${procedure.price}</small>
                        </div>
                    `;
                    button.addEventListener('click', function () {
                        if (typeof onSelect === 'function') {
                            onSelect(procedure);
                        }
                    });
                    container.appendChild(button);
                });
            }

            function updateTotals(total = {}) {
                document.getElementById('schedule-total-price').innerText = formatCurrency(total.total_price || 0);
                document.getElementById('schedule-total-cost').innerText = formatCurrency(total.total_cost || 0);
                document.getElementById('schedule-estimate-cost').innerText = formatCurrency(total.estimate_cost || 0);
            }

            function normalizeModalLayering(modalId) {
                const modalEl = document.getElementById(modalId);
                if (!modalEl) {
                    return;
                }

                if (modalEl.parentElement !== document.body) {
                    document.body.appendChild(modalEl);
                }

                modalEl.style.zIndex = '1060';
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

            async function apiPut(url, payload) {
                const response = await fetch(url, {
                    method: 'PUT',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const error = await response.json().catch(() => ({}));
                    throw new Error(error.message || 'Erro ao salvar confirmação');
                }

                return response.json();
            }

            async function loadProfessionals() {
                const selects = [
                    document.getElementById('confirm-professional-id'),
                    document.getElementById('edit-schedule-professional-id')
                ].filter(Boolean);

                selects.forEach((select) => {
                    select.innerHTML = '<option value="">Selecione...</option>';
                });

                try {
                    const response = await fetch(`{{ route('users.index') }}?has_medical=Sim&limit=200`, { credentials: 'same-origin' });
                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();
                    const users = Array.isArray(data?.data) ? data.data : [];

                    users.forEach((user) => {
                        selects.forEach((select) => {
                            const option = document.createElement('option');
                            option.value = user.id;
                            option.textContent = user.name;
                            select.appendChild(option);
                        });
                    });
                } catch (error) {
                    console.error(error);
                }
            }

            function openPrintModal() {
                // Build URL with current filters
                const params = new URLSearchParams();

                if (state.patientId) {
                    params.append('patient_id', state.patientId);
                }

                if (state.procedureId) {
                    params.append('procedure_id', state.procedureId);
                }

                const startDate = document.getElementById('filter-start-date').value;
                if (startDate) {
                    params.append('start', startDate);
                }

                const endDate = document.getElementById('filter-end-date').value;
                if (endDate) {
                    params.append('end', endDate);
                }

                const status = document.getElementById('filter-status').value;
                if (status) {
                    params.append('status', status);
                }

                const procedureType = document.getElementById('filter-procedure-type').value;
                if (procedureType) {
                    params.append('procedure_type_id', procedureType);
                }

                const url = `{{ route('panel.schedules.print') }}?${params.toString()}`;
                window.open(url, '_blank');
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

            function openConfirmAttendanceModal(row) {
                document.getElementById('confirm-schedule-id').value = row.id;
                document.getElementById('confirm-schedule-patient').value = row.patient_name || '-';
                document.getElementById('confirm-schedule-procedure').value = row.procedure_name || '-';
                document.getElementById('confirm-professional-id').value = '';
                document.getElementById('confirm-observation-status').value = '';
                normalizeModalLayering('modal-confirm-attendance');
                attendanceModal.show();
                normalizeModalLayering('modal-confirm-attendance');
            }

            function syncEditProfessionalRequirement() {
                const status = document.getElementById('edit-schedule-status').value;
                const professionalSelect = document.getElementById('edit-schedule-professional-id');
                professionalSelect.required = status === 'Confirmado';
            }

            function openEditScheduleModal(row) {
                document.getElementById('edit-schedule-id').value = row.id;
                state.editPatientId = row.patient_id || '';
                document.getElementById('edit-schedule-patient-id').value = row.patient_id || '';
                document.getElementById('edit-schedule-patient-search').value = row.patient_name || '';
                state.editProcedureId = row.procedure_id || '';
                document.getElementById('edit-schedule-procedure-id').value = row.procedure_id || '';
                document.getElementById('edit-schedule-procedure-search').value = row.procedure_name || '';
                document.getElementById('edit-schedule-date').value = row.date_real || '';
                document.getElementById('edit-schedule-time').value = String(row.time || '').slice(0, 5);
                document.getElementById('edit-schedule-status').value = row.status || 'Marcado';
                document.getElementById('edit-schedule-professional-id').value = row.professional_id || '';
                document.getElementById('edit-schedule-observation-status').value = row.observation_status || '';
                clearResults('edit-schedule-procedure-results');
                clearResults('edit-schedule-patient-results');
                syncEditProfessionalRequirement();
                normalizeModalLayering('modal-edit-schedule');
                editScheduleModal.show();
                normalizeModalLayering('modal-edit-schedule');
            }

            function getTaxByMasterCard() {
                return { 1: 0.0449, 2: 0.0999, 3: 0.1199, 4: 0.1299, 5: 0.1399, 6: 0.1499, 7: 0.1599, 8: 0.1699, 9: 0.1799, 10: 0.1899 };
            }

            function getTaxByEloCard() {
                return { 1: 0.0568, 2: 0.1138, 3: 0.1338, 4: 0.1438, 5: 0.1538, 6: 0.1638, 7: 0.1738, 8: 0.1838, 9: 0.1938, 10: 0.2038 };
            }

            function getPaymentTax(brand, installments) {
                const map = Number(brand) < 2 ? getTaxByMasterCard() : getTaxByEloCard();
                return map[Number(installments)] || 0;
            }

            function getDebitTax(brand) {
                return Number(brand) <= 2 ? 0.0269 : 0.0388;
            }

            function formatMoneyValue(value) {
                const amount = parseMoneyValue(value);
                return 'R$ ' + amount.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function parseMoneyValue(value) {
                if (typeof value === 'number') {
                    return Number.isFinite(value) ? value : 0;
                }

                const normalized = String(value ?? '')
                    .replace(/\./g, '')
                    .replace(',', '.')
                    .replace(/[^0-9.-]/g, '');

                const parsed = parseFloat(normalized);
                return Number.isFinite(parsed) ? parsed : 0;
            }

            function recalculateSalePayment() {
                const typePayment = Number(document.getElementById('edit-payment-type').value || 1);
                const brand = Number(document.getElementById('edit-payment-brand').value || 1);
                const installments = Number(document.getElementById('edit-payment-installments').value || 1);
                const subtotal = Number(window.currentPaymentSubtotal || 0);

                const pixAmount = subtotal >= 250 ? subtotal - (subtotal * 0.05) : subtotal;
                const debitTax = getDebitTax(brand);
                const debitAmount = subtotal + (subtotal * debitTax);
                const installmentTax = getPaymentTax(brand, installments);
                const installmentBase = installments > 0 ? (subtotal / installments) : subtotal;
                const installmentAmount = installmentBase + (installmentBase * installmentTax);
                const creditTotal = installmentAmount * installments;

                document.getElementById('edit-payment-pix').innerText = formatMoneyValue(pixAmount);
                document.getElementById('edit-payment-debit').innerText = formatMoneyValue(debitAmount);
                document.getElementById('edit-payment-credit-total').innerText = formatMoneyValue(creditTotal);
                document.getElementById('edit-payment-installment').innerText = formatMoneyValue(installmentAmount);
            }

            function onPaymentInstallmentsChange() {
                const installments = Number(document.getElementById('edit-payment-installments').value || 1);
                if (installments > 1) {
                    document.getElementById('edit-payment-type').value = '2';
                }
                recalculateSalePayment();
            }

            async function openEditPaymentModal(scheduleId, saleId) {
                document.getElementById('edit-payment-order-id').value = saleId;
                document.getElementById('edit-payment-schedule-id').value = scheduleId;

                try {
                    const response = await fetch(`{{ url('/') }}/salesOrders/${saleId}`, { credentials: 'same-origin' });
                    if (!response.ok) throw new Error('Erro ao carregar pedido');

                    const data = await response.json();
                    const order = data.data;

                    // Load patient name and status
                    document.getElementById('edit-payment-patient-name').value = order.patient_name || '-';
                    document.getElementById('edit-payment-status').value = order.status || 0;

                    // Load order items
                    const itemsBody = document.getElementById('edit-payment-items-body');
                    const items = Array.isArray(order.items) ? order.items : [];

                    itemsBody.innerHTML = items.map(item => {
                        const unitPrice = parseMoneyValue(item.price);
                        const lineTotal = item.price_total !== undefined
                            ? parseMoneyValue(item.price_total)
                            : unitPrice * (item.qty || 0);

                        return `
                        <tr>
                            <td>${item.procedure_name || '-'}</td>
                            <td>${item.qty || 0}</td>
                            <td>${formatMoneyValue(unitPrice)}</td>
                            <td>${formatMoneyValue(lineTotal)}</td>
                        </tr>
                    `;
                    }).join('');

                    if (!items.length) {
                        itemsBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Nenhum item encontrado</td></tr>';
                    }

                    const subtotal = items.reduce((sum, item) => {
                        if (item.price_total !== undefined) {
                            return sum + parseMoneyValue(item.price_total);
                        }

                        return sum + (parseMoneyValue(item.price) * (item.qty || 0));
                    }, 0);
                    window.currentPaymentSubtotal = subtotal;
                    document.getElementById('edit-payment-subtotal').innerText = formatMoneyValue(subtotal);

                    // Set current payment info
                    document.getElementById('edit-payment-type').value = order.type_payment || 1;
                    document.getElementById('edit-payment-brand').value = order.brand_card || 1;
                    document.getElementById('edit-payment-installments').value = order.qty_installments || 1;

                    normalizeModalLayering('modal-edit-payment');
                    const modal = new bootstrap.Modal(document.getElementById('modal-edit-payment'));
                    modal.show();
                    normalizeModalLayering('modal-edit-payment');

                    recalculateSalePayment();
                } catch (error) {
                    showToast('Erro ao carregar dados do pagamento: ' + error.message, 'danger');
                }
            }

            async function savePaymentChanges() {
                const orderId = document.getElementById('edit-payment-order-id').value;
                const typePayment = Number(document.getElementById('edit-payment-type').value);
                const brandCard = Number(document.getElementById('edit-payment-brand').value);
                const qtyInstallments = Number(document.getElementById('edit-payment-installments').value);
                const status = Number(document.getElementById('edit-payment-status').value);
                const saveButton = document.getElementById('btn-save-edit-payment');

                const previousText = saveButton.innerText;
                saveButton.disabled = true;
                saveButton.innerText = 'Salvando...';

                try {
                    const response = await fetch(`{{ url('/') }}/salesOrders/${orderId}`, {
                        method: 'PUT',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            type_payment: typePayment,
                            brand_card: brandCard,
                            qty_installments: qtyInstallments,
                            status: status
                        })
                    });

                    if (!response.ok) {
                        const error = await response.json().catch(() => ({}));
                        throw new Error(error.message || 'Erro ao salvar');
                    }

                    showToast('Pagamento alterado com sucesso', 'success');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modal-edit-payment'));
                    if (modal) modal.hide();
                    table.ajax.reload();
                } catch (error) {
                    showToast('Erro: ' + error.message, 'danger');
                } finally {
                    saveButton.disabled = false;
                    saveButton.innerText = previousText;
                }
            }

            async function saveScheduleEdition() {
                const scheduleId = document.getElementById('edit-schedule-id').value;
                const patientId = document.getElementById('edit-schedule-patient-id').value;
                const procedureId = document.getElementById('edit-schedule-procedure-id').value;
                const date = document.getElementById('edit-schedule-date').value;
                const time = document.getElementById('edit-schedule-time').value;
                const status = document.getElementById('edit-schedule-status').value;
                const professionalId = document.getElementById('edit-schedule-professional-id').value;
                const observationStatus = document.getElementById('edit-schedule-observation-status').value || '';
                const saveButton = document.getElementById('btn-save-edit-schedule');

                if (!patientId) {
                    showToast('Selecione o paciente do agendamento.', 'danger');
                    return;
                }

                if (!procedureId) {
                    showToast('Selecione o procedimento do agendamento.', 'danger');
                    return;
                }

                if (!date || !time) {
                    showToast('Preencha data e hora do agendamento.', 'danger');
                    return;
                }

                if (status === 'Confirmado' && !professionalId) {
                    showToast('Selecione um profissional para status confirmado.', 'danger');
                    return;
                }

                const previousText = saveButton.innerText;
                saveButton.disabled = true;
                saveButton.innerText = 'Salvando...';

                try {
                    await apiPut(`{{ url('/') }}/schedules/${scheduleId}`, {
                        patient_id: Number(patientId),
                        procedure_id: Number(procedureId),
                        date,
                        time,
                        status,
                        professional_id: professionalId ? Number(professionalId) : null,
                        observation_status: observationStatus,
                    });

                    if (status === 'Confirmado') {
                        await apiPut(`{{ url('/') }}/schedule/update-status/${scheduleId}`, {
                            status,
                            professional_id: Number(professionalId),
                            observation_status: observationStatus,
                        });
                    }

                    showToast('Agendamento atualizado com sucesso.', 'success');
                    editScheduleModal.hide();
                    table.ajax.reload();
                } catch (error) {
                    showToast(error.message || 'Erro ao editar agendamento.', 'danger');
                } finally {
                    saveButton.disabled = false;
                    saveButton.innerText = previousText;
                }
            }

            async function saveAttendanceConfirmation() {
                const scheduleId = document.getElementById('confirm-schedule-id').value;
                const professionalId = document.getElementById('confirm-professional-id').value;
                const observationStatus = document.getElementById('confirm-observation-status').value || '';
                const saveButton = document.getElementById('btn-save-confirm-attendance');

                if (!professionalId) {
                    showToast('Selecione um profissional para confirmar.', 'danger');
                    return;
                }

                const previousText = saveButton.innerText;
                saveButton.disabled = true;
                saveButton.innerText = 'Confirmando...';

                try {
                    await apiPut(`{{ url('/') }}/schedule/update-status/${scheduleId}`, {
                        status: 'Confirmado',
                        professional_id: Number(professionalId),
                        observation_status: observationStatus,
                    });

                    showToast('Atendimento confirmado com sucesso.', 'success');
                    attendanceModal.hide();
                    table.ajax.reload();
                } catch (error) {
                    showToast(error.message || 'Erro ao confirmar atendimento.', 'danger');
                } finally {
                    saveButton.disabled = false;
                    saveButton.innerText = previousText;
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
                renderProcedureResults(data.data || [], {
                    containerId: 'filter-procedure-results',
                    onSelect: function (procedure) {
                        state.procedureId = procedure.id;
                        document.getElementById('filter-procedure-id').value = procedure.id;
                        document.getElementById('filter-procedure-search').value = procedure.name;
                        clearResults('filter-procedure-results');
                    }
                });
            });

            const searchEditPatients = debounce(async function () {
                const term = document.getElementById('edit-schedule-patient-search').value.trim();
                if (term.length < 2) {
                    state.editPatientId = '';
                    document.getElementById('edit-schedule-patient-id').value = '';
                    clearResults('edit-schedule-patient-results');
                    return;
                }

                const res = await fetch(`{{ route('patients.index') }}?search=${encodeURIComponent(term)}&limit=7`, { credentials: 'same-origin' });
                if (!res.ok) { clearResults('edit-schedule-patient-results'); return; }

                const data = await res.json();
                const container = document.getElementById('edit-schedule-patient-results');
                container.innerHTML = '';
                (data.data || []).forEach((patient) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'list-group-item list-group-item-action';
                    button.style.backgroundColor = '#2a2a3a';
                    button.style.color = '#fff';
                    button.style.borderColor = 'rgba(255,255,255,0.1)';
                    button.innerHTML = `
                        <div class="d-flex align-items-center gap-2">
                            <img src="${patient.photo}" alt="foto" style="width:32px;height:32px;border-radius:999px;object-fit:cover;" />
                            <div>
                                <div>${patient.name}</div>
                                <small>${patient.phone || '-'}</small>
                            </div>
                        </div>
                    `;
                    button.addEventListener('click', function () {
                        state.editPatientId = patient.id;
                        document.getElementById('edit-schedule-patient-id').value = patient.id;
                        document.getElementById('edit-schedule-patient-search').value = `${patient.name} - ${patient.phone || ''}`;
                        clearResults('edit-schedule-patient-results');
                    });
                    container.appendChild(button);
                });
            });

            const searchEditProcedures = debounce(async function () {
                const term = document.getElementById('edit-schedule-procedure-search').value.trim();
                if (term.length < 2) {
                    state.editProcedureId = '';
                    document.getElementById('edit-schedule-procedure-id').value = '';
                    clearResults('edit-schedule-procedure-results');
                    return;
                }

                const res = await fetch(`{{ route('procedures.index') }}?search=${encodeURIComponent(term)}&limit=7`, { credentials: 'same-origin' });
                if (!res.ok) {
                    clearResults('edit-schedule-procedure-results');
                    return;
                }

                const data = await res.json();
                renderProcedureResults(data.data || [], {
                    containerId: 'edit-schedule-procedure-results',
                    onSelect: function (procedure) {
                        state.editProcedureId = procedure.id;
                        document.getElementById('edit-schedule-procedure-id').value = procedure.id;
                        document.getElementById('edit-schedule-procedure-search').value = procedure.name;
                        clearResults('edit-schedule-procedure-results');
                    }
                });
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
                            5: 'date',
                            6: 'time'
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
                        { data: 'photo',
                            render: function (data) {
                                return `<img src="${data}" data-src="${data}" alt="imagem" class="img-clickable" style="width:60px; height:auto; border-radius:5px; cursor:pointer;"/>`;
                            },
                            orderable: false,
                            searchable: false
                        },
                        { data: 'patient_name', orderable: false },
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
                                        ${whatsapp ? `<a href="${whatsapp}" target="_blank" class="text-decoration-success small">WhatsApp</a>` : ''}
                                    </div>
                                `;
                            }
                        },
                        { data: 'procedure_name', orderable: false },
                        {
                            data: 'price',
                            orderable: false,
                            searchable: false,
                            render: function(data) {
                                return 'R$ ' + parseFloat(data).toFixed(2).replace('.', ',');
                            },
                        },
                        { data: 'date' },
                        { data: 'time' },
                        {
                            data: 'status_title',
                            orderable: false,
                            searchable: false
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

                                buttons.push(`<button type="button" class="btn btn-sm btn-outline-secondary btn-edit-schedule" data-id="${row.id}" title="Editar agendamento"><i class="ph ph-pencil-simple-line"></i></button>`);
                                buttons.push(`<a href="/panel-attendances-open-by-schedule/${row.id}" class="btn btn-sm btn-outline-info" title="Abrir Atendimento"><i class="ph ph-clipboard-text"></i></a>`);
                                buttons.push(`<a href="/panel-patients-show/${row.patient_id}" class="btn btn-sm btn-outline-primary" target="_blank" title="Editar Paciente"><i class="ph ph-user"></i></a>`);

                                if (row.status !== 'Confirmado') {
                                    buttons.push(`<button type="button" class="btn btn-sm btn-success btn-confirm-attendance" data-id="${row.id}" title="Confirmar atendimento"><i class="ph ph-check"></i></button>`);
                                }

                                if (row.sale_id) {
                                    buttons.push(`<button type="button" class="btn btn-sm btn-warning btn-edit-payment" data-id="${row.id}" data-sale-id="${row.sale_id}" title="Editar pagamento"><i class="ph ph-currency-circle-dollar"></i></button>`);
                                    buttons.push(`<a href="/panel-sales-orders-edit/${row.sale_id}" class="btn btn-sm btn-outline-primary" target="_blank" title="Abrir pedido"><i class="ph ph-shopping-bag-open"></i></a>`);
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
            document.getElementById('edit-schedule-patient-search').addEventListener('keyup', searchEditPatients);
            document.getElementById('edit-schedule-procedure-search').addEventListener('keyup', searchEditProcedures);
            document.getElementById('btn-save-confirm-attendance').addEventListener('click', saveAttendanceConfirmation);
            document.getElementById('btn-save-edit-schedule').addEventListener('click', saveScheduleEdition);
            document.getElementById('edit-schedule-status').addEventListener('change', syncEditProfessionalRequirement);
            document.getElementById('btn-print-schedules').addEventListener('click', openPrintModal);
            document.getElementById('btn-save-edit-payment').addEventListener('click', savePaymentChanges);
            document.getElementById('modal-confirm-attendance').addEventListener('shown.bs.modal', function () { normalizeModalLayering('modal-confirm-attendance'); });
            document.getElementById('modal-confirm-attendance').addEventListener('hidden.bs.modal', cleanupModalArtifacts);
            document.getElementById('modal-edit-schedule').addEventListener('shown.bs.modal', function () { normalizeModalLayering('modal-edit-schedule'); });
            document.getElementById('modal-edit-schedule').addEventListener('hidden.bs.modal', cleanupModalArtifacts);
            document.getElementById('modal-edit-payment').addEventListener('shown.bs.modal', function () { normalizeModalLayering('modal-edit-payment'); });
            document.getElementById('modal-edit-payment').addEventListener('hidden.bs.modal', cleanupModalArtifacts);

            document.getElementById('datatable-schedules').addEventListener('click', function (event) {
                const confirmButton = event.target.closest('.btn-confirm-attendance');
                const editButton = event.target.closest('.btn-edit-schedule');
                const editPaymentButton = event.target.closest('.btn-edit-payment');

                if (editButton) {
                    const rowData = table.row(editButton.closest('tr')).data();
                    if (!rowData) {
                        showToast('Não foi possível carregar os dados do agendamento.', 'danger');
                        return;
                    }

                    openEditScheduleModal(rowData);
                    return;
                }

                if (editPaymentButton) {
                    const scheduleId = editPaymentButton.getAttribute('data-id');
                    const saleId = editPaymentButton.getAttribute('data-sale-id');
                    openEditPaymentModal(scheduleId, saleId);
                    return;
                }

                if (!confirmButton) {
                    return;
                }

                const rowData = table.row(confirmButton.closest('tr')).data();
                if (!rowData) {
                    showToast('Não foi possível carregar os dados do agendamento.', 'danger');
                    return;
                }

                openConfirmAttendanceModal(rowData);
            });

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

                const editProcedureBox = document.getElementById('edit-schedule-procedure-results');
                const editProcedureInput = document.getElementById('edit-schedule-procedure-search');
                if (!editProcedureBox.contains(event.target) && event.target !== editProcedureInput) {
                    clearResults('edit-schedule-procedure-results');
                }

                const editPatientBox = document.getElementById('edit-schedule-patient-results');
                const editPatientInput = document.getElementById('edit-schedule-patient-search');
                if (!editPatientBox.contains(event.target) && event.target !== editPatientInput) {
                    clearResults('edit-schedule-patient-results');
                }
            });

            loadProcedureTypes();
            loadProfessionals();
            normalizeModalLayering('modal-confirm-attendance');
            normalizeModalLayering('modal-edit-schedule');
            attendanceModal = new bootstrap.Modal(document.getElementById('modal-confirm-attendance'));
            editScheduleModal = new bootstrap.Modal(document.getElementById('modal-edit-schedule'));
            initializeTable();
        })();
    </script>
@endpush

