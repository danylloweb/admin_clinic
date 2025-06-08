<section class="content-header">
        <div class="box box-primary collapsed-box">
            <div class="box-header with-border">
                <h3 class="box-title">Agendamento #{{ $schedule->id }}</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <dl class="dl-horizontal">
                            <dt>ID do Cliente</dt>
                            <dd>{{ $schedule->customer_id }}</dd>

                            <dt>Order ID</dt>
                            <dd>{{ $schedule->order_id }}</dd>

                            <dt>Order Item ID</dt>
                            <dd>{{ $schedule->order_item_id }}</dd>

                            <dt>Provider ID</dt>
                            <dd>{{ $schedule->provider_id }}</dd>

                            <dt>Status</dt>
                            <dd>{{ \App\Enums\ScheduleStatusEnum::translateFromInt($schedule->status) }}</dd>

                            <dt>Ref ID</dt>
                            <dd>{{ $schedule->ref_id }}</dd>

                            <dt>Ref Parent ID</dt>
                            <dd>{{ $schedule->ref_parent_id }}</dd>

                            <dt>Canal</dt>
                            <dd>{{ $schedule->channel }}</dd>

                            @if($schedule->when_date)
                                <dt>Data</dt>
                                <dd>{{ \Carbon\Carbon::parse($schedule->when_date)->format('d/m/Y') }}</dd>
                                <dt>Hora Início</dt>
                                <dd>{{ $schedule->when_time_start }}</dd>
                                <dt>Hora Fim</dt>
                                <dd>{{ $schedule->when_time_end }}</dd>
                            @endif

                        </dl>
                    </div>
                </div>
            </div>
        </div>
</section>
