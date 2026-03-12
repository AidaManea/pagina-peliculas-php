// Gestión de favoritos en localStorage y render de tarjetas

const FAVORITES_KEY = "filmoraFavorites";

function getFavorites() {
  try {
    const raw = localStorage.getItem(FAVORITES_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

function saveFavorites(favs) {
  localStorage.setItem(FAVORITES_KEY, JSON.stringify(favs));
}

function isFavorite(id) {
  return getFavorites().some((f) => f.id === id);
}

function toggleFavorite(movie) {
  const favs = getFavorites();
  const existingIndex = favs.findIndex((f) => f.id === movie.id);
  if (existingIndex >= 0) {
    favs.splice(existingIndex, 1);
  } else {
    favs.push(movie);
  }
  saveFavorites(favs);
}

function createMovieCard(movie, { showFavoriteButton = true } = {}) {
  const card = document.createElement("article");
  card.className = "pelicula-card";

  const posterUrl = movie.image || movie.movie_banner || "";
  const favorite = isFavorite(movie.id);

  card.innerHTML = `
    <div class="poster" style="${posterUrl ? `background-image:url('${posterUrl}');background-size:cover;background-position:center;` : ""}">
      ${!posterUrl ? "🎬" : ""}
      ${
        showFavoriteButton
          ? `<button type="button" class="btn-fav ${favorite ? "active" : ""}" aria-pressed="${favorite}">
               <span class="icon">❤</span>
             </button>`
          : ""
      }
    </div>
    <div class="pelicula-body">
      <h3>${movie.title}</h3>
      <p>${movie.release_date || ""} ${movie.director ? "· " + movie.director : ""}</p>
    </div>
  `;

  if (showFavoriteButton) {
    const btn = card.querySelector(".btn-fav");
    btn.addEventListener("click", () => {
      toggleFavorite(movie);
      btn.classList.toggle("active");
      btn.setAttribute("aria-pressed", btn.classList.contains("active"));
    });
  }

  return card;
}

