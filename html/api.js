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
      const valid = results.filter(Boolean);

      if (!valid.length) {
        grid.innerHTML = "<p>No se han podido cargar las películas ahora mismo.</p>";
        return;
      }

      valid.forEach((film) => {
        const movie = {
          id: film.imdbID,
          title: film.Title,
          release_date: film.Year,
          director: film.Director || "",
          image: film.Poster !== "N/A" ? film.Poster : "",
          movie_banner: "",
        };
        const card = createMovieCard(movie, { showFavoriteButton: true });
        grid.appendChild(card);
      });
    })
    .catch((err) => {
      console.error("Error al cargar la API de películas", err);
      grid.innerHTML = "<p>No se han podido cargar las películas ahora mismo.</p>";
    });
});
