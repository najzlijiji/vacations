<?php
	//checks if string can be converted to date
	function isDate(string $date) {
		try {
			$date = new \DateTime($date);
			return true;
		}
		catch (Exception $e) {
			return false;
		}
	}

	function filterInt(string $value, string $method): int{
		if($method=="POST"){
			return intval(filter_input(INPUT_POST, $value, FILTER_SANITIZE_NUMBER_INT));
		}
		else if($method=="GET"){
			return intval(filter_input(INPUT_GET, $value, FILTER_SANITIZE_NUMBER_INT));
		}
		
	}

	function filterString(string $value, string $method): string{
		if($method=="POST"){
			return filter_input(INPUT_POST, $value, FILTER_SANITIZE_STRING);
		}
		else if($method=="GET"){
			return filter_input(INPUT_GET, $value, FILTER_SANITIZE_STRING);
		}
	}

	
?>