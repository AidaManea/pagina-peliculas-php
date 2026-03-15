// --- TESTS DE FUNCIONALIDAD ---
// Este archivo sirve para comprobar que todo esta bien sin tener que hacerlo a mano mil veces.
// Solo lo usamos nosotros para debuguear.

function ejecutarPruebas() {
    console.log("%c INICIANDO PRUEBAS DE FILMORA ", "background: #222; color: #bada55");

    // 1. Probar que el LocalStorage funciona (favoritos)
    try {
        localStorage.setItem("test_fav", "peli_prueba");
        if (localStorage.getItem("test_fav") === "peli_prueba") {
            console.log("✅ LocalStorage: OK");
        } else {
            console.error("❌ LocalStorage: Ha fallado");
        }
        localStorage.removeItem("test_fav");
    } catch (e) {
        console.error("❌ LocalStorage: Error grave", e);
    }

    // 2. Probar conexion a la API de OMDB
    var API_KEY = "b5ae98fe";
    fetch("https://www.omdbapi.com/?apikey=" + API_KEY + "&t=Batman")
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.Response === "True") {
                console.log("✅ API OMDB: Conexión establecida correctamente");
            } else {
                console.error("❌ API OMDB: Error en la respuesta", data);
            }
        })
        .catch(function(err) {
            console.error("❌ API OMDB: Error de conexion", err);
        });

    // 3. Probar si el servidor responde
    fetch("../PHP/getPerfilDatos.php", { credentials: "include" })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.hasOwnProperty("success")) {
                console.log("✅ Servidor PHP: Respondiendo bien");
                console.log("Sesion actual:", data.success ? "Logueado" : "No logueado");
            } else {
                console.error("❌ Servidor PHP: Respuesta rara", data);
            }
        })
        .catch(function() {
            console.error("❌ Servidor PHP: No se puede conectar");
        });
}

// Lo lanzamos al cargar si estamos en modo debug
// ejecutarPruebas();
