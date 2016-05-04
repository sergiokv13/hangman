<?php
class Game 
{
	public $word;
	public $health;
	public $revealed;
	
	function __construct($word)
	{
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
	
	public function hasFinished()
	{
		return ($this->health === 0) or $this->hasBeenRevealed();
	}
	
	public function hasBeenRevealed()
	{
		return ($this->revealed === $this->word);
	}
}
?>

