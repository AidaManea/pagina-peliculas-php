const OMDB_API_KEY = "a3638a31";
let todasLasPeliculas = [];

function filtrarPorAnio(lista, filtro) {
  if (filtro === "antes2000") {
    return lista.filter((p) => parseInt(p.release_date, 10) < 2000);
  }
  if (filtro === "2000_2010") {
    return lista.filter((p) => {
      const anio = parseInt(p.release_date, 10);
      return anio >= 2000 && anio <= 2010;
    });
  }
  if (filtro === "despues2010") {
    return lista.filter((p) => parseInt(p.release_date, 10) > 2010);
  }
  return lista;
}

function renderRecomendados() {
  const grid = document.getElementById("gridRecomendados");
  const select = document.getElementById("filtroAnio");
  if (!grid || !select) return;

  const filtro = select.value;
  grid.innerHTML = "";

  const listaFiltrada = filtrarPorAnio(todasLasPeliculas, filtro);

  if (!listaFiltrada.length) {
    grid.innerHTML = "<p>No hay películas para este filtro.</p>";
    return;
  }

  listaFiltrada.forEach((movie) => {
    const card = createMovieCard(movie, true, true, false);
    grid.appendChild(card);
  });
}

document.addEventListener("DOMContentLoaded", () => {
  const select = document.getElementById("filtroAnio");
  const grid = document.getElementById("gridRecomendados");
  if (!grid) return;

  //Lista de pelis recomendadas
  const recommendedTitles = [
    "Coco",
    "Inside Out",
    "Toy Story",
    "Toy Story 2",
    "Toy Story 3",
    "Finding Nemo",
    "Finding Dory",
    "Monsters, Inc.",
    "Monsters University",
    "The Incredibles",
    "The Incredibles 2",
    "Ratatouille",
    "Up",
    "WALL·E",
    "Zootopia",
    "Moana",
    "Frozen",
    "Frozen II",
    "Big Hero 6",
    "How to Train Your Dragon",
    "How to Train Your Dragon 2",
    "Kung Fu Panda",
    "Kung Fu Panda 2",
    "Shrek",
    "Shrek 2",
    "The Lego Movie",
    "La La Land",
    "Whiplash",
    "The Social Network",
    "Parasite",
    "Birdman",
    "Moonlight",
    "Green Book",
    "Spotlight",
    "The King\'s Speech",
    "Slumdog Millionaire",
    "The Pursuit of Happyness",
    "A Beautiful Mind",
    "Mad Max: Fury Road",
    "Blade Runner 2049",
    "Arrival",
    "Edge of Tomorrow",
    "Logan",
    "Dune",
    "Interstellar",
    "Inception",
    "Guardians of the Galaxy Vol. 2",
    "Black Panther",
    "Doctor Strange",
    "Spider-Man: Into the Spider-Verse",
    "The Batman",
    "Knives Out",
    "Baby Driver",
    "The Prestige",
    "Prisoners",
    "No Country for Old Men",
    "There Will Be Blood",
    "The Conjuring",
    "The Conjuring 2",
    "Insidious",
    "Hereditary",
    "Get Out",
    "A Quiet Place",
    "It",
    "The Ring",
    "The Shining",
    "Midsommar",
    "The Babadook",
    "Train to Busan"
  ];

  const requests = recommendedTitles.map((title) =>
    fetch(`https://www.omdbapi.com/?apikey=${OMDB_API_KEY}&type=movie&t=${encodeURIComponent(title)}`)
      .then((res) => res.json())
      .catch(() => null)
  );

  Promise.all(requests)
    .then((results) => {
      //Igual que en index: siempre mostramos una tarjeta por cada título recomendado.
      todasLasPeliculas = recommendedTitles.map((title, index) => {
        const film = results[index];

        if (film && film.Response === "True") {
          let fdate = film.Year + "-01-01";
          if (film.Released && film.Released !== "N/A") {
              fdate = new Date(film.Released).toISOString().split('T')[0];
          }

          const movie = {
            id: film.imdbID,
            title: film.Title,
            release_date: film.Year,
            director: film.Director || "",
            image: film.Poster !== "N/A" ? film.Poster : "",
            movie_banner: "",
            genre: film.Genre || "Desconocido",
            duration: parseInt((film.Runtime || "").replace(" min", "")) || 0,
            description: film.Plot || "Sin descripción",
            rating: parseFloat(film.imdbRating) || 0,
            formattedDate: fdate
          };

          const formData = new FormData();
          formData.append('titulo', movie.title);
          formData.append('genero', movie.genre);
          formData.append('duracion', movie.duration);
          formData.append('descripcion', movie.description);
          formData.append('año', movie.formattedDate);
          formData.append('director', movie.director);
          formData.append('valoracion', movie.rating);
          formData.append('imagen', movie.image);

          fetch('../PHP/Peliculas/apiGuardarPelicula.php', { method: 'POST', body: formData }).catch(e => console.error(e));
          return movie;
        }

        //Fallback simple si la API no devuelve nada
        return {
          id: title,
          title: title,
          release_date: "",
          director: "",
          image: "",
          movie_banner: ""
        };
      });

      renderRecomendados();
    })
    .catch((err) => {
      console.error("Error al cargar la API de películas", err);
      grid.innerHTML = "<p>No se han podido cargar las películas ahora mismo.</p>";
    });

  if (select) {
    select.addEventListener("change", renderRecomendados);
  }
});

