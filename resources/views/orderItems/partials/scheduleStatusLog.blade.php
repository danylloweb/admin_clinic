<section class="content-header">
    <div class="box box-primary collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title">Logs do Agendamento</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead>
                <tr>
                    <th>Status</th>
                    <th>Autor</th>
                    <th>Log</th>
                    <th>Data</th>
                </tr>
                </thead>
                <tbody>
                @foreach($scheduleStatusLog as $log)
                    <tr>
                        <td>{{ \App\Enums\ScheduleStatusEnum::translateFromInt($log->status) }}</td>
                        <td>{{ $log->author }}</td>
                        <td>{{ $log->log }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->updated_at)->format('d/m/Y H:i:s') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
