const OMDB_API_KEY = "b5ae98fe";
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
    const card = createMovieCard(movie, { showFavoriteButton: true, showSeenButton: true });
    grid.appendChild(card);
  });
}

document.addEventListener("DOMContentLoaded", () => {
  const select = document.getElementById("filtroAnio");
  const grid = document.getElementById("gridRecomendados");
  if (!grid) return;

  // Lista de pelis recomendadas "chulas"
  const recommendedTitles = [
    "La La Land",
    "Coco",
    "Inside Out",
    "Whiplash",
    "Mad Max: Fury Road",
    "Spider-Man: Into the Spider-Verse",
    "Guardians of the Galaxy Vol. 2",
    "The Social Network",
    "Dune",
    "Soul",
    "The Grand Budapest Hotel",
    "Jojo Rabbit",
    "Black Panther",
    "Doctor Strange",
    "Ratatouille",
    "Up",
    "The Incredibles",
    "Zootopia",
    "Toy Story 3",
    "Moana",
    "Shrek",
    "Shrek 2",
    "Kung Fu Panda",
    "How to Train Your Dragon",
    "Big Hero 6",
    "Frozen",
    "WALL·E",
    "Monsters, Inc.",
    "Finding Nemo",
    "The Lego Movie",
    // Terror y suspense
    "The Conjuring",
    "The Conjuring 2",
    "Insidious",
    "Hereditary",
    "Get Out",
    "A Quiet Place",
    "It",
    "The Ring",
    "The Shining"
  ];

  const requests = recommendedTitles.map((title) =>
    fetch(`https://www.omdbapi.com/?apikey=${OMDB_API_KEY}&type=movie&t=${encodeURIComponent(title)}`)
      .then((res) => res.json())
      .then((data) => (data && data.Response === "True" ? data : null))
      .catch(() => null)
  );

  Promise.all(requests)
    .then((results) => {
      const valid = results.filter(Boolean);

      todasLasPeliculas = valid.map((film) => {
        const movie = {
          id: film.imdbID,
          title: film.Title,
          release_date: film.Year,
          director: film.Director || "",
          image: film.Poster !== "N/A" ? film.Poster : "",
          movie_banner: "",
          // Extend fields properties for favoritism logic
          genre: film.Genre || "Desconocido",
          duration: parseInt((film.Runtime || "").replace(" min", "")) || 0,
          description: film.Plot || "Sin descripción",
          rating: parseFloat(film.imdbRating) || 0,
          formattedDate: film.Released && film.Released !== "N/A" ? new Date(film.Released).toISOString().split('T')[0] : (film.Year + "-01-01")
        };

        const formData = new FormData();
        formData.append('titulo', movie.title);
        formData.append('genero', movie.genre);
        formData.append('duracion', movie.duration);
        formData.append('descripcion', movie.description);
        formData.append('año', movie.formattedDate);
        formData.append('director', movie.director);
        formData.append('valoracion', movie.rating);

        fetch('../PHP/Peliculas/apiGuardarPelicula.php', { method: 'POST', body: formData }).catch(e => console.error(e));
        return movie;
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

