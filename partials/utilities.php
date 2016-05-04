<?php
function createConnection() 
{
	$servername = "localhost";
	$username = "root";
	$password = "";
	$database = "hangman"; 

	$conn = mysqli_connect($servername, $username, $password, $database);

	if (!$conn)
	{
		die("Fallo en la conexión con la Base de Datos. " 
		   . mysqli_connect_error());
	}
	
	return $conn;
}
?>