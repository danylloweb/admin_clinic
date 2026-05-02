<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{ asset('accordion.8001c1c2.css') }}">
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link rel="icon" type="icon" href="{{ asset('icons/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <!-- Imagens pré-carregadas -->
    <link rel="preload" as="image" href="{{ asset('1.a60d8661.jpg') }}" type="image/jpeg">
    <link rel="preload" as="image" href="{{ asset('2.c59b7c56.jpg') }}" type="image/jpeg">
    <link rel="preload" as="image" href="{{ asset('3.e41aa4f9.jpg') }}" type="image/jpeg">
    <link rel="preload" as="image" href="{{ asset('4.a142bcad.jpg') }}" type="image/jpeg">
    <link rel="preload" as="image" href="{{ asset('5.898434bc.jpg') }}" type="image/jpeg">
    <link rel="preload" as="image" href="{{ asset('6.98e0f35c.jpg') }}" type="image/jpeg">
    <link rel="preload" as="image" href="{{ asset('7.368c39ac.jpg') }}" type="image/jpeg">
    <link rel="preload" as="image" href="{{ asset('8.8d1edcfe.jpg') }}" type="image/jpeg">
</head>
<body>
<div id="page-loader">
    <div class="h-12 spinner-border w-12" role="status"><span class="visually-hidden">Carregando...</span></div>
</div>
<div class="toast-container position-fixed top-0 end-0 p-3 z-3" id="toast-container"></div>
<header class="header">
    <button type="button" class="d-xl-none icon me-2 ms-n2.5 sidebar-toggle">
        <svg width="15" height="12" fill="none" class="pe-none" viewBox="0 0 16 13">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M1 1h14M1 6.25h9.546M1 11.5h14"/>
        </svg>
        <span class="visually-hidden">Open Sidebar</span></button>
    <a class="d-none d-sm-block logo" href="{{route('dashboard')}}"><img src="{{ asset('img/logo reduzida.png') }}"  width="80px"></a> <i class="ms-auto"></i>
    <div class="content-search d-lg-flex d-none"><i class="fs-5 ph ph-magnifying-glass"></i>
        <input type="text" class="form-control" placeholder="Pesquisar...">
    </div>
    <ul class="header-menu ms-6 ms-xl-10">
        <li class="dropdown header-notify">
            <button type="button" class="ph ph-bell" data-bs-toggle="dropdown" data-bs-display="static"
                    data-bs-auto-close="outside" aria-expanded="false"><span
                    class="visually-hidden">Notificações</span></button>
            <div class="dropdown-menu header-dropdown-menu">
                <div class="align-items-center d-flex flex-shrink-0 h-11 px-4">
                    <div class="fw-medium text-body-emphasis">Notificações</div>
                    <div class="d-flex gap-px me-n2 ms-auto">
                        <button type="button" class="icon ph ph-check-circle"><span
                                class="visually-hidden">Marcar como Lido</span></button>
                        <button type="button" class="icon ph ph-app-window"><span class="visually-hidden">Abrir Notificações</span>
                        </button>
                        <button type="button" class="icon ph ph-gear"><span class="visually-hidden">Configurações</span>
                        </button>
                    </div>
                </div>
                <div class="flex-grow-1 mx-n1.5 pb-1 px-3" data-scrollbar>
                    <div id="top-notifications"></div>
                </div>
            </div>
        </li>
        <li class="d-none d-sm-block dropdown">
            <button type="button" class="ph ph-check-square-offset" data-bs-toggle="dropdown" data-bs-display="static"
                    data-bs-auto-close="outside" aria-expanded="false"><span class="visually-hidden">Tarefas</span>
            </button>
            <div class="dropdown-menu header-dropdown-menu">
                <div class="align-items-center d-flex flex-shrink-0 h-11 mb-2 px-4">
                    <div class="fw-medium text-body-emphasis">Ongoing Tasks</div>
                    <div class="d-flex gap-px me-n2 ms-auto">
                        <button type="button" class="icon ph ph-check-circle"><span
                                class="visually-hidden">Configurações</span></button>
                        <button type="button" class="icon ph ph-app-window"><span
                                class="visually-hidden">Open Tasks</span></button>
                        <button type="button" class="icon ph ph-gear"><span class="visually-hidden">Configurações</span>
                        </button>
                    </div>
                </div>
                <div class="flex-grow-1 mx-n1.5 pb-1 px-3" data-scrollbar>
                    <div id="top-tasks"></div>
                </div>
            </div>
        </li>
        <li class="dropdown">
            <button type="button" class="h-11 me-n0.5 p-2 rounded w-11" data-bs-toggle="dropdown" aria-expanded="false">
                <img id="user-avatar" class="h-8 rounded" src="" alt="Avatar do usuário">
            </button>
            <div class="dropdown-menu p-2">
                <div class="px-3 py-2">
                    <strong id="user-name" class="d-block text-dark fs-sm"></strong>
                    <small class="text-muted" id="user-email"></small>
                </div>
                <hr class="my-1">
                <a href="#" class="dropdown-item"><i class="ph ph-user-circle"></i> Profile</a>
                <a href="#" class="dropdown-item" onclick="logoutUser()"><i class="ph ph-sign-out"></i> Sair</a>
            </div>
        </li>
    </ul>
</header>
@include('layouts.aside')
<header class="content-header"><h2 class="fs-6 m-0 ps-3 text-body-emphasis">Inicio</h2>
    <nav aria-label="breadcrumb" class="d-none d-sm-flex ms-6">
        <ol class="breadcrumb fs-7 mt-0.5">
            <li class="breadcrumb-item"><a href="#">{{ $title }}</a></li>
            <li class="active breadcrumb-item" aria-current="page">{{ $subtitle }}</li>
        </ol>
    </nav>
    <i class="ms-auto"></i>
    @php
        use Carbon\Carbon;
        $now = Carbon::now();
        $start = $now->copy()->startOfWeek()->subDay();
        $end   = $now->copy()->endOfWeek()->subDay();
    @endphp

    <div class="d-md-flex d-none date-range-picker date-range-picker-body fs-7">
        <i class="fs-3 me-2 ph ph-clock position-relative"></i>
        <input type="text" name="start" value="{{ $start->format('m/d/Y') }}" class="form-control-plaintext w-20" required readonly>
        <span class="mx-n2">-</span>
        <input type="text" name="end" value="{{ $end->format('m/d/Y') }}" class="form-control-plaintext text-end w-20" required readonly>
    </div>

    <div class="align-items-center d-flex gap-1 ms-3">
        <a href="{{ $routeCreate??"#" }}" class="icon icon-subtle ph ph-plus-circle"></a>
        <a href class="icon icon-subtle ph ph-download" ></a>
        <a href class="icon icon-subtle ph ph-gear"></a>
    </div>
</header>
<div id="content" data-scrollbar>
 @yield('content')
</div>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/index.2675e9f1.js') }}" type="module"></script>
<script src="{{ asset('js/index.ea66387c.js') }}" nomodule defer></script>
<script src="{{ asset('js/vendor.353a377b.js') }}" type="module"></script>
<script src="{{ asset('js/vendor.07ba7954.js') }}" nomodule defer></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"></script>
<script>
    function showToast(message, type = "info") {
        const toastId = "toast-" + Date.now();
        const container = document.getElementById("toast-container");

        const toast = document.createElement("div");
        toast.className = `toast align-items-center text-white bg-${type} border-0 show mb-2`;
        toast.setAttribute("role", "alert");
        toast.setAttribute("id", toastId);

        toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            <span class="spinner-border spinner-border-sm me-1 mt-1"
                role="status" aria-hidden="true">
            </span>
        </div>
    `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.classList.remove("show");
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

</script>
@stack('scripts')
</body>
</html>
