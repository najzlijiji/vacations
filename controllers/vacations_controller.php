<?php

namespace Controllers;
use Models\Vacation;
use Models\User;
use Vacations\VacationStatus;

class VacationsController {

	public function approved(){
		$this->renderPage(VacationStatus::APPROVED);
	}

	public function rejected(){
		$this->renderPage(VacationStatus::REJECTED);
	}

	public function pending(){
		$this->renderPage(VacationStatus::PENDING);
	}

	public function request(){
		$user = new User;
		$names = $user->users();
		require_once("views/vacations/request.php");
	}

	private function renderPage($status){

		$vacations = new Vacation;
		if( isset($_GET['page']) ){
			$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
			$offset=($page-1)*10;
			$vacations->offset=$offset;		
		}
		else{
			$page=1;
		}


		if( isset($_GET['date']) ){
			$filter = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_STRING);
			$f=$filter;
			$filter=explode(',',$filter);
			$vacations->initFilter($filter);
		}
		else{
			$f='';
		}

		$data = $vacations->returnVacations($status);
		$pages=ceil($vacations->pages[$status]/10);
		$pending=$vacations->pages[VacationStatus::PENDING];
		require_once("views/vacations/".VacationStatus::$statusName[$status].".php");
	}
}
?>