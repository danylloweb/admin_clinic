@extends('layouts.header')
@section('content')
    <div class="card mb-3">
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Novo Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6 position-relative">
                                <label class="form-label">Paciente</label>
                                <input type="text" id="patient-search" class="form-control" placeholder="Pesquisar por nome ou telefone" onkeyup="searchPatients()" autocomplete="off">
                                <div id="patient-results" class="list-group position-absolute w-100 z-3 border rounded shadow-sm" style="background:#2a2a3a;"></div>
                                <div id="selected-patient" class="mt-2"></div>
                            </div>

                            <div class="col-md-6 position-relative">
                                <label class="form-label">Procedimentos</label>
                                <input type="text" id="procedure-search" class="form-control" placeholder="Pesquisar por nome" onkeyup="searchProcedures()" autocomplete="off">
                                <div id="procedure-results" class="list-group position-absolute w-100 z-3 border rounded shadow-sm" style="background:#2a2a3a;"></div>
                            </div>
                        </div>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="cart-table">
                                <thead>
                                <tr>
                                    <th>Procedimento</th>
                                    <th>Valor unitario</th>
                                    <th width="120">Quantidade</th>
                                    <th>Total</th>
                                    <th width="70">Acao</th>
                                </tr>
                                </thead>
                                <tbody id="cart-items-body">
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Nenhum item adicionado</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-3">
                                <label class="form-label">Tipo de pagamento</label>
                                <select id="type-payment" class="form-select" onchange="recalculateTotals()">
                                    <option value="1">PIX</option>
                                    <option value="2">Cartao de Credito</option>
                                    <option value="3">Cartao de Debito</option>
                                    <option value="4">Dinheiro</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cartao (referencia de taxa)</label>
                                <select id="brand-card" class="form-select" onchange="recalculateTotals()">
                                    <option value="1">MasterCard</option>
                                    <option value="2">Visa</option>
                                    <option value="3">Elo</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Parcelas</label>
                                <select id="qty-installments" class="form-select" onchange="onInstallmentsChange()">
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

                        <div class="card mt-3">
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-3"><strong>Subtotal:</strong> <span id="subtotal-value">R$ 0,00</span></div>
                                    <div class="col-md-3"><strong>PIX:</strong> <span id="pix-value">R$ 0,00</span></div>
                                    <div class="col-md-3"><strong>Debito:</strong> <span id="debit-value">R$ 0,00</span></div>
                                    <div class="col-md-3"><strong>Credito total:</strong> <span id="credit-total-value">R$ 0,00</span></div>
                                    <div class="col-md-3"><strong>Valor da parcela:</strong> <span id="credit-installment-value">R$ 0,00</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 d-flex gap-2">
                            <button id="btn-create-order" class="btn btn-primary" onclick="submitSalesOrder()">Salvar pedido</button>
                            <button id="btn-generate-document" type="button" class="btn btn-outline-primary" onclick="openInvoicePreview()" disabled>Gerar documento</button>
                            <a href="{{ route('panel.sales-order.index') }}" class="btn btn-secondary">Voltar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const serverUserId = {{ isset($userId) ? (int) $userId : 'null' }};

        const saleState = {
            selectedPatient: null,
            items: []
        };

        let patientSearchTimer = null;
        let procedureSearchTimer = null;

        function formatMoney(value) {
            return Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }

        function parseMoneyBr(value) {
            if (typeof value === 'number') {
                return value;
            }
            if (!value) {
                return 0;
            }
            const normalized = String(value).replace(/\./g, '').replace(',', '.').replace(/[^0-9.-]/g, '');
            const parsed = parseFloat(normalized);
            return Number.isFinite(parsed) ? parsed : 0;
        }

        function getTaxByMasterCard() {
            return {
                1: 0.0449,
                2: 0.0999,
                3: 0.1199,
                4: 0.1299,
                5: 0.1399,
                6: 0.1499,
                7: 0.1599,
                8: 0.1699,
                9: 0.1799,
                10: 0.1899
            };
        }

        function getTaxByEloCard() {
            return {
                1: 0.0568,
                2: 0.1138,
                3: 0.1338,
                4: 0.1438,
                5: 0.1538,
                6: 0.1638,
                7: 0.1738,
                8: 0.1838,
                9: 0.1938,
                10: 0.2038
            };
        }

        function getInstallmentTax(brand, installments) {
            const map = Number(brand) < 2 ? getTaxByMasterCard() : getTaxByEloCard();
            return map[Number(installments)] || 0;
        }

        function getDebitTax(brand) {
            return Number(brand) <= 2 ? 0.0269 : 0.0388;
        }

        function getSelectedPaymentInfo() {
            const typePayment = Number(document.getElementById('type-payment').value || 1);
            const brandCard = Number(document.getElementById('brand-card').value || 1);
            const qtyInstallments = Number(document.getElementById('qty-installments').value || 1);
            return { typePayment, brandCard, qtyInstallments };
        }

        function onInstallmentsChange() {
            const installments = Number(document.getElementById('qty-installments').value || 1);
            if (installments > 1) {
                document.getElementById('type-payment').value = '2';
            }
            recalculateTotals();
        }

        function getSubtotal() {
            return saleState.items.reduce((sum, item) => sum + (item.price * item.qty), 0);
        }

        function updateInvoiceButtonState() {
            const button = document.getElementById('btn-generate-document');
            if (!button) {
                return;
            }
            button.disabled = !(saleState.selectedPatient && saleState.items.length > 0);
        }

        function calculateTotals() {
            const subtotal = getSubtotal();
            const payment = getSelectedPaymentInfo();
            const pixAmount = subtotal >= 250 ? subtotal - (subtotal * 0.05) : subtotal;
            const debitTax = getDebitTax(payment.brandCard);
            const debitAmount = subtotal + (subtotal * debitTax);
            const installmentTax = getInstallmentTax(payment.brandCard, payment.qtyInstallments);
            const installmentBase = payment.qtyInstallments > 0 ? (subtotal / payment.qtyInstallments) : subtotal;
            const installmentAmount = installmentBase + (installmentBase * installmentTax);
            const creditTotal = installmentAmount * payment.qtyInstallments;

            return {
                subtotal,
                pixAmount,
                debitAmount,
                creditTotal,
                installmentAmount,
                payment,
            };
        }

        function formatTodayPtBr() {
            return new Date().toLocaleDateString('pt-BR');
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function getPaymentLabel(typePayment) {
            switch (Number(typePayment)) {
                case 1: return 'PIX';
                case 2: return 'Cartao de Credito';
                case 3: return 'Cartao de Debito';
                case 4: return 'Dinheiro';
                default: return 'Nao informado';
            }
        }

        function getBrandLabel(brandCard) {
            switch (Number(brandCard)) {
                case 1: return 'MasterCard';
                case 2: return 'Visa';
                case 3: return 'Elo';
                default: return 'Nao informado';
            }
        }

        window.openInvoicePreview = function () {
            if (!saleState.selectedPatient) {
                showToast('Selecione um paciente para gerar o documento', 'danger');
                return;
            }

            if (!saleState.items.length) {
                showToast('Adicione ao menos um procedimento para gerar o documento', 'danger');
                return;
            }

            const totals = calculateTotals();
            const patient = saleState.selectedPatient;
            const socialName = patient.social_name || patient.name || 'Paciente';
            const today = formatTodayPtBr();
            const paymentLabel = getPaymentLabel(totals.payment.typePayment);
            const brandLabel = getBrandLabel(totals.payment.brandCard);

            const serializedItems = saleState.items.map(item => ({
                name: item.name,
                price: item.price,
                qty: item.qty,
            }));

            const query = new URLSearchParams({
                social_name: socialName,
                patient_name: patient.name || socialName,
                phone: patient.phone || '-',
                date: today,
                payment_label: paymentLabel,
                brand_label: brandLabel,
                qty_installments: String(totals.payment.qtyInstallments),
                subtotal: String(totals.subtotal),
                pix_amount: String(totals.pixAmount),
                debit_amount: String(totals.debitAmount),
                credit_total: String(totals.creditTotal),
                installment_amount: String(totals.installmentAmount),
                items: JSON.stringify(serializedItems),
            });

            const url = `{{ route('panel.sales-order.invoice') }}?${query.toString()}`;
            const newWindow = window.open(url, '_blank');
            if (!newWindow) {
                showToast('Nao foi possivel abrir nova aba. Verifique bloqueio de pop-up.', 'danger');
            }
        };

        function renderSelectedPatient() {
            const container = document.getElementById('selected-patient');
            if (!saleState.selectedPatient) {
                container.innerHTML = '';
                updateInvoiceButtonState();
                return;
            }

            const patient = saleState.selectedPatient;
            container.innerHTML = `
                <div class="border rounded p-2 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <img src="${patient.photo}" alt="foto" style="width:38px;height:38px;border-radius:999px;object-fit:cover;">
                        <div>
                            <div class="text-white">${patient.name}</div>
                            <small class="text-white">${patient.phone || '-'}</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" type="button" onclick="removeSelectedPatient()">Remover</button>
                </div>
            `;
            updateInvoiceButtonState();
        }

        function removeSelectedPatient() {
            saleState.selectedPatient = null;
            document.getElementById('patient-search').value = '';
            document.getElementById('patient-results').innerHTML = '';
            renderSelectedPatient();
        }

        function renderCartItems() {
            const tbody = document.getElementById('cart-items-body');
            if (!saleState.items.length) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Nenhum item adicionado</td></tr>';
                recalculateTotals();
                updateInvoiceButtonState();
                return;
            }

            tbody.innerHTML = saleState.items.map(item => `
                <tr>
                    <td>${item.name}</td>
                    <td>${formatMoney(item.price)}</td>
                    <td>
                        <input
                            type="number"
                            min="1"
                            class="form-control form-control-sm"
                            value="${item.qty}"
                            onchange="updateItemQty(${item.id}, this.value)">
                    </td>
                    <td>${formatMoney(item.price * item.qty)}</td>
                    <td>
                        <button class="btn btn-sm btn-danger" type="button" onclick="removeItemFromCart(${item.id})">
                            <i class="ph ph-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');

            recalculateTotals();
            updateInvoiceButtonState();
        }

        function recalculateTotals() {
            const totals = calculateTotals();

            document.getElementById('subtotal-value').innerText = formatMoney(totals.subtotal);
            document.getElementById('pix-value').innerText = formatMoney(totals.pixAmount);
            document.getElementById('debit-value').innerText = formatMoney(totals.debitAmount);
            document.getElementById('credit-total-value').innerText = formatMoney(totals.creditTotal);
            document.getElementById('credit-installment-value').innerText = formatMoney(totals.installmentAmount);
        }

        function updateItemQty(procedureId, qty) {
            const normalizedQty = Math.max(1, parseInt(qty, 10) || 1);
            saleState.items = saleState.items.map(item => {
                if (item.id === procedureId) {
                    return { ...item, qty: normalizedQty };
                }
                return item;
            });
            renderCartItems();
        }

        function removeItemFromCart(procedureId) {
            saleState.items = saleState.items.filter(item => item.id !== procedureId);
            renderCartItems();
        }

        function addItemToCart(procedure) {
            const qtyInput = document.getElementById(`qty-procedure-${procedure.id}`);
            const qty = Math.max(1, parseInt(qtyInput.value, 10) || 1);
            const existing = saleState.items.find(item => item.id === procedure.id);

            if (existing) {
                existing.qty += qty;
            } else {
                saleState.items.push({
                    id: procedure.id,
                    name: procedure.name,
                    price: parseMoneyBr(procedure.price),
                    qty: qty
                });
            }

            renderCartItems();
            showToast('Procedimento adicionado ao carrinho', 'success');
        }

        async function fetchPatients(term) {
            const url = `{{ route('patients.index') }}?search=${encodeURIComponent(term)}&limit=8`;
            const res = await fetch(url, { credentials: 'same-origin' });
            if (!res.ok) {
                return [];
            }
            const data = await res.json();
            return data.data || [];
        }

        async function fetchProcedures(term) {
            const url = `{{ route('procedures.index') }}?search=${encodeURIComponent(term)}&limit=8`;
            const res = await fetch(url, { credentials: 'same-origin' });
            if (!res.ok) {
                return [];
            }
            const data = await res.json();
            return data.data || [];
        }

        window.searchPatients = function () {
            const term = document.getElementById('patient-search').value.trim();
            clearTimeout(patientSearchTimer);

            if (term.length < 2) {
                document.getElementById('patient-results').innerHTML = '';
                return;
            }

            patientSearchTimer = setTimeout(async function () {
                const patients = await fetchPatients(term);
                const resultBox = document.getElementById('patient-results');

                if (!patients.length) {
                    resultBox.innerHTML = '<div class="list-group-item text-dark">Nenhum paciente encontrado</div>';
                    return;
                }

                resultBox.innerHTML = patients.map(patient => `
                    <button type="button" class="list-group-item list-group-item-action" onclick="selectPatient(${patient.id})">
                        <div class="d-flex align-items-center gap-2">
                            <img src="${patient.photo}" alt="foto" style="width:34px;height:34px;border-radius:999px;object-fit:cover;">
                            <div>
                                <div class="text-white">${patient.name}</div>
                                <small class="text-white">${patient.phone || '-'}</small>
                            </div>
                        </div>
                    </button>
                `).join('');

                window.__patientsLastSearch = patients;
            }, 250);
        };

        window.selectPatient = function (id) {
            const patients = window.__patientsLastSearch || [];
            const selected = patients.find(item => Number(item.id) === Number(id));
            if (!selected) {
                return;
            }
            saleState.selectedPatient = selected;
            document.getElementById('patient-search').value = `${selected.name} - ${selected.phone || ''}`;
            document.getElementById('patient-results').innerHTML = '';
            renderSelectedPatient();
        };

        window.searchProcedures = function () {
            const term = document.getElementById('procedure-search').value.trim();
            clearTimeout(procedureSearchTimer);

            if (term.length < 2) {
                document.getElementById('procedure-results').innerHTML = '';
                return;
            }

            procedureSearchTimer = setTimeout(async function () {
                const procedures = await fetchProcedures(term);
                const resultBox = document.getElementById('procedure-results');

                if (!procedures.length) {
                    resultBox.innerHTML = '<div class="list-group-item text-dark">Nenhum procedimento encontrado</div>';
                    return;
                }

                resultBox.innerHTML = procedures.map(procedure => `
                    <div class="list-group-item">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div>
                                <div class="text-white">${procedure.name}</div>
                                <small class="text-white">Valor: R$ ${procedure.price}</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input id="qty-procedure-${procedure.id}" type="number" min="1" value="1" class="form-control form-control-sm text-dark" style="width:56px;">
                                <button type="button" class="btn btn-sm btn-primary" onclick="addProcedureFromList(${procedure.id})">Adicionar</button>
                            </div>
                        </div>
                    </div>
                `).join('');

                window.__proceduresLastSearch = procedures;
            }, 250);
        };

        window.addProcedureFromList = function (id) {
            const procedures = window.__proceduresLastSearch || [];
            const selected = procedures.find(item => Number(item.id) === Number(id));
            if (!selected) {
                return;
            }
            addItemToCart(selected);
        };

        function getLoggedUserId() {
            if (serverUserId) {
                return serverUserId;
            }
            try {
                const userJson = localStorage.getItem('user');
                if (!userJson) {
                    return null;
                }
                const user = JSON.parse(userJson);
                return user?.id || null;
            } catch (error) {
                return null;
            }
        }

        function buildSalesOrderPayload() {
            const payment = getSelectedPaymentInfo();
            const userId = getLoggedUserId();
            return {
                patient_id: saleState.selectedPatient?.id || null,
                qty_installments: payment.typePayment === 2 ? payment.qtyInstallments : 1,
                user_id: userId,
                type_payment: payment.typePayment,
                brand_card: payment.brandCard,
                items: saleState.items.map(item => ({
                    procedure_id: item.id,
                    qty: item.qty
                }))
            };
        }

        window.submitSalesOrder = async function () {
            const payload = buildSalesOrderPayload();

            if (!payload.patient_id) {
                showToast('Selecione um paciente para continuar', 'danger');
                return;
            }

            if (!payload.items.length) {
                showToast('Adicione pelo menos um procedimento', 'danger');
                return;
            }

            if (!payload.user_id) {
                showToast('Usuario nao identificado no navegador. Faca login novamente.', 'danger');
                return;
            }

            const button = document.getElementById('btn-create-order');
            button.disabled = true;
            const previousText = button.innerText;
            button.innerText = 'Salvando...';

            try {
                const res = await fetch('{{ route('salesOrders.store') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload)
                });

                if (!res.ok) {
                    const errorResponse = await res.json().catch(() => ({}));
                    showToast(errorResponse.message || 'Erro ao salvar pedido', 'danger');
                    return;
                }

                showToast('Pedido criado com sucesso', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route('panel.sales-order.index') }}';
                }, 900);
            } catch (error) {
                showToast('Erro de rede ao salvar pedido', 'danger');
            } finally {
                button.disabled = false;
                button.innerText = previousText;
            }
        };

        document.addEventListener('click', function (event) {
            const patientInput = document.getElementById('patient-search');
            const patientResults = document.getElementById('patient-results');
            if (!patientResults.contains(event.target) && event.target !== patientInput) {
                patientResults.innerHTML = '';
            }

            const procedureInput = document.getElementById('procedure-search');
            const procedureResults = document.getElementById('procedure-results');
            if (!procedureResults.contains(event.target) && event.target !== procedureInput) {
                procedureResults.innerHTML = '';
            }
        });

        renderSelectedPatient();
        renderCartItems();
    </script>
@endpush

