<?php
class Game 
{
	public $word;
	public $health;
	public $revealed;
	private $conn;

	function __construct($word)
	{		
		/*require_once '../api/v1/dbConnect.php';

        $db = new dbConnect();
        $this->conn = $db->connect();*/

		$this->word= $word;
		$this->revealed=str_repeat("_",strlen($word));
		$this->health = 6;
	}
	
	public function play($letter)
	{
		$pos=-1;
		$prevRevealed = $this->revealed;
			
		do 
		{
			$pos=stripos($this->word, $letter, $pos+1);
			if($pos !== false)
			{
				$this->revealed[$pos] = $this->word[$pos];
			}
		} while($pos !== false);
		
		if ($prevRevealed === $this->revealed)
			$this->health--;			
	}
	
	public function hasFinished($uidParams)
	{
		return ($this->health === 0) or $this->hasBeenRevealed();
	}
	
	public function hasBeenRevealed()
	{
		return ($this->revealed === $this->word);
	}
}
?>

