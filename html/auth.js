// funcion para enseñar error
function showError(element, message) {
  if (!element) return;
  element.textContent = message;
  element.style.display = "block";
}

// funcion para ocultar error
function clearError(element) {
  if (!element) return;
  element.textContent = "";
  element.style.display = "none";
}

// mirar si esta vacio
function isEmpty(value) {
  return !value || value.trim().length === 0;
}

function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(String(email).toLowerCase());
}

document.addEventListener("DOMContentLoaded", () => {
  // Registro
  const registroForm = document.getElementById("registroForm");
  if (registroForm) {
    const nombre = document.getElementById("nombre");
    const apellido = document.getElementById("apellido");
    const usuario = document.getElementById("usuario");
    const email = document.getElementById("email");
    const pass = document.getElementById("pass");
    const repPass = document.getElementById("rep-pass");

    registroForm.addEventListener("submit", (e) => {
      e.preventDefault(); // Prevent default form submission

      if (isEmpty(nombre?.value) || isEmpty(apellido?.value) || isEmpty(usuario?.value) || isEmpty(email?.value) || isEmpty(pass?.value) || isEmpty(repPass?.value)) {
        alert("Faltan datos por rellenar en el formulario.");
        return;
      }
      
      if (!isValidEmail(email.value)) {
        alert("Por favor, introduce un correo electrónico válido.");
        return;
      }

      if (pass.value !== repPass.value) {
        alert("Las contraseñas no coinciden.");
        return;
      }

      const formData = new FormData(registroForm);

      // Use a cache-busting param just in case
      fetch(`../PHP/registro.php?v=${Date.now()}`, {
        method: "POST",
        body: formData,
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert("¡Registro completado con éxito! Se te enviará al inicio.");
          window.location.href = "index.html";
        } else {
          alert(data.message || "Error al registrarse.");
          // Si el usuario ya existe, redirigir al login según solicitado por el usuario.
          if (data.message && data.message.includes("ya están registrados")) {
            window.location.href = "login.html";
          }
        }
      })
      .catch(err => {
        console.error("Error:", err);
        alert("Ocurrió un error inesperado al contactar con el servidor.");
      });
    });
  }

  // Login
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    const loginUser = document.getElementById("loginUser");
    const loginEmail = document.getElementById("loginEmail");
    const loginPass = document.getElementById("loginPass");
    const errorBox = document.getElementById("loginError");

    loginForm.addEventListener("submit", (e) => {
      e.preventDefault(); // Prevent default form submission
      clearError(errorBox);

      if (isEmpty(loginUser?.value) || isEmpty(loginEmail?.value) || isEmpty(loginPass?.value)) {
        showError(errorBox, "Introduce usuario, correo y contraseña.");
        return;
      }
      
      if (!isValidEmail(loginEmail.value)) {
        showError(errorBox, "Por favor, introduce un correo electrónico válido.");
        return;
      }

      const formData = new FormData(loginForm);

      fetch(`../PHP/login.php?v=${Date.now()}`, {
        method: "POST",
        body: formData,
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          window.location.href = "index.html";
        } else {
          showError(errorBox, data.message || "Usuario o contraseña incorrectos.");
        }
      })
      .catch(err => {
        console.error("Error:", err);
        showError(errorBox, "Ocurrió un error inesperado al contactar con el servidor.");
      });
    });
  }
});
