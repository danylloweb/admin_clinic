<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="{{ asset('accordion.8001c1c2.css') }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renovar | Login</title>
    <link rel="icon" type="icon" href="{{ asset('img/favicon.ico') }}">
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
<body class="align-items-center d-flex p-5">
<div class="toast-container position-fixed top-0 end-0 p-3 z-3" id="toast-container"></div>
<div class="card m-auto mw-400 p-8 w-100"><h2 class="fs-6 text-body-emphasis">Bem vindo ao Admin!</h2>
    <div class="mb-5 text-body-secondary">Entre com suas credenciais</div>
    <form id="loginForm" class="mb-5">
        <div class="mb-3 position-relative">
            <i class="fs-3 left-0 m-2.5 ph ph-user-circle position-absolute text-body-secondary top-0"></i>
            <input type="email" name="email" class="form-control ps-10" placeholder="E-mail" required>
        </div>
        <div class="mb-5 position-relative">
            <i class="fs-3 left-0 m-2.5 ph ph-keyhole position-absolute text-body-secondary top-0"></i>
            <input type="password" name="password" class="form-control ps-10" placeholder="Senha" required>
        </div>
        <button type="submit" class="bg-opacity-75 btn btn-secondary w-100">Entrar</button>
    </form>

</div>
<script>
    const defaultTheme = "8";
    let theme = localStorage.getItem("sa-theme") || defaultTheme;
    const validThemes = ["1", "2", "3", "4", "5", "6", "7", "8"];
    if (!validThemes.includes(theme)) {
        theme = defaultTheme;
        localStorage.setItem("sa-theme", theme);
    }
    document.documentElement.setAttribute("data-sa-theme", theme);
</script>
{{-- Scripts principais --}}
<script src="{{ asset('js/index.2675e9f1.js') }}" type="module"></script>
<script src="{{ asset('js/index.ea66387c.js') }}" nomodule defer></script>
<script src="{{ asset('js/vendor.353a377b.js') }}" type="module"></script>
<script src="{{ asset('js/vendor.07ba7954.js') }}" nomodule defer></script>
</body>
<script>
    document.getElementById("loginForm").addEventListener("submit", async function (e) {
        e.preventDefault();

        const form     = this;
        const email    = form.email.value;
        const password = form.password.value;

        const btn = form.querySelector("button[type='submit']");
        const originalText = btn.innerHTML;

        // Desabilita botão e inputs
        btn.disabled = true;
        btn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        Entrando...
    `;
        Array.from(form.elements).forEach(el => el.disabled = true);

        try {
            const res = await fetch("{{ route('login') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ email, password })
            });

            const data = await res.json();

            if (!res.ok) {
                showToast("Usuário e senha incorretos: " + (data.message || "Verifique suas credenciais"), "danger");

                // Reativa botão e inputs
                btn.disabled = false;
                btn.innerHTML = originalText;
                Array.from(form.elements).forEach(el => el.disabled = false);
                return;
            }

            localStorage.setItem("token", data.token);
            document.cookie = "jwt_token=" + data.token + "; path=/";

            showToast("Login realizado com sucesso! Redirecionando...", "success");

            await fetchUserData(data.token);

            setTimeout(() => {
                window.location.href = "/dashboard";
            }, 2500);

        } catch (err) {
            showToast("Erro inesperado. Tente novamente.", "danger");
            console.error(err);

            // Reativa botão e inputs em caso de erro inesperado
            btn.disabled = false;
            btn.innerHTML = originalText;
            Array.from(form.elements).forEach(el => el.disabled = false);
        }
    });

    // 🔹 Busca dados do usuário autenticado
    async function fetchUserData(token) {
        try {
            const res = await fetch('/dashboard-data', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (res.ok && data.user) {
                localStorage.setItem("user", JSON.stringify(data.user));
                console.log("Usuário:", data.user);
            } else {
                console.warn("Erro ao obter usuário:", data.message);
            }
        } catch (err) {
            console.error("Erro ao buscar dados do usuário:", err);
        }
    }

    // 🔹 Função auxiliar para exibir toast
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
</html>
