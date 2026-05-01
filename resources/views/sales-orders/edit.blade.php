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
                    <div class="fw-semibold">R$ ${order.amount}</div>
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
                        : `<span class="text-muted small">Agendado</span>`
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

    loadOrder();
</script>
@endpush

