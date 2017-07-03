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
		if(is_null($names)){
			require_once("views/vacations/no_users.php");
		}
		else{			
			require_once("views/vacations/request.php");
		}
	}

	private function renderPage(int $status){

		$vacations = new Vacation;
		if( isset($_GET['page']) ){
			$page = filterString('page',"GET");
			$offset = ($page-1)*10;
			$vacations->offset = $offset;		
		}
		else{
			$page = 1;
		}


		if( isset($_GET['date']) ){
			$filter = filterString('date', "GET");
			$f = $filter;
			$filter = explode(',',$filter);
			$vacations->initFilter($filter);
		}
		else{
			$f = '';
		}

		$vacations->returnVacations($status);
		$data = $vacations->data;
		$pages=ceil($vacations->pages[$status]/10);
		$pending=$vacations->pages[VacationStatus::PENDING];
		require_once("views/vacations/".VacationStatus::$statusName[$status].".php");
	}
}
?>