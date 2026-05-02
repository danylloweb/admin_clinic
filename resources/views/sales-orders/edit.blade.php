@extends('layouts.header')
@section('content')
    <div class="card mb-3">
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Editar Pedido</h5>
                        <span id="order-number-badge" class="badge fs-7"></span>
                    </div>
                    <div class="card-body" id="edit-form-body">
                        <div class="text-center py-5">
                            <div class="spinner-border" role="status"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal editar agendamento --}}
    <div class="modal fade" id="modal-edit-schedule" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Agendamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit-schedule-id">
                    <div class="mb-3">
                        <label class="form-label">Paciente</label>
                        <input type="text" id="edit-schedule-patient" class="form-control" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Procedimento</label>
                        <input type="text" id="edit-schedule-procedure-search" class="form-control" placeholder="Buscar procedimento" autocomplete="off">
                        <input type="hidden" id="edit-schedule-procedure-id">
                        <div id="edit-schedule-procedure-results" class="list-group position-absolute mt-1 z-3 shadow-sm" style="width:calc(100% - 3rem);background:#2a2a3a;"></div>
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
                        <textarea id="edit-schedule-observation" class="form-control" rows="3" placeholder="Observação"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-edit-schedule">Salvar alterações</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal agendamento --}}
    <div class="modal fade" id="modal-schedule" tabindex="-1" aria-labelledby="modal-schedule-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-schedule-label">Agendar Procedimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="schedule-item-id">
                    <div class="mb-3">
                        <label class="form-label">Procedimento</label>
                        <input type="text" id="schedule-procedure-name" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data</label>
                        <input type="date" id="schedule-date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hora</label>
                        <input type="time" id="schedule-time" class="form-control">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="schedule-send-message" checked>
                        <label class="form-check-label" for="schedule-send-message">Enviar mensagem de confirmacao</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-schedule">Salvar Agendamento</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const ORDER_ID = {{ (int) $orderId }};

    const typePaymentLabels = {1:'PIX', 2:'Cartão de Crédito', 3:'Cartão de Débito', 4:'Dinheiro'};
    const statusLabels = {0:'Inicial', 1:'Pago', 2:'Cancelado', 3:'Parcial', 4:'Finalizado'};

    let currentOrder = null;

    async function apiGet(url) {
        const res = await fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } });
        if (!res.ok) throw new Error('Erro na requisição');
        return res.json();
    }

    async function apiPut(url, data) {
        const res = await fetch(url, {
            method: 'PUT',
            credentials: 'same-origin',
            headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message || 'Erro ao salvar');
        }
        return res.json();
    }

    async function apiPost(url, data) {
        const res = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.message || 'Erro ao salvar');
        }
        return res.json();
    }

    function statusBadge(item) {
        if (item.schedule_id) {
            return `<span class="badge bg-success">${item.status}</span>`;
        }
        return `<span class="badge bg-warning text-dark">${item.status}</span>`;
    }

    function renderForm(order) {
        currentOrder = order;
        document.getElementById('order-number-badge').innerText = '#' + order.number;

        const body = document.getElementById('edit-form-body');
        body.innerHTML = `
            {{-- Paciente somente leitura --}}
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Paciente</label>
                    <div class="border rounded p-2 d-flex align-items-center gap-2">
                        <div>
                            <div class="fw-semibold">${order.patient_name}</div>
                            <small class="text-muted">${order.phone || '-'}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Status</label>
                    <select id="edit-order-status" class="form-select" onchange="saveStatus()">
                        <option value="0" ${Number(order.status) === 0 ? 'selected' : ''}>Inicial</option>
                        <option value="1" ${Number(order.status) === 1 ? 'selected' : ''}>Pago</option>
                        <option value="2" ${Number(order.status) === 2 ? 'selected' : ''}>Cancelado</option>
                        <option value="3" ${Number(order.status) === 3 ? 'selected' : ''}>Parcial</option>
                        <option value="4" ${Number(order.status) === 4 ? 'selected' : ''}>Finalizado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Total</label>
                    <div class="fw-semibold fs-4">R$ ${order.amount}</div>
                </div>
            </div>

            <hr>

            {{-- Campos editáveis --}}
            <div class="row g-3 mb-3">
                <div class="col-md-2">
                    <label class="form-label">Tipo de pagamento</label>
                    <select id="edit-type-payment" class="form-select">
                        <option value="1" ${Number(order.type_payment) === 1 ? 'selected' : ''}>PIX </option>
                        <option value="2" ${Number(order.type_payment) === 2 ? 'selected' : ''}>Cartão de Crédito 💳</option>
                        <option value="3" ${Number(order.type_payment) === 3 ? 'selected' : ''}>Cartão de Débito 💳</option>
                        <option value="4" ${Number(order.type_payment) === 4 ? 'selected' : ''}>Dinheiro 💵</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Bandeira do cartão</label>
                    <select id="edit-brand-card" class="form-select">
                        <option value="1" ${Number(order.brand_card) === 1 ? 'selected' : ''}>MasterCard</option>
                        <option value="2" ${Number(order.brand_card) === 2 ? 'selected' : ''}>Visa</option>
                        <option value="3" ${Number(order.brand_card) === 3 ? 'selected' : ''}>Elo</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Parcelas</label>
                    <select id="edit-qty-installments" class="form-select">
                        ${[1,2,3,4,5,6,7,8,9,10].map(n =>
                            `<option value="${n}" ${Number(order.qty_installments) === n ? 'selected' : ''}>${n}x</option>`
                        ).join('')}
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button class="btn btn-primary" onclick="savePayment()">Salvar pagamento</button>
                </div>
            </div>

            <hr>

            {{-- Valores calculados --}}
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-3"><strong>PIX:</strong> R$ ${order.amount_pix}</div>
                        <div class="col-md-3"><strong>Débito:</strong> R$ ${order.amount_debit}</div>
                        <div class="col-md-3"><strong>Crédito total:</strong> R$ ${order.amount_credit}</div>
                        <div class="col-md-3"><strong>Parcela:</strong> R$ ${order.amount_installment}</div>
                    </div>
                </div>
            </div>

            <hr>

            {{-- Itens do pedido --}}
            <h6 class="fw-semibold mb-2">Itens do Pedido</h6>
            <div class="table-responsive">
                <table class="table table-bordered" id="items-table">
                    <thead>
                        <tr>
                            <th>Procedimento</th>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Status</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody id="items-tbody">
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex gap-2">
                <a href="{{ route('panel.sales-order.index') }}" class="btn btn-secondary">Voltar</a>
            </div>
        `;

        renderItems(order.items || []);
    }

    function renderItems(items) {
        const tbody = document.getElementById('items-tbody');
        if (!tbody) return;

        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Sem itens</td></tr>';
            return;
        }

        tbody.innerHTML = items.map(item => `
            <tr>
                <td>${item.procedure_name}</td>
                <td>${item.date || '-'}</td>
                <td>${item.time || '-'}</td>
                <td>${statusBadge(item)}</td>
                <td>
                    ${!item.schedule_id
                        ? `<button class="btn btn-sm btn-outline-success" onclick="openScheduleModal(${item.id}, '${escapeAttr(item.procedure_name)}')">
                               <i class="ph ph-calendar-plus"></i> Agendar
                           </button>`
                        : `<div class="d-flex gap-1">
                               <button class="btn btn-sm btn-outline-success" onclick="openEditScheduleModal(${item.schedule_id}, '${escapeAttr(item.procedure_name)}', ${item.procedure_id || 'null'})">
                                   <i class="ph ph-pencil-simple-line"></i>Editar
                               </button>
                           </div>`
                    }
                </td>
            </tr>
        `).join('');
    }

    function escapeAttr(value) {
        return String(value ?? '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    function normalizeModalLayering() {
        const modalEl = document.getElementById('modal-schedule');
        if (!modalEl) return;

        // Keep modal at body level to avoid z-index conflicts with parent containers.
        if (modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }

        modalEl.style.zIndex = '1060';
        document.querySelectorAll('.modal-backdrop').forEach((backdrop) => {
            backdrop.style.zIndex = '1050';
        });
    }

    window.openScheduleModal = function(itemId, procedureName) {
        document.getElementById('schedule-item-id').value = itemId;
        document.getElementById('schedule-procedure-name').value = procedureName;
        document.getElementById('schedule-date').value = '';
        document.getElementById('schedule-time').value = '';
        document.getElementById('schedule-send-message').checked = true;
        normalizeModalLayering();
        const modal = new bootstrap.Modal(document.getElementById('modal-schedule'));
        modal.show();
        normalizeModalLayering();
    };

    document.getElementById('modal-schedule').addEventListener('shown.bs.modal', normalizeModalLayering);

    window.savePayment = async function() {
        const typePayment = Number(document.getElementById('edit-type-payment').value);
        const brandCard = Number(document.getElementById('edit-brand-card').value);
        const qtyInstallments = Number(document.getElementById('edit-qty-installments').value);

        try {
            if (typePayment !== currentOrder.type_payment) {
                await apiPut(`{{ url('/') }}/update-typePayment/salesOrders/${ORDER_ID}`, { type_payment: typePayment });
            }
            if (brandCard !== currentOrder.brand_card) {
                await apiPut(`{{ url('/') }}/update-brandPayment/salesOrders/${ORDER_ID}`, { brand_card: brandCard });
            }
            if (qtyInstallments !== currentOrder.qty_installments) {
                await apiPut(`{{ url('/') }}/saleOrder-update-installment/${ORDER_ID}`, { qty_installments: qtyInstallments });
            }
            showToast('Pagamento atualizado com sucesso', 'success');
            loadOrder();
        } catch (e) {
            showToast(e.message || 'Erro ao salvar', 'danger');
        }
    };

    window.saveStatus = async function() {
        const status = Number(document.getElementById('edit-order-status').value);

        try {
            if (status === Number(currentOrder.status)) {
                showToast('Status ja esta selecionado', 'info');
                return;
            }

            await apiPut(`{{ url('/') }}/update-status/salesOrders/${ORDER_ID}`, { status });
            showToast('Status atualizado com sucesso', 'success');
            loadOrder();
        } catch (e) {
            showToast(e.message || 'Erro ao atualizar status', 'danger');
        }
    };

    document.getElementById('btn-save-schedule').addEventListener('click', async function() {
        const itemId = document.getElementById('schedule-item-id').value;
        const date_schedule = document.getElementById('schedule-date').value;
        const time_schedule = document.getElementById('schedule-time').value;
        const send_message = document.getElementById('schedule-send-message').checked ? 'send' : 'not';

        if (!date_schedule || !time_schedule) {
            showToast('Preencha data e hora', 'danger');
            return;
        }

        this.disabled = true;
        const prev = this.innerText;
        this.innerText = 'Salvando...';

        try {
            const procedureItemId = Number(itemId);

            // Busca o item do pedido para obter o procedure_id
            const item = (currentOrder.items || []).find(i => i.id === procedureItemId);
            if (!item) {
                showToast('Item nao encontrado', 'danger');
                return;
            }

            await apiPut(`{{ url('/') }}/schedule/salesOrdersItems/${procedureItemId}`, {
                date_schedule,
                time_schedule,
                send_message,
                sales_order_item_id: procedureItemId,
            });

            showToast('Agendamento salvo com sucesso', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modal-schedule')).hide();
            loadOrder();
        } catch (e) {
            showToast(e.message || 'Erro ao agendar', 'danger');
        } finally {
            this.disabled = false;
            this.innerText = prev;
        }
    });

    async function loadOrder() {
        try {
            const data = await apiGet(`{{ url('/') }}/salesOrders/${ORDER_ID}`);
            const order = data.data ?? data;
            renderForm(order);
        } catch (e) {
            document.getElementById('edit-form-body').innerHTML =
                `<div class="alert alert-danger">Erro ao carregar pedido: ${e.message}</div>`;
        }
    }

    // ---- Edit Schedule Modal ----
    let editScheduleModalInstance = null;
    let editScheduleProcedureTimer = null;

    async function loadProfessionalsForEdit() {
        const select = document.getElementById('edit-schedule-professional-id');
        if (!select || select.options.length > 1) return;
        try {
            const data = await apiGet(`{{ route('users.index') }}?has_medical=Sim&limit=200`);
            (data.data || []).forEach(user => {
                const opt = document.createElement('option');
                opt.value = user.id;
                opt.textContent = user.name;
                select.appendChild(opt);
            });
        } catch (e) {}
    }

    window.openEditScheduleModal = async function(scheduleId, procedureName, procedureId) {
        const modalEl = document.getElementById('modal-edit-schedule');
        document.getElementById('edit-schedule-id').value = scheduleId;
        document.getElementById('edit-schedule-patient').value = currentOrder?.patient_name || '-';
        document.getElementById('edit-schedule-procedure-search').value = procedureName || '';
        document.getElementById('edit-schedule-procedure-id').value = procedureId || '';
        document.getElementById('edit-schedule-date').value = '';
        document.getElementById('edit-schedule-time').value = '';
        document.getElementById('edit-schedule-status').value = 'Marcado';
        document.getElementById('edit-schedule-professional-id').value = '';
        document.getElementById('edit-schedule-observation').value = '';
        document.getElementById('edit-schedule-procedure-results').innerHTML = '';

        await loadProfessionalsForEdit();

        try {
            const data = await apiGet(`{{ url('/') }}/schedules/${scheduleId}`);
            const s = data.data ?? data;
            document.getElementById('edit-schedule-date').value = s.date_real || s.date || '';
            document.getElementById('edit-schedule-time').value = String(s.time || '').slice(0, 5);
            document.getElementById('edit-schedule-status').value = s.status || 'Marcado';
            document.getElementById('edit-schedule-professional-id').value = s.professional_id || '';
            document.getElementById('edit-schedule-observation').value = s.observation_status || '';
        } catch (e) {}

        if (modalEl.parentElement !== document.body) document.body.appendChild(modalEl);
        modalEl.style.zIndex = '1060';
        editScheduleModalInstance = new bootstrap.Modal(modalEl);
        editScheduleModalInstance.show();
        document.querySelectorAll('.modal-backdrop').forEach(b => b.style.zIndex = '1050');
    };

    document.getElementById('modal-edit-schedule').addEventListener('shown.bs.modal', function() {
        this.style.zIndex = '1060';
        document.querySelectorAll('.modal-backdrop').forEach(b => b.style.zIndex = '1050');
    });

    document.getElementById('modal-edit-schedule').addEventListener('hidden.bs.modal', function() {
        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });

    document.getElementById('edit-schedule-procedure-search').addEventListener('keyup', function() {
        const term = this.value.trim();
        clearTimeout(editScheduleProcedureTimer);
        const resultsEl = document.getElementById('edit-schedule-procedure-results');

        if (term.length < 2) {
            document.getElementById('edit-schedule-procedure-id').value = '';
            resultsEl.innerHTML = '';
            return;
        }

        editScheduleProcedureTimer = setTimeout(async function() {
            try {
                const data = await apiGet(`{{ route('procedures.index') }}?search=${encodeURIComponent(term)}&limit=7`);
                resultsEl.innerHTML = '';
                (data.data || []).forEach(procedure => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'list-group-item list-group-item-action';
                    btn.style.cssText = 'background:#2a2a3a;color:#fff;border-color:rgba(255,255,255,0.1)';
                    btn.innerHTML = `<div class="d-flex justify-content-between"><span>${procedure.name}</span><small>R$ ${procedure.price}</small></div>`;
                    btn.addEventListener('click', function() {
                        document.getElementById('edit-schedule-procedure-id').value = procedure.id;
                        document.getElementById('edit-schedule-procedure-search').value = procedure.name;
                        resultsEl.innerHTML = '';
                    });
                    resultsEl.appendChild(btn);
                });
            } catch(e) {}
        }, 350);
    });

    document.getElementById('btn-save-edit-schedule').addEventListener('click', async function() {
        const scheduleId = document.getElementById('edit-schedule-id').value;
        const procedureId = document.getElementById('edit-schedule-procedure-id').value;
        const date = document.getElementById('edit-schedule-date').value;
        const time = document.getElementById('edit-schedule-time').value;
        const status = document.getElementById('edit-schedule-status').value;
        const professionalId = document.getElementById('edit-schedule-professional-id').value;
        const observation = document.getElementById('edit-schedule-observation').value || '';

        if (!procedureId) { showToast('Selecione o procedimento.', 'danger'); return; }
        if (!date || !time) { showToast('Preencha data e hora.', 'danger'); return; }

        this.disabled = true;
        const prev = this.innerText;
        this.innerText = 'Salvando...';

        try {
            await apiPut(`{{ url('/') }}/schedules/${scheduleId}`, {
                procedure_id: Number(procedureId),
                date, time, status,
                professional_id: professionalId ? Number(professionalId) : null,
                observation_status: observation,
            });

            if (status === 'Confirmado' && professionalId) {
                await apiPut(`{{ url('/') }}/schedule/update-status/${scheduleId}`, {
                    status,
                    professional_id: Number(professionalId),
                    observation_status: observation,
                });
            }

            showToast('Agendamento atualizado com sucesso.', 'success');
            editScheduleModalInstance?.hide();
            loadOrder();
        } catch (e) {
            showToast(e.message || 'Erro ao salvar agendamento.', 'danger');
        } finally {
            this.disabled = false;
            this.innerText = prev;
        }
    });
    // ---- End Edit Schedule Modal ----

    loadOrder();
</script>
@endpush

