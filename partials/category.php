<!DOCTYPE html>
<?php
function redirectToCategories()
{
	header("Location: categories.php");
	exit();
}

if (!isset($_GET["id"]))
	redirectToCategories();

include_once("utilities.php");

function createWord($word, $categoryId, $conn) 
{
	$sql = "INSERT INTO word(categoryId, text) VALUES (?, ?)";

	$stmt = mysqli_stmt_init($conn);
	$success = false;
	
    if (mysqli_stmt_prepare($stmt, $sql)) 
	{
		/* bind parameters for markers, other options are: 
		   i - integer, d - double, s - string, b - BLOB */
		mysqli_stmt_bind_param($stmt, "is", $categoryId, $word);

		$success = mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);		
	}
	
	return $success;
}

function getCategoryName($categoryId, $conn)
{
	$sql = "SELECT name FROM category WHERE id=?";
	
    $stmt = mysqli_stmt_init($conn);
	
    if (mysqli_stmt_prepare($stmt, $sql)) 
	{
		mysqli_stmt_bind_param($stmt, "i", $categoryId);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_bind_result($stmt, $name);
		mysqli_stmt_fetch($stmt);
		mysqli_stmt_close($stmt);
		
		return $name;
	}
	else
		return null;
}

function getWords($categoryId, $conn)
{
	$sql = "SELECT id, text FROM word WHERE categoryId=?";

	$stmt = mysqli_stmt_init($conn);
	
	if (mysqli_stmt_prepare($stmt, $sql)) 
	{
		/* bind parameters for markers, other options are: 
		   i - integer, d - double, s - string, b - BLOB */
		mysqli_stmt_bind_param($stmt, "i", $categoryId);

		mysqli_stmt_execute($stmt);

		return mysqli_stmt_get_result($stmt);
	}
	
	return null;
}

$conn = createConnection();
$categoryId = $_GET["id"];

if (isset($_POST["createNewWordButton"]))
{
	if ( createWord( $_POST["newWordTextBox"], $categoryId, $conn ) )
	{
		header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]) . '?' . $_SERVER["QUERY_STRING"] );
		exit();
	}
	else 
	{
		die("Ocurrió un error al intentar crear la palabra.");
	}
}

$categoryName = getCategoryName($categoryId, $conn);
if ($categoryName === null)
	redirectToCategories();

$result = getWords($categoryId, $conn);
?>
<html lang="es-BO">
  <head>
    <title>El ahorcado - Categoría: <?= $categoryName ?></title>
	<meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/admin.css">
  </head>
  <body>
    <h1>Palabras para Categoría "<?= $categoryName ?>"</h1>
	<ul>
	<?php 
	    if (mysqli_num_rows($result) > 0) {
      
			while($row = mysqli_fetch_array($result))
				{ 
    ?>
			<li><?= $row["text"] ?> [<a href="deleteWord.php?id=<?= $row["id"] ?>" onclick="return confirm('¿Está seguro que desea eliminar esta palabra?');">X</a>]</li>
    <?php }
        }
		
		mysqli_close($conn);
	 ?>
	  <li>
		<form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>?<?= $_SERVER["QUERY_STRING"] ?>">
		  <input type="text" name="newWordTextBox" maxlength="50" />
		  <input type="submit" name="createNewWordButton" value="Crear" />
		</form>
	  </li>
	</ul>
	
	<div>
	   <a href="deleteCategory.php?categoryId=<?= $categoryId ?>" onclick="return confirm('¿Está seguro que desea eliminar esta categoría?');">Eliminar esta categoría</a>
	</div>
  </body>
</html>