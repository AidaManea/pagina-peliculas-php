// Estas funciones sirven para que el usuario sepa si ha escrito algo mal
function enseñarError(elemento, mensaje) {
  if (elemento) {
    elemento.textContent = mensaje;
    elemento.style.display = "block"; // Lo ponemos a la vista
  }
}

function limpiarError(elemento) {
  if (elemento) {
    elemento.textContent = "";
    elemento.style.display = "none"; // Lo escondemos de nuevo
  }
}

// Una funcion sencilla para ver si el campo esta vacio
function estaVacio(valor) {
  return !valor || valor.trim().length === 0;
}

// Aqui validamos el email con una "expresion regular", que es un poco lioso pero funciona
function esEmailValido(email) {
  var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(String(email).toLowerCase());
}

document.addEventListener("DOMContentLoaded", function () {
  
  // --- PARTE DEL REGISTRO ---
  var registroForm = document.getElementById("registroForm");
  if (registroForm) {
    registroForm.addEventListener("submit", function (e) {
      e.preventDefault(); // Evitamos que la pagina se recargue sola al darle al boton

      // Cogemos todos los inputs del formulario por su ID
      var nombre = document.getElementById("nombre");
      var apellido = document.getElementById("apellido");
      var usuario = document.getElementById("usuario");
      var email = document.getElementById("email");
      var pass = document.getElementById("pass");
      var repPass = document.getElementById("rep-pass");

      console.log("Intentando registrar a:", usuario.value);

      // Comprobamos que el usuario no se haya dejado ningun campo en blanco
      if (estaVacio(nombre.value) || estaVacio(apellido.value) || estaVacio(usuario.value) || 
          estaVacio(email.value) || estaVacio(pass.value) || estaVacio(repPass.value)) {
        alert("¡Oye! Tienes que rellenar todos los campos del formulario.");
        return;
      }
      
      // Validamos que el email tenga un formato correcto
      if (!esEmailValido(email.value)) {
        alert("Parece que ese correo no es valido. Escribelo bien, porfa.");
        return;
      }

      // Miramos que la contraseña sea la misma en los dos campos
      if (pass.value !== repPass.value) {
        alert("Las contraseñas no coinciden. ¡Revisalo!");
        return;
      }

      // Creamos los datos para enviarlos al PHP
      var datos = new FormData(registroForm);

      // Hacemos la llamada al servidor
      fetch("../PHP/registro.php", {
        method: "POST",
        body: datos,
        credentials: "include" // Importante para que se guarde la sesion
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (data.success) {
          alert("¡Todo perfecto! Ya estas registrado. Vamos al inicio.");
          window.location.href = "index.html";
        } else {
          alert(data.message || "Algo ha fallado al intentar registrarte.");
          // Si el usuario ya existe lo mandamos al login
          if (data.message && data.message.indexOf("ya existen") !== -1) {
            window.location.href = "login.html";
          }
        }
      })
      .catch(function(error) {
        console.log("Error raro en el registro:", error);
        alert("Parece que ha habido un error al conectar con el servidor.");
      });
    });
  }

  // --- PARTE DEL LOGIN ---
  var loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      e.preventDefault(); // Que no se recargue la pagina todavia
      
      var errorBox = document.getElementById("loginError");
      limpiarError(errorBox);

      // Pillamos los datos de acceso
      var user = document.getElementById("loginUser");
      var email = document.getElementById("loginEmail");
      var pass = document.getElementById("loginPass");

      console.log("Pidiendo login para:", user.value);

      // Validacion rapida antes de enviar nada
      if (estaVacio(user.value) || estaVacio(email.value) || estaVacio(pass.value)) {
        enseñarError(errorBox, "Tienes que poner el usuario, el correo y la contraseña.");
        return;
      }
      
      if (!esEmailValido(email.value)) {
        enseñarError(errorBox, "Ese correo electronico no parece valido.");
        return;
      }

      var datos = new FormData(loginForm);

      // Llamada al PHP de login
      fetch("../PHP/login.php", {
        method: "POST",
        body: datos,
        credentials: "include" // Esto es clave para que no nos eche luego
      })
      .then(function(res) { return res.json(); })
      .then(function(data) {
        console.log("Respuesta del login:", data);
        if (data.success) {
          // Si ha ido bien, nos vamos a la pagina principal
          window.location.href = "index.html";
        } else {
          // Si no, enseñamos el mensaje que nos mande el PHP
          enseñarError(errorBox, data.message || "Usuario o clave incorrectos.");
        }
      })
      .catch(function(error) {
        console.log("Error al intentar logearse:", error);
        enseñarError(errorBox, "Vaya, parece que no puedo conectar con el servidor.");
      });
    });
  }
});
