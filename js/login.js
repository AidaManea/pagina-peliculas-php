// =====================================================================
// VALIDACIÓN LOGIN
// =====================================================================
function validarLogin(e) {
    e.preventDefault();

    const nombre = document.getElementById('nombre')?.value.trim() ?? '';
    const correo = document.getElementById('email')?.value.trim() ?? '';
    const contrasena = document.getElementById('pass')?.value ?? '';

    if (nombre === '') {
        mostrarError('⚠️ Introduce tu nombre de usuario.');
        return;
    }
    if (!correo.includes('@') || correo === '') {
        mostrarError('⚠️ El correo electrónico no es válido.');
        return;
    }
    if (contrasena === '') {
        mostrarError('⚠️ La contraseña no puede estar vacía.');
        return;
    }

    // Enviar al PHP mediante fetch
    const form = document.querySelector('form');
    const data = new FormData(form);

    fetch('../PHP/login.php', {
        method: 'POST',
        body: data
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            mostrarExito('✅ Login correcto. Cargando tu inicio...');
            setTimeout(() => { window.location.href = 'index.html'; }, 800);
        } else {
            mostrarError(res.message || '⚠️ Usuario o contraseña incorrectos.');
        }
    })
    .catch(() => {
        mostrarError('⚠️ No se pudo conectar con el servidor.');
    });
}

// =====================================================================
// VALIDACIÓN REGISTRO
// =====================================================================
function validarRegistro(e) {
    e.preventDefault();

    const nombre = document.getElementById('nombre')?.value.trim() ?? '';
    const correo = document.getElementById('email')?.value.trim() ?? '';
    const contrasena = document.getElementById('pass')?.value ?? '';
    const repContrasena = document.getElementById('rep-pass')?.value ?? '';

    if (nombre === '') {
        mostrarError('⚠️ Introduce un nombre de usuario.');
        return;
    }
    if (!correo.includes('@') || correo === '') {
        mostrarError('⚠️ El correo electrónico no es válido.');
        return;
    }
    if (contrasena.length < 6) {
        mostrarError('⚠️ La contraseña debe tener al menos 6 caracteres.');
        return;
    }
    if (contrasena !== repContrasena) {
        mostrarError('⚠️ Las contraseñas no coinciden.');
        return;
    }

    const form = document.querySelector('form');
    const data = new FormData(form);

    fetch('../PHP/registro.php', {
        method: 'POST',
        body: data
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            mostrarExito('✅ Cuenta creada. Entrando en la aplicación...');
            setTimeout(() => { window.location.href = 'index.html'; }, 1200);
        } else {
            mostrarError(res.message || '⚠️ Error al registrar. Inténtalo de nuevo.');
        }
    })
    .catch(() => {
        mostrarError('⚠️ No se pudo conectar con el servidor.');
    });
}

// =====================================================================
// HELPERS UI
// =====================================================================
function mostrarError(msg) {
    let el = document.getElementById('form-msg');
    if (!el) {
        el = document.createElement('p');
        el.id = 'form-msg';
        document.querySelector('form').prepend(el);
    }
    el.style.color = '#ff5c5c';
    el.style.fontWeight = '600';
    el.style.marginBottom = '10px';
    el.textContent = msg;
}

function mostrarExito(msg) {
    let el = document.getElementById('form-msg');
    if (!el) {
        el = document.createElement('p');
        el.id = 'form-msg';
        document.querySelector('form').prepend(el);
    }
    el.style.color = '#4caf50';
    el.style.fontWeight = '600';
    el.style.marginBottom = '10px';
    el.textContent = msg;
}

// =====================================================================
// ENGANCHAR EVENTO SEGÚN QUÉ PÁGINA ESTAMOS
// =====================================================================
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    if (!form) return;

    const esRegistro = !!document.getElementById('rep-pass');

    form.addEventListener('submit', (e) => {
        if (esRegistro) {
            validarRegistro(e);
        } else {
            validarLogin(e);
        }
    });
});
