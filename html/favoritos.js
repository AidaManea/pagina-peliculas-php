// --- MANEJO DE FAVORITOS Y VISTAS (LocalStorage) ---
// Aqui guardamos las peliculas para que se queden grabadas en el navegador del usuario.
// He usado nombres de claves un poco largos para que no choquen con otras webs.

var CLAVE_FAVORITOS = "filmoraFavorites";
var CLAVE_VISTAS = "filmoraSeen";

// Sacamos los favoritos del navegador. Si no hay nada, devolvemos un array vacio [].
function obtenerFavoritos() {
  try {
    var datos = localStorage.getItem(CLAVE_FAVORITOS);
    if (datos) {
      return JSON.parse(datos);
    } else {
      return [];
    }
  } catch (e) {
    // Si falla el parseo o algo, mejor devolver vacio para que no explote la web
    return [];
  }
}

// Guardamos la lista convirtiendola a texto (JSON)
function guardarFavoritos(lista) {
  localStorage.setItem(CLAVE_FAVORITOS, JSON.stringify(lista));
}

// Lo mismo para las pelis que ya hemos visto
function obtenerVistas() {
  try {
    var datos = localStorage.getItem(CLAVE_VISTAS);
    if (datos) {
      return JSON.parse(datos);
    } else {
      return [];
    }
  } catch (e) {
    return [];
  }
}

function guardarVistas(lista) {
  localStorage.setItem(CLAVE_VISTAS, JSON.stringify(lista));
}

// Para saber rapido si una peli ya esta marcada como vista por su ID
function esVista(id) {
  var vistas = obtenerVistas();
  for (var i = 0; i < vistas.length; i++) {
    if (vistas[i].id === id) {
      return true;
    }
  }
  return false;
}

// Esta funcion añade la peli si no esta, o la quita si ya la habiamos visto
function cambiarVista(peli) {
  var vistas = obtenerVistas();
  var indice = -1;
  
  // Buscamos la peli en nuestra lista
  for (var i = 0; i < vistas.length; i++) {
    if (vistas[i].id === peli.id) {
      indice = i;
      break;
    }
  }

  if (indice >= 0) {
    vistas.splice(indice, 1); // La quitamos
  } else {
    vistas.push(peli); // La añadimos
  }
  guardarVistas(vistas);

  // Tambien le avisamos al servidor por si acaso para que lo guarde en la cuenta
  var datosForm = new FormData();
  datosForm.append('title', peli.title);
  datosForm.append('genero', peli.genre || 'Desconocido');
  datosForm.append('director', peli.director || 'Desconocido');
  datosForm.append('duracion', peli.duration || 0);
  datosForm.append('descripcion', peli.description || 'Sin descripción');
  datosForm.append('valoracion', peli.rating || 0);
  datosForm.append('año', peli.formattedDate || (peli.release_date + '-01-01'));

  // Usamos fetch para mandarlo al PHP
  fetch('../PHP/Peliculas/marcarVista.php', {
    method: 'POST',
    body: datosForm,
    credentials: 'include' // Esto es para que sepa que somos nosotros (la sesion)
  })
  .catch(function(error) {
    console.warn('¡Uy! No he podido guardar la peli vista en el servidor.', error);
  });
}

// Comprobamos si es favorita
function esFavorita(id) {
  var favs = obtenerFavoritos();
  for (var i = 0; i < favs.length; i++) {
    if (favs[i].id === id) {
      return true;
    }
  }
  return false;
}

// Lo mismo que cambiarVista pero para favoritas
function cambiarFavorito(peli) {
  var favs = obtenerFavoritos();
  var indice = -1;
  
  for (var i = 0; i < favs.length; i++) {
    if (favs[i].id === peli.id) {
      indice = i;
      break;
    }
  }

  if (indice >= 0) {
    favs.splice(indice, 1);
  } else {
    favs.push(peli);
  }
  guardarFavoritos(favs);

  // Mandamos los datos al servidor. Aqui tambien pasamos el año formateado.
  var datosForm = new FormData();
  datosForm.append('title', peli.title);
  datosForm.append('genero', peli.genre || 'Desconocido');
  datosForm.append('director', peli.director || 'Desconocido');
  datosForm.append('duracion', peli.duration || 0);
  datosForm.append('descripcion', peli.description || 'Sin descripción');
  datosForm.append('valoracion', peli.rating || 0);
  datosForm.append('año', peli.formattedDate || (peli.release_date + '-01-01'));

  fetch('../PHP/Peliculas/marcarFavorita.php', {
    method: 'POST',
    body: datosForm,
    credentials: 'include'
  })
  .catch(function() {
    console.warn('Ha fallado el guardado de favoritos en la base de datos.');
  });
}

// --- CREACION DE LAS TARJETAS DE PELICULAS ---
// Esta funcion es la que genera todo el cuadradito con la imagen y el titulo
function createMovieCard(peli, mostrarFavs, mostrarVistas, mostrarBorrar) {
  // Creamos el elemento "article" que sera el contenedor principal
  var card = document.createElement("article");
  card.className = "pelicula-card";

  // Pillamos la imagen. Si no hay, podriamos poner un icono de claqueta
  var urlImagen = peli.image || peli.movie_banner || "";
  var favorita = esFavorita(peli.id);
  var vista = esVista(peli.id);

  // Montamos los botones de los iconos (estrella y visto)
  var botonesExtra = "";
  if (mostrarFavs !== false) {
    var claseFav = favorita ? "active" : "";
    botonesExtra += '<button type="button" class="btn-fav ' + claseFav + '" title="Añadir a favoritas"><span class="icon">★</span></button>';
  }
  
  if (mostrarVistas !== false) {
    var claseVista = vista ? "active" : "";
    botonesExtra += '<button type="button" class="btn-seen ' + claseVista + '" title="Marcar como vista"><span class="icon">V</span></button>';
  }

  // Si hay imagen, la ponemos de fondo del poster
  var estiloPoster = urlImagen ? 'style="background-image:url(' + urlImagen + ');background-size:cover;background-position:center;"' : "";
  
  // Aqui montamos todo el HTML de dentro
  card.innerHTML = 
    '<div class="poster" ' + estiloPoster + '>' +
      (!urlImagen ? "🎬" : "") +
      botonesExtra +
    '</div>' +
    '<div class="pelicula-body">' +
      '<h3>' + peli.title + '</h3>' +
      '<p>' + (peli.release_date || "") + (peli.director ? " · " + peli.director : "") + '</p>' +
    '</div>';

  // Si nos piden el boton de borrar (en la lista de edicion)
  if (mostrarBorrar === true) {
     var divEdicion = document.createElement("div");
     divEdicion.className = "edit-controls";
     divEdicion.style.display = "none";
     divEdicion.innerHTML = '<button type="button" class="btn-delete">Quitar de mi lista</button>';
     card.appendChild(divEdicion);

     var btnBorrar = divEdicion.querySelector(".btn-delete");
     btnBorrar.addEventListener("click", function() {
         // Si borra, lo quitamos de favoritos y de la pantalla
         cambiarFavorito(peli);
         card.remove(); 
         // Si la lista se queda vacia enseñamos el mensaje de "No hay nada"
         if (document.querySelectorAll('#listaFavoritas .pelicula-card').length === 0) {
             var vacia = document.getElementById("listaVacia");
             if (vacia) vacia.style.display = "block";
         }
     });
  }

  // Ponemos el click al boton de favoritos
  if (mostrarFavs !== false) {
    var btn = card.querySelector(".btn-fav");
    btn.addEventListener("click", function(e) {
      e.stopPropagation(); // que no se abra la peli (si pusieramos enlace)
      cambiarFavorito(peli);
      btn.classList.toggle("active");
    });
  }

  // Y al boton de pelis vistas
  if (mostrarVistas !== false) {
    var btnVista = card.querySelector(".btn-seen");
    btnVista.addEventListener("click", function(e) {
      e.stopPropagation();
      cambiarVista(peli);
      btnVista.classList.toggle("active");
    });
  }

  return card;
}

