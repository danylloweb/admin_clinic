@if($notificationTransactionLogs && count($notificationTransactionLogs))
    <section class="content-header">
        <div class="box box-primary collapsed-box">
            <div class="box-header with-border">
                <h3 class="box-title">Notificações de Transasionais</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Evento</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notificationTransactionLogs as $log)
                            <tr>
                                <td>{{ $log['event_name'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($log['created_at'])->format('d/m/Y H:i') }}</td>
                                <td>
                                    <button class="btn btn-xs btn-default toggle-log-details"
                                        data-target="#log-details-{{ $loop->index }}">
                                        <i class="fa fa-eye"></i> Ver mais
                                    </button>
                                    <button class="btn btn-xs btn-warning resend-notification" data-log-id="{{ $log['_id'] }}"
                                        <i class="fa fa-refresh"></i> Reenviar
                                    </button>
                                </td>
                            </tr>
                            <tr id="log-details-{{ $loop->index }}" class="log-details" style="display: none;">
                                <td colspan="3">
                                    <strong>Payload:</strong>
                                    <pre>{{ json_encode($log['notification_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                                    <strong>Resposta:</strong>
                                    <pre>{{ json_encode($log['notification_response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endif
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-log-details').forEach(button => {
                button.addEventListener('click', () => {
                    const target = document.querySelector(button.dataset.target);
                    if (target) {
                        const isVisible = target.style.display === 'table-row';
                        target.style.display = isVisible ? 'none' : 'table-row';
                        button.innerHTML = isVisible
                            ? '<i class="fa fa-eye"></i> Ver mais'
                            : '<i class="fa fa-eye-slash"></i> Ocultar';
                    }
                });
            });

            document.querySelectorAll('.resend-notification').forEach(button => {
                button.addEventListener('click', async () => {
                    const logId = button.dataset.logId;

                    Swal.fire({
                        title: 'Reenviando...',
                        text: 'Aguarde enquanto a notificação é reenviada.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        const response = await fetch('{{ route('notification.transaction.resend') }}', {
                            method: 'POST',
                            headers: {
                                'Authorization': 'b473bf7f-3ef8-4b90-a37a-bce820efe5e8',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                log_id: logId,
                            })
                        });

                        const result = await response.json();

                        if (response.ok) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Notificação reenviada com sucesso!',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro ao reenviar',
                                text: result?.message || 'Ocorreu um erro desconhecido.',
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro de rede',
                            text: 'Não foi possível se conectar ao servidor.',
                        });
                    }
                });
            });

        });
    </script>
@endpush
