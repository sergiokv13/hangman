<?php
    include_once("utilities.php");
	require_once("game.php");
	
    function fetchRandomWordFromDB()
    {
		$categoryId = 1;
	    $sql = "SELECT text FROM word WHERE categoryId=? ORDER BY RAND() LIMIT 1";
		$conn = createConnection();

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

	session_start();
	
	$data = json_decode(file_get_contents('php://input'), true);
	
	if (!isset($data["letter"]) || !isset($_SESSION["game"]))
	{
		$game = new Game(fetchRandomWordFromDB());
		$_SESSION["game"] = $game;
	}
	else
	{
		$letter = $data["letter"];
		
        $game=$_SESSION["game"]; 
		$game->play($letter);
	}
	

	
	$result = new StdClass();
    $result->health=$game->health;
    $result->revealed = $game->revealed;
    $result->hasWon = $game->revealed === $game->word;

	header('Content-Type: application/json');
	echo json_encode($result);	
?>


