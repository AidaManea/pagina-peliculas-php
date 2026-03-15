const OMDB_API_KEY = "b5ae98fe";

document.addEventListener("DOMContentLoaded", () => {
  const grid = document.querySelector(".peliculas-grid");
  if (!grid) return;

  grid.innerHTML = "";

  // Lista de pelis muy conocidas (intentamos muchas para que OMDb devuelva bastantes válidas)
  const popularTitles = [
    "Inception",
    "The Dark Knight",
    "Interstellar",
    "Pulp Fiction",
    "The Matrix",
    "Fight Club",
    "Forrest Gump",
    "The Lord of the Rings: The Fellowship of the Ring",
    "The Lord of the Rings: The Two Towers",
    "The Lord of the Rings: The Return of the King",
    "Avengers: Endgame",
    "Avengers: Infinity War",
    "Titanic",
    "The Shawshank Redemption",
    "Star Wars",
    "Star Wars: Episode V - The Empire Strikes Back",
    "Star Wars: Episode VI - Return of the Jedi",
    "The Godfather",
    "The Godfather: Part II",
    "Gladiator",
    "Jurassic Park",
    "The Lion King",
    "Pirates of the Caribbean: The Curse of the Black Pearl",
    "Pirates of the Caribbean: Dead Man's Chest",
    "The Avengers",
    "Guardians of the Galaxy",
    "Guardians of the Galaxy Vol. 2",
    "Spider-Man: No Way Home",
    "Spider-Man: Homecoming",
    "Spider-Man: Far from Home",
    "Avatar",
    "Avatar: The Way of Water",
    "Joker",
    "The Dark Knight Rises",
    "The Wolf of Wall Street",
    "Deadpool",
    "Deadpool 2",
    "Skyfall",
    "Casino Royale",
    "La La Land",
    "Frozen",
    "Frozen II",
    "Top Gun",
    "Top Gun: Maverick",
    "Harry Potter and the Sorcerer's Stone",
    "Harry Potter and the Chamber of Secrets",
    "Harry Potter and the Prisoner of Azkaban",
    "Harry Potter and the Goblet of Fire",
    "Harry Potter and the Order of the Phoenix",
    "Harry Potter and the Half-Blood Prince",
    "Harry Potter and the Deathly Hallows: Part 1",
    "Harry Potter and the Deathly Hallows: Part 2"
  ];

  const requests = popularTitles.map((title) =>
    fetch(`https://www.omdbapi.com/?apikey=${OMDB_API_KEY}&type=movie&t=${encodeURIComponent(title)}`)
      .then((res) => res.json())
      .catch(() => null)
  );

  Promise.all(requests)
    .then((results) => {
      // Siempre pintamos una tarjeta por cada título de la lista,
      // aunque la API falle para alguno: así nunca desaparecen películas.
      popularTitles.forEach((title, index) => {
        const film = results[index];

        let movie;
        if (film && film.Response === "True") {
          let fdate = film.Year + "-01-01";
          if (film.Released && film.Released !== "N/A") {
              fdate = new Date(film.Released).toISOString().split('T')[0];
          }

          movie = {
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

          fetch('../PHP/Peliculas/apiGuardarPelicula.php', { method: 'POST', body: formData }).catch(e => console.error(e));
        } else {
          // Fallback simple si la API no devuelve nada
          movie = {
            id: title,
            title: title,
            release_date: "",
            director: "",
            image: "",
            movie_banner: "",
          };
        }

        const card = createMovieCard(movie, true, true, false);
        grid.appendChild(card);
      });
    })
    .catch((err) => {
      console.error("Error al cargar la API de películas", err);
      grid.innerHTML = "<p>No se han podido cargar las películas ahora mismo.</p>";
    });
});
