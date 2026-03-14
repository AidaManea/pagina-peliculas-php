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

const SEEN_KEY = "filmoraSeen";

function getSeen() {
  try {
    const raw = localStorage.getItem(SEEN_KEY);
    return raw ? JSON.parse(raw) : [];
  } catch {
    return [];
  }
}

function saveSeen(seen) {
  localStorage.setItem(SEEN_KEY, JSON.stringify(seen));
}

function isSeen(id) {
  return getSeen().some((f) => f.id === id);
}

function toggleSeen(movie) {
  const seen = getSeen();
  const existingIndex = seen.findIndex((f) => f.id === movie.id);
  let nowSeen = false;

  if (existingIndex >= 0) {
    seen.splice(existingIndex, 1);
    nowSeen = false;
  } else {
    seen.push(movie);
    nowSeen = true;
  }
  saveSeen(seen);

  // Sincronizar con la BD (tabla vistas)
  const formData = new FormData();
  formData.append('movieId', movie.id);
  formData.append('title', movie.title);
  formData.append('year', movie.release_date || '');
  formData.append('director', movie.director || '');

  fetch('../PHP/Peliculas/marcarVista.php', {
    method: 'POST',
    body: formData,
    credentials: 'include'
  }).catch(() => {
    // Si falla la llamada, mantenemos localStorage pero podrías mostrar un aviso
    console.warn('No se pudo actualizar la película vista en la base de datos.');
  });
}

function isFavorite(id) {
  return getFavorites().some((f) => f.id === id);
}

function toggleFavorite(movie) {
  const favs = getFavorites();
  const existingIndex = favs.findIndex((f) => f.id === movie.id);
  let nowFav = false;

  if (existingIndex >= 0) {
    favs.splice(existingIndex, 1);
    nowFav = false;
  } else {
    favs.push(movie);
    nowFav = true;
  }
  saveFavorites(favs);

  // Sincronizar con la BD (tabla favoritas)
  const formData = new FormData();
  formData.append('movieId', movie.id);
  formData.append('title', movie.title);
  formData.append('year', movie.release_date || '');
  formData.append('director', movie.director || '');

  fetch('../PHP/Peliculas/marcarFavorita.php', {
    method: 'POST',
    body: formData,
    credentials: 'include'
  }).catch(() => {
    console.warn('No se pudo actualizar favoritos en la base de datos.');
  });
}

function createMovieCard(movie, { showFavoriteButton = true, showSeenButton = true, showEditButton = false } = {}) {
  const card = document.createElement("article");
  card.className = "pelicula-card";

  const posterUrl = movie.image || movie.movie_banner || "";
  const favorite = isFavorite(movie.id);
  const seen = isSeen(movie.id);

  let extraButtons = "";
  if (showFavoriteButton) {
    extraButtons += `
      <button type="button" class="btn-fav ${favorite ? "active" : ""}" aria-pressed="${favorite}">
        <span class="icon">★</span>
      </button>
    `;
  }
  
  if (showSeenButton) {
    extraButtons += `
      <button type="button" class="btn-seen ${seen ? "active" : ""}" aria-pressed="${seen}">
        <span class="icon">V</span>
      </button>
    `;
  }

  card.innerHTML = `
    <div class="poster" style="${posterUrl ? `background-image:url('${posterUrl}');background-size:cover;background-position:center;` : ""}">
      ${!posterUrl ? "🎬" : ""}
      ${extraButtons}
    </div>
    <div class="pelicula-body">
      <h3>${movie.title}</h3>
      <p>${movie.release_date || ""} ${movie.director ? "· " + movie.director : ""}</p>
    </div>
  `;

  if (showEditButton) {
     const editContainer = document.createElement("div");
     editContainer.className = "edit-controls";
     editContainer.style.display = "none";
     editContainer.innerHTML = `<button class="btn-delete">Quitar de mi lista</button>`;
     card.appendChild(editContainer);

     const btnDelete = editContainer.querySelector(".btn-delete");
     btnDelete.addEventListener("click", () => {
         toggleFavorite(movie);
         card.remove(); // Elimina elemento del DOM
         if (document.querySelectorAll('#listaFavoritas .pelicula-card').length === 0) {
             const vacia = document.getElementById("listaVacia");
             if (vacia) vacia.style.display = "block";
         }
     });
  }

  if (showFavoriteButton) {
    const btn = card.querySelector(".btn-fav");
    btn.addEventListener("click", () => {
      toggleFavorite(movie);
      btn.classList.toggle("active");
      btn.setAttribute("aria-pressed", btn.classList.contains("active"));
    });
  }

  if (showSeenButton) {
    const btnSeen = card.querySelector(".btn-seen");
    btnSeen.addEventListener("click", () => {
      toggleSeen(movie);
      btnSeen.classList.toggle("active");
      btnSeen.style.color = btnSeen.classList.contains("active") ? "#4caf50" : "#fff";
      btnSeen.setAttribute("aria-pressed", btnSeen.classList.contains("active"));
    });
  }

  return card;
}

