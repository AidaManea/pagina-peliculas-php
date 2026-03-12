<?php
require('Conexiondb.php');

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

  $sql = "SELECT * FROM pelis";
  $stmt = $conn->query($sql);
  $peliculas = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Conexión exitosa";
} catch(PDOException $e) {
  echo "Error de conexión: " . $e->getMessage();
}

?>
<h2>Lista de Películas</h2>

<a href="../../html/AñadirPelicula.html">Añadir película</a>

<table borde="1">

<tr>
<th>Titulo</th>
<th>Genero</th>
<th>Duracion</th>
<th>Año</th>
<th>Director</th>
<th>Acciones</th>
</tr>

<?php foreach($peliculas as $p): ?>

<tr>

<td><?= $p['titulo'] ?></td>
<td><?= $p['genero'] ?></td>
<td><?= $p['duracion'] ?></td>
<td><?= $p['anio'] ?></td>
<td><?= $p['director'] ?></td>

<td>

<a href="editarPelicula.php?id=<?= $p['id'] ?>">Editar</a>

<a href="eliminarPelicula.php?id=<?= $p['id'] ?>" 
onclick="return confirm('¿Seguro que quieres borrar esta película?')">
Eliminar
</a>

</td>

</tr>

<?php endforeach; ?>

</table>