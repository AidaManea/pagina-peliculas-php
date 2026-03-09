<?php
/* CONSUMIR LA API*/

$apiUrl = "https://devsapihub.com/api-movies";

$response = file_get_contents($apiUrl);

$movies = json_decode($response, true);


/*FUNCIÓN PARA MOSTRAR ESTRELLAS */

function generarEstrellas($rating){

    $totalStars = 5;
    $html = "";

    for($i = 0; $i < $totalStars; $i++){

        if($i < floor($rating)){
            $html .= '<span class="star filled">★</span>';
        }else{
            $html .= '<span class="star empty">☆</span>';
        }

    }

    return $html;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Películas Destacadas</title>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">


<style>

/* CONTENEDOR GENERAL */

body{
background:#f5f5f5;
}


/* TARJETA DE PELÍCULA*/

.movie-card{
position:relative;
border-radius:20px;
overflow:hidden;
box-shadow:0 10px 20px rgba(0,0,0,0.3);
transition:transform 0.3s ease;
background:#000;
}

.movie-card:hover{
transform:scale(1.03);
}


/* POSTER*/

.movie-poster{
width:100%;
height:320px;
object-fit:cover;
filter:brightness(0.9);
}


/* OVERLAY */

.movie-overlay{
position:absolute;
bottom:0;
left:0;
right:0;
padding:1rem;
background:linear-gradient(to top, rgba(0,0,0,0.85), transparent);
color:#fff;
}

.movie-title{
font-size:1.1rem;
font-weight:600;
margin:0;
}

.movie-info{
font-size:0.9rem;
color:#ccc;
}


/* ESTRELLAS Y RATING*/

.movie-rating{
position:absolute;
top:10px;
left:10px;
background:white;
color:#333;
font-weight:bold;
font-size:0.85rem;
padding:4px 8px;
border-radius:12px;
}

.star{
font-size:12px;
}

.star.filled{
color:#ffd700;
}

.star.empty{
color:#ccc;
}

</style>

</head>

<body>


<!--CONTENEDOR PRINCIPAL -->

<div class="container py-4">

<h2 class="text-center mb-4 fw-bold">🎬 Películas Destacadas</h2>


<div class="row">

<?php foreach($movies as $index => $movie): ?>

<?php
/* GENERAR URL DE LA IMAGEN */

$imageUrl = "https://devsapihub.com/img-movies/" . ($index + 1) . ".jpg";
?>

<div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">

<div class="movie-card">


<!-- POSTER -->

<img 
src="<?= $imageUrl ?>" 
alt="<?= $movie['title'] ?>" 
class="movie-poster"
>


<!-- RATING -->

<div class="movie-rating">

<span class="rating-year"><?= $movie['year'] ?></span>

|

<?= generarEstrellas($movie['stars']) ?>

</div>


<!-- INFORMACIÓN -->

<div class="movie-overlay">

<h4 class="movie-title">

<?= $movie['title'] ?>

</h4>

<p class="movie-info">

<?= $movie['description'] ?>

</p>

</div>


</div>

</div>

<?php endforeach; ?>

</div>

</div>

</body>
</html>