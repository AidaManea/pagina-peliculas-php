const OMDB_API_KEY = "b5ae98fe";

document.addEventListener("DOMContentLoaded", () => {
  const grid = document.querySelector(".peliculas-grid");
  if (!grid) return;

  grid.innerHTML = "";

  // Lista de pelis muy conocidas
  const popularTitles = [
    "Inception",
    "The Dark Knight",
    "Interstellar",
    "Pulp Fiction",
    "The Matrix",
    "Fight Club",
    "Forrest Gump",
    "The Lord of the Rings: The Fellowship of the Ring",
    "Avengers: Endgame",
    "Titanic",
    "The Shawshank Redemption",
    "Star Wars",
    "The Godfather",
    "The Godfather: Part II",
    "Gladiator",
    "Jurassic Park",  
    "The Lion King",
    "Pirates of the Caribbean: The Curse of the Black Pearl",
    "The Avengers",
    "Guardians of the Galaxy",
    "Spider-Man: No Way Home"
  ];

  const requests = popularTitles.map((title) =>
    fetch(`https://www.omdbapi.com/?apikey=${OMDB_API_KEY}&type=movie&t=${encodeURIComponent(title)}`)
      .then((res) => res.json())
      .then((data) => (data && data.Response === "True" ? data : null))
      .catch(() => null)
  );

  Promise.all(requests)
    .then((results) => {
      const valid = [];
      for (let i = 0; i < results.length; i++) {
        if (results[i] !== null) {
          valid.push(results[i]);
        }
      }

      if (!valid.length) {
        grid.innerHTML = "<p>No se han podido cargar las películas ahora mismo.</p>";
        return;
      }

      valid.forEach((film) => {
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
          // Extend fields properties for favoritism logic
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

        fetch('../PHP/Peliculas/apiGuardarPelicula.php', { method: 'POST', body: formData }).catch(e => console.error(e));

        const card = createMovieCard(movie, true, true, false);
        grid.appendChild(card);
      });
    })
    .catch((err) => {
      console.error("Error al cargar la API de películas", err);
      grid.innerHTML = "<p>No se han podido cargar las películas ahora mismo.</p>";
    });
});
