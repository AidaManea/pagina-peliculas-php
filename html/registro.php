<?php

require('../PHP/db.php');

try {

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$username = $_POST['user'];
$password = hash('sha256', $_POST['pass']);

$stmt = $conn->prepare("INSERT INTO users (username, password)
VALUES (:username, :password)");

$stmt->bindParam(':username', $username);
$stmt->bindParam(':password', $password);

$stmt->execute();

header("Location: ../html/login.html");
exit();

} catch(PDOException $e) {

echo "Error: " . $e->getMessage();

}

$conn = null;

?>