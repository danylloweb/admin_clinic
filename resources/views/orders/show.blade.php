@extends('layouts.app')
@section('content')
    <section class="content-header">
        <h1>
            Detalhes do Pedido
            <small>#{{ $order->id }}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('painel.orders') }}"><i class="fa fa-list"></i> Pedidos</a></li>
            <li class="active">Detalhes</li>
        </ol>
    </section>

    <section class="content">
        <!-- Detalhes principais do pedido -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Informações do Pedido</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <!-- Coluna da Order -->
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Hash</dt>
                            <dd class="col-sm-8">{{ $order->order_hash }}</dd>

                            <dt class="col-sm-4">Sessão</dt>
                            <dd class="col-sm-8">{{ $order->session_id }}</dd>

                            <dt class="col-sm-4">Pedido Externo</dt>
                            <dd class="col-sm-8">{{ $order->external_order_id }}</dd>

                            <dt class="col-sm-4">CEP</dt>
                            <dd class="col-sm-8">{{ $order->zip_code }}</dd>

                            <dt class="col-sm-4">Fornecedor ID</dt>
                            <dd class="col-sm-8">{{ $order->provider_id }}</dd>

                            <dt class="col-sm-4">Canal</dt>
                            <dd class="col-sm-8">{{ $order->channel }}</dd>

                            <dt class="col-sm-4">Total</dt>
                            <dd class="col-sm-8">R$ {{ number_format($order->total, 2, ',', '.') }}</dd>

                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8">{{ \App\Enums\OrderStatusEnum::translateFromInt($order->status) }}</dd>
                        </dl>
                    </div>
                    <!-- Coluna do Customer (JSON) -->
                    <div class="col-md-6">
                        <h4>Informações do Cliente</h4>
                        @php
                            $customer = json_decode($order->customer);
                        @endphp

                        <dl class="row">
                            <dt class="col-sm-4">ID</dt>
                            <dd class="col-sm-8">{{ $customer->id ?? '-' }}</dd>

                            <dt class="col-sm-4">Nome</dt>
                            <dd class="col-sm-8">{{ $customer->name ?? '-' }}</dd>

                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8">{{ $customer->email ?? '-' }}</dd>

                            <dt class="col-sm-4">Telefone</dt>
                            <dd class="col-sm-8">{{ $customer->phone ?? '-' }}</dd>

                            <dt class="col-sm-4">Documento</dt>
                            <dd class="col-sm-8">{{ $customer->document ?? '-' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Itens do Pedido -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Itens do Pedido</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    @foreach($order->items as $index => $item)
                        @php
                            $metadata = json_decode($item->metadata ?? '{}');
                            $isService = $item->type == \App\Enums\ItemTypeEnum::SERVICE->value;
                            $translatedType = \App\Enums\ItemTypeEnum::translateFromInt($item->type);
                        @endphp

                        <div class="col-md-4">
                            <div class="box box-widget widget-user">
                                <div class="widget-user-header bg-aqua-active">
                                    <h4 class="widget-user-username">{{ $item->ref_description }}</h4>
                                    <h5 class="widget-user-desc">{{ $item->ref_parent_description }}</h5>
                                </div>
                                <div class="widget-user-image" style="top: 60px;margin-top: 30px">
                                    <a href="{{ $item->ref_image_url }}" target="_blank">
                                        <img class="img-circle" src="{{ $item->ref_image_url }}" alt="Imagem Ref" style="width: 60px; height: 60px;">
                                    </a>
                                    <a href="{{ $item->ref_parent_image_url }}" target="_blank">
                                        <img class="img-circle" src="{{ $item->ref_parent_image_url }}" alt="Imagem Pai" style="width: 60px; height: 60px; margin-left: 10px;">
                                    </a>
                                </div>
                                <div class="box-footer" style="margin-top: 40px;">
                                    <ul class="list-group list-group-unbordered">
                                        <li class="list-group-item"><b>Ref ID</b> <span class="pull-right">{{ $item->ref_id }}</span></li>
                                        <li class="list-group-item"><b>Ref Parent ID</b> <span class="pull-right">{{ $item->ref_parent_id }}</span></li>
                                        <li class="list-group-item"><b>Tipo</b> <span class="pull-right">{{ $translatedType }}</span></li>
                                        <li class="list-group-item"><b>Preço</b> <span class="pull-right">R$ {{ number_format($item->price, 2, ',', '.') }}</span></li>
                                        <li class="list-group-item"><b>Quantidade</b> <span class="pull-right">{{ $item->quantity }}</span></li>
                                        <li class="list-group-item"><b>Cliente ID</b> <span class="pull-right">{{ $item->customer_id }}</span></li>
                                        <li class="list-group-item"><b>Status</b> <span class="pull-right">{{ \App\Enums\OrderItemStatusEnum::translateFromInt($item->status) }}</span></li>
                                    </ul>

                                    @if($isService && !empty((array)$metadata))
                                        <button class="btn btn-default btn-block btn-sm mt-2 mb-1" data-toggle="collapse" data-target="#metadata-{{ $index }}">
                                            Ver Metadata do Serviço
                                        </button>
                                        <div class="collapse" id="metadata-{{ $index }}">
                                            <pre class="bg-light p-2 mt-2"><code>{{ json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                        </div>
                                    @endif
                                    <a href="{{ route('painel.order.item.show', ['id' => $item->id]) }}"
                                       class="btn btn-info btn-block btn-sm mt-3" style="margin-top: 8px">
                                        Ver Detalhes do Item
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
