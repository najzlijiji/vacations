<?php

	function is_date($date) {
		try {
			$date = new DateTime($date);
			return true;
		}
		catch (Exception $e) {
			return false;
		}
	}
	
?>