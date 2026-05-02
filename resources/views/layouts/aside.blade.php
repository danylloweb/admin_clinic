<aside id="sidebar">
    <button type="button" id="sidebar-close" class="icon sidebar-close"><i class="fs-5 ph ph-x"></i> <span
            class="visually-hidden">Fechar Menu</span></button>
    <a class="d-none d-sm-flex logo" href="{{ route('dashboard') }}">Renovar Admin 2.0</a>
    <div data-scrollbar>
        <ul class="menu">
            <li class="menu-title">Dashboards</li>
            <li><a href="{{ route('dashboard') }}"> <i class="ph ph-kanban"></i> <span>Graficos</span> </a></li>
            <li class="menu-title">Procedimentos</li>
            <li class="menu-sub"><a href> <i class="ph ph-syringe fs-1"></i> <span>Todos</span> </a>
                <ul>
                    <li><a href="{{ route('panel.procedures.index') }}">Pacotes</a></li>
                    <li><a href="{{ route('panel.procedures.index') }}">A vulso</a></li>
                </ul>
            </li>
            <li class="menu-title">Pacientes</li>
            <li class="menu-sub"><a href> <i class="ph ph-user-list"></i> <span>Lista</span> </a>
                <ul>
                    <li><a href="{{ route('panel.patient.index') }}">Todos</a></li>
                    <li><a href="#">Novo paciente</a></li>
                </ul>
            </li>
            <li class="menu-title">Operacional</li>
            <li><a href="{{ route('panel.schedules.index') }}"> <i class="ph ph-calendar-blank"></i> <span>Agendamentos</span></a></li>
            <li><a href="#"> <i class="ph ph-calendar-blank"></i> <span>Calendario</span></a></li>
            <li><a href="#"> <i class="ph ph-stethoscope"></i> <span>Triagens</span></a></li>
            <li class="menu-title">Vendas</li>
            <li><a href="{{ route('panel.sales-order.index') }}"> <i class="ph ph-shopping-bag-open"></i> <span>Pedido de Vendas</span></a></li>
            <li><a href="{{ route('panel.sales-order.create') }}"> <i class="ph ph-shopping-cart"></i> <span>Novo Pedido</span></a></li>
            <li class="menu-title">Marketing</li>
            <li class="menu-sub"><a href> <i class="ph ph-whatsapp-logo"></i> <span>Whatsapp</span> </a>
                <ul>
                    <li><a href="{{ route('panel.campaigns.index') }}"><i class="ph ph-whatsapp-logo"></i>Lista</a></li>
                    <li><a href="{{ route('panel.campaign.create') }}"><i class="ph ph-whatsapp-logo"></i>Novo</a></li>
                </ul>
            </li>
            <li class="menu-sub"><a href> <i class="ph ph-chat-circle-text"></i> <span>Mensagens</span> </a>
                <ul>
                    <li><a href="{{ route('panel.campaigns.index') }}">Lista</a></li>
                    <li><a href="#">Novo</a></li>
                    <li><a href="#">Enviar</a></li>
                </ul>
            </li>
            <li class="menu-title">Contatos</li>
            <li class="menu-sub"><a href> <i class="ph ph-whatsapp-logo"></i> <span>Whatsapp</span> </a>
                <ul>
                    <li><a href=""><i class="ph ph-whatsapp-logo"></i>Lista</a></li>
                </ul>
            </li>
        </ul>
    </div>
</aside>
