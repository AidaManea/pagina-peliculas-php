function showError(element, message) {
  if (!element) return;
  element.textContent = message;
  element.style.display = "block";
}

function clearError(element) {
  if (!element) return;
  element.textContent = "";
  element.style.display = "none";
}

function isEmpty(value) {
  return !value || value.trim().length === 0;
}

document.addEventListener("DOMContentLoaded", () => {
  // Registro
  const registroForm = document.getElementById("registroForm");
  if (registroForm) {
    const username = document.getElementById("username");
    const email = document.getElementById("email");
    const pass = document.getElementById("pass");
    const repPass = document.getElementById("repPass");
    const errorBox = document.getElementById("registroError");

    registroForm.addEventListener("submit", (e) => {
      clearError(errorBox);

      if (isEmpty(username?.value) || isEmpty(email?.value) || isEmpty(pass?.value) || isEmpty(repPass?.value)) {
        e.preventDefault();
        showError(errorBox, "Rellena todos los campos antes de registrarte.");
        return;
      }

      if (pass.value !== repPass.value) {
        e.preventDefault();
        showError(errorBox, "Las contraseñas no coinciden.");
        return;
      }
    });
  }

  // Login
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    const loginUser = document.getElementById("loginUser");
    const loginPass = document.getElementById("loginPass");
    const errorBox = document.getElementById("loginError");

    loginForm.addEventListener("submit", (e) => {
      clearError(errorBox);

      if (isEmpty(loginUser?.value) || isEmpty(loginPass?.value)) {
        e.preventDefault();
        showError(errorBox, "Introduce usuario y contraseña.");
        return;
      }
    });
  }
});
