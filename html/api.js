// --- CONEXION CON LA API DE OMDB ---
// Aqui es donde traemos las pelis de internet para que la web no este vacia al principio.

var CLAVE_API = "b5ae98fe";

document.addEventListener("DOMContentLoaded", function() {
  
  // Buscamos donde vamos a meter las pelis
  var grid = document.querySelector(".peliculas-grid");
  if (!grid) {
    return; // Si no estamos en la pagina que tiene el grid, nos salimos
  }

  // Vaciamos el contenedor por si acaso tiene algo
  grid.innerHTML = "";

  // Lista de pelis que queremos que aparezcan al principio
  var titulos = [
    "Inception", "The Dark Knight", "Interstellar", "Pulp Fiction",
    "The Matrix", "Fight Club", "Forrest Gump", "Titanic", "Gladiator",
    "Jurassic Park", "The Lion King", "Avengers: Endgame"
  ];

  console.log("Cargando peliculas populares desde OMDB...");

  // Recorremos la lista y pedimos cada una a la API
  for (var i = 0; i < titulos.length; i++) {
    var tituloActual = titulos[i];
    
    // Usamos fetch para llamar a OMDB
    fetch("https://www.omdbapi.com/?apikey=" + CLAVE_API + "&type=movie&t=" + encodeURIComponent(tituloActual))
      .then(function(res) { return res.json(); })
      .then(function(data) {
        if (data && data.Response === "True") {
          
          // Formateamos un poco la fecha porque la DB es tiquismiquis con eso
          var anio = data.Year + "-01-01";
          if (data.Released && data.Released !== "N/A") {
              anio = new Date(data.Released).toISOString().split('T')[0];
          }

          // Creamos un objeto ordenado con los datos que nos interesan
          var peliObj = {
            id: data.imdbID,
            title: data.Title,
            release_date: data.Year,
            director: data.Director || "",
            image: data.Poster !== "N/A" ? data.Poster : "",
            genre: data.Genre || "Desconocido",
            duration: parseInt((data.Runtime || "").replace(" min", "")) || 0,
            description: data.Plot || "Sin descripción",
            rating: parseFloat(data.imdbRating) || 0,
            formattedDate: anio
          };

          // Tambien la guardamos en nuestra base de datos por si la necesitamos luego
          var datosForm = new FormData();
          datosForm.append('titulo', peliObj.title);
          datosForm.append('genero', peliObj.genre);
          datosForm.append('duracion', peliObj.duration);
          datosForm.append('descripcion', peliObj.description);
          datosForm.append('año', peliObj.formattedDate);
          datosForm.append('director', peliObj.director);
          datosForm.append('valoracion', peliObj.rating);

          // Llamamos al PHP que guarda las pelis
          fetch('../PHP/Peliculas/apiGuardarPelicula.php', { method: 'POST', body: datosForm })
            .catch(function() { 
              console.log("No se pudo guardar " + peliObj.title + " en nuestra DB"); 
            });

          // Usamos la funcion que esta en favoritos.js para crear la tarjetita
          var card = createMovieCard(peliObj, true, true, false);
          grid.appendChild(card);
        }
      })
      .catch(function(err) {
        console.error("Vaya, parece que la API de OMDB ha fallado:", err);
      });
  }
});
