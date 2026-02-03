@extends('layouts.header')
@section('content')
<div class="container py-6">
    <div class="row">
        <div class="col-12 col-md-12 mx-auto">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h5 class="mb-0">Editar Procedimento</h5>
                </div>
                <div class="card-body">
                    <form id="procedureForm">
                        <input type="hidden" name="id" id="procedureId" value="{{ $patient->id }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome</label>
                                <input type="text" name="name" id="name" value="{{ $patient->name }}" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo</label>
                                <select name="procedure_type_id" id="procedure_type_id" class="form-select" required>
                                    <option value="">Carregando tipos...</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Preço de custo</label>
                                <input type="number" step="0.01" name="cost_price" id="cost_price" value="{{ $patient->cost_price }}" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">% sobre venda</label>
                                <input type="number" step="0.01" name="percentage_on_sale" id="percentage_on_sale" value="{{ $patient->percentage_on_sale }}" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Preço (calculado)</label>
                                <input type="text" name="price" id="price" value="{{ $patient->price }}" class="form-control" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Quantidade (qty)</label>
                                <input type="number" name="qty" id="qty" value="{{ $patient->qty ?? 1 }}"  class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Preço unitário</label>
                                <input type="number" step="0.01" name="unit_price" id="unit_price" value="{{ $patient->unit_price }}" class="form-control">
                            </div>

                            <div class="col-md-4 mb-3 d-flex align-items-center">
                                <div class="form-check form-switch me-4">
                                    <input class="form-check-input" type="checkbox" id="is_package" {{ $patient->is_package ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_package">É pacote</label>
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="status" {{ $patient->status ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">Ativo</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="hidden" id="description" value="{{ $patient->description }}">
                            <div class="border quill">
                                <div class="quill-inner" id="description-editor">{{ $patient->description }}</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observação</label>
                            <input type="hidden" id="observation" value="{{ $patient->observation }}">
                            <div class="border quill">
                                <div class="quill-inner" id="observation-editor">{{ $patient->observation }}</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Instruções ao paciente</label>
                            <input type="hidden" id="patient_instructions" value="{{ $patient->patient_instructions }}">
                            <div class="border quill">
                                <div class="quill-inner" id="patient_instructions-editor">{{ $patient->patient_instructions }}</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Agendamento de mensagem</label>
                            <input type="hidden" id="message_schedule" value="{{ $patient->message_schedule }}">
                            <div class="border quill">
                                <div class="quill-inner" id="message_schedule-editor">{{ $patient->message_schedule }}</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Agendamento de mensagem após</label>
                            <input type="hidden" id="message_schedule_after" value="{{ $patient->message_schedule_after }}">
                            <div class="border quill">
                                <div class="quill-inner" id="message_schedule_after-editor">{{ $patient->message_schedule_after }}</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" id="saveBtn" class="btn btn-primary">Salvar</button>
                            <a href="{{ route('panel.procedures.index') }}" class="btn btn-secondary ms-2">Voltar</a>
                        </div>

                        <div id="formAlert" class="alert d-none" role="alert"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function computePrice(){
        const costEl = document.getElementById('cost_price');
        const percEl = document.getElementById('percentage_on_sale');
        const qtyEl = document.getElementById('qty');
        const priceEl = document.getElementById('price');
        const unitPriceEl = document.getElementById('unit_price');

        const cost = parseFloat(costEl?.value) || 0;
        const perc = parseFloat(percEl?.value) || 0;
        const qty = parseFloat(qtyEl?.value) || 1;

        // unit price after percentage
        const unit = cost + (cost * perc / 100);
        const unitRounded = Number.isFinite(unit) ? unit : 0;

        // total price = unit_price * qty
        const total = unitRounded;

        if (unitPriceEl) unitPriceEl.value = unitRounded.toFixed(2)/qty;
        if (priceEl) priceEl.value = total.toFixed(2);
    }
    $("#qty").on("change", computePrice);

    (function(){
        const form    = document.getElementById('procedureForm');
        const saveBtn = document.getElementById('saveBtn');
        const id      = document.getElementById('procedureId').value;

        const typeSelect = document.getElementById('procedure_type_id');

        async function loadTypes(){
            try{
                const res = await fetch('/procedureTypes?limit=1000', { credentials: 'same-origin' });
                if (!res.ok) {
                    console.error('Falha ao carregar tipos', res.status);
                    typeSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                    return;
                }
                const data = await res.json();
                let items = [];
                if (data.data) items = data.data; // paginated
                else if (Array.isArray(data)) items = data;
                typeSelect.innerHTML = '<option value="">-- selecione --</option>';
                const selectedId = String('{{ $patient->procedure_type_id ?? "" }}');
                items.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id ?? t; // presenter may return object or primitive
                    opt.text = t.name ?? (typeof t === 'string' ? t : t.id);
                    if (String(opt.value) === selectedId) opt.selected = true;
                    typeSelect.appendChild(opt);
                });
            }catch(err){
                console.error(err);
                typeSelect.innerHTML = '<option value="">Erro ao carregar</option>';
            }
        }



        document.getElementById('cost_price').addEventListener('input', computePrice);
        document.getElementById('percentage_on_sale').addEventListener('input', computePrice);

        form.addEventListener('submit', async function(e){
            e.preventDefault();
            disableForm(form, "Salvando...");

            if (!validateForm(this)){
                enableForm(form,"Salvar alterações");
                return false;
            }
            const payload = {
                name: document.getElementById('name').value,
                procedure_type_id: document.getElementById('procedure_type_id').value || null,
                description: document.getElementById('description').value,
                observation: document.getElementById('observation').value,
                patient_instructions: document.getElementById('patient_instructions').value,
                message_schedule: document.getElementById('message_schedule').value,
                message_schedule_after: document.getElementById('message_schedule_after').value,
                cost_price: document.getElementById('cost_price').value || null,
                percentage_on_sale: document.getElementById('percentage_on_sale').value || 0,
                qty: document.getElementById('qty').value || 1,
                unit_price: document.getElementById('unit_price').value || null,
                is_package: document.getElementById('is_package').checked ? 1 : 0,
                status: document.getElementById('status').checked ? 1 : 0
            };
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };
            if (csrfMeta){ headers['X-CSRF-TOKEN'] = csrfMeta.getAttribute('content'); }

            try{
                const res = await fetch('/procedures/' + id, {
                    method: 'PUT',
                    headers,
                    body: JSON.stringify(payload),
                    credentials: 'same-origin'
                });

                if (res.ok){
                    const data = await res.json();
                    showToast('Procedimento atualizado com sucesso', 'success');
                    // update price field if returned
                    if (data && data.price){ document.getElementById('price').value = data.price; }
                } else if (res.status === 422){
                    const err = await res.json();
                    const messages = [];
                    if (err.errors){ for (const k in err.errors){ messages.push(err.errors[k].join(', ')); } }
                    else if (err.message){ messages.push(err.message); }
                    showToast(messages.join('\n'), 'danger');
                } else {
                    const txt = await res.text();
                    showToast('Erro ao salvar: ' + (txt || res.statusText), 'danger');
                }
            }catch(err){
                showToast('Erro de rede: ' + err.message, 'danger');
            }finally{
                saveBtn.disabled = false;
                saveBtn.innerText = 'Salvar';
            }
        });

        // init
        loadTypes();
        computePrice();

        function validateForm(form) {
            const isValid = form.checkValidity();
            if (!isValid) {
                form.reportValidity();
                enableForm(form, "Salvar alterações");
                return false;
            }
            function getEditorHtml(id){
                return $(id).html().trim().replace(/<(.|\n)*?>/g, '').trim();
            }

            const descriptionHtml          = getEditorHtml('#description-editor');
            const observationHtml          = getEditorHtml('#observation-editor');
            const patientInstHtml          = getEditorHtml('#patient_instructions-editor');
            const messageScheduleHtml      = getEditorHtml('#message_schedule-editor');
            const messageScheduleAfterHtml = getEditorHtml('#message_schedule_after-editor');

            const descInput = document.getElementById('description');
            if(descInput) descInput.value = descriptionHtml;
            const obsInput = document.getElementById('observation');
            if(obsInput) obsInput.value = observationHtml;
            const instrInput = document.getElementById('patient_instructions');
            if(instrInput) instrInput.value = patientInstHtml;
            const msInput = document.getElementById('message_schedule');
            if(msInput) msInput.value = messageScheduleHtml;
            const msaInput = document.getElementById('message_schedule_after');
            if(msaInput) msaInput.value = messageScheduleAfterHtml;

            const nameEl = document.getElementById('name');
            if(nameEl && nameEl.value.trim() === ''){
                nameEl.focus();
                showToast('O nome do procedimento é obrigatório', 'danger');
                enableForm(form, "Salvar alterações");
                return false;
            }
            return true;
        }
    })();
</script>
@endpush
