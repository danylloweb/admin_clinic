// 🔹 Função carregamento de tema
localStorage.setItem("sa-theme", "8");
document.documentElement.setAttribute("data-sa-theme", "8");
// 🔹 Função carregamento de usuário
document.addEventListener("DOMContentLoaded", function () {
    const user = JSON.parse(localStorage.getItem("user"));
    if (user) {
        document.getElementById("user-avatar").src = user.img || "1.e810f372.jpg";
        document.getElementById("user-name").textContent = user.name || "Usuário";
        document.getElementById("user-email").textContent = user.email || "";
    }
});
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
        </div>
    `;
    container.appendChild(toast);
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
// 🔹 Função logout User
function logoutUser() {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    document.cookie = "jwt_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
    window.location.href = "/login";
}

function disableForm(formElement, loadingText = "Enviando...", toast = false) {
    if (toast){
        showToast("Enviando...", "info");
    }
    const submitButton = formElement.querySelector("button[type='submit']");
    if (!submitButton) return null;

    const originalText = submitButton.innerHTML;

    submitButton.disabled = true;
    submitButton.innerHTML = `
        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        ${loadingText}
    `;

    Array.from(formElement.elements).forEach(el => el.disabled = true);

    return originalText; // retorna o texto original para usar no enableForm
}

function enableForm(formElement, originalButtonText) {
    const submitButton = formElement.querySelector("button[type='submit']");
    if (!submitButton) return;

    submitButton.disabled = false;
    submitButton.innerHTML = originalButtonText;

    Array.from(formElement.elements).forEach(el => el.disabled = false);
}
