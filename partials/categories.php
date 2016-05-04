<!DOCTYPE html>
<?php
include "utilities.php";

$conn = createConnection();

if (isset($_POST["createNewCategoryButton"]))
{
	$categoryName = $_POST["newCategoryTextBox"];
	$sql = "INSERT INTO category(name) VALUES (?)";
	
	$stmt = mysqli_stmt_init($conn);	
	if (mysqli_stmt_prepare($stmt, $sql))
	{
		mysqli_stmt_bind_param($stmt, "s", $categoryName);
	
	    mysqli_stmt_execute($stmt);
	
		$lastId = mysqli_insert_id($conn);
		
		echo $_SERVER["HTTP_HOST"];
		
		header("Location: /category.php?id=" . $lastId);
		$success = true;
	}
}

$selectSql = "SELECT id, name FROM category";
$result = mysqli_query($conn, $selectSql);
?>
<html lang="es-BO">
  <head>
    <title>El ahorcado - Categorías</title>
	<meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/admin.css">
  </head>
  <body>
    
    <h1>Categorías</h1>
	<ul>
	<?php
       if (mysqli_num_rows($result) > 0) 
	   {
		   while( $row = mysqli_fetch_assoc($result) )
		   { ?>
			  <li><a href="category.php?id=<?= $row["id"] ?>"><?= $row["name"] ?></a></li>  
	 <?php
           }
	   }
	  ?>
	  <ul>
		<form method="POST" action="categories.php">
		  <input type="text" name="newCategoryTextBox" maxlength="50" />
		  <input type="submit" name="createNewCategoryButton" value="Crear" />
		</form>
	  </li>
	</ul>
  </body>
</html>