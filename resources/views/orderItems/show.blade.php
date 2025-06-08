@extends('layouts.app')
@section('content')
    <section class="content-header">
        <h1>Detalhes do Item do Pedido</h1>
        <ol class="breadcrumb">
            <li><a href="javascript:history.back()"><i class="fa fa-list"></i>Items</a></li>
            <li class="active">Detalhes</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Detalhes do Item #{{ $orderItem->id }}</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-9">
                        <dl class="dl-horizontal">
                            <dt> ID</dt>
                            <dd>{{ $orderItem->id }}</dd>

                            <dt>Order ID</dt>
                            <dd>{{ $orderItem->order_id }}</dd>

                            <dt>Pedido Externo</dt>
                            <dd>{{ $orderItem->external_order_id }}</dd>

                            <dt>Ref ID</dt>
                            <dd>{{ $orderItem->ref_id }}</dd>

                            <dt>Ref Pai ID</dt>
                            <dd>{{ $orderItem->ref_parent_id }}</dd>

                            <dt>Tipo</dt>
                            <dd>{{ \App\Enums\ItemTypeEnum::translateFromInt($orderItem->type) }}</dd>

                            <dt>Preço</dt>
                            <dd>R$ {{ number_format($orderItem->price, 2, ',', '.') }}</dd>

                            <dt>Quantidade</dt>
                            <dd>{{ $orderItem->quantity }}</dd>

                            <dt>Cliente ID</dt>
                            <dd>{{ $orderItem->customer_id }}</dd>

                            <dt>Status</dt>
                            <dd>{{ \App\Enums\OrderItemStatusEnum::translateFromInt($orderItem->status) }}</dd>
                        </dl>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="mb-3">
                            <span>{{$orderItem->ref_description}}</span><br>
                            <a href="{{ $orderItem->ref_image_url }}" target="_blank">
                                <img src="{{ $orderItem->ref_image_url }}" alt="Imagem Ref" class="img-thumbnail" style="max-width: 100px;">
                            </a>
                        </div>
                        <div>
                            <span>{{$orderItem->ref_parent_description}}</span><br>
                            <a href="{{ $orderItem->ref_parent_image_url }}" target="_blank">
                                <img src="{{ $orderItem->ref_parent_image_url }}" alt="Imagem Pai" class="img-thumbnail" style="max-width: 100px;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>


    {{-- Abaixo virá a parte do schedule e seus logs --}}
    @if($orderItem->schedule)
        {{-- Abaixo virá a parte do professional --}}
     @include('orderItems.partials.professional', ['professional' => $orderItem->schedule->professional])
     @include('orderItems.partials.schedule', ['schedule' => $orderItem->schedule])
     @include('orderItems.partials.scheduleStatusLog', ['scheduleStatusLog' => $orderItem->schedule->scheduleStatusLog])
     @include('orderItems.partials.notificationTransactionLog', ['notificationTransactionLogs' => $notificationTransactionLogs])
    @endif
    {{-- Abaixo virá a parte do customer --}}

@endsection
