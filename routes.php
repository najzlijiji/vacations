<?php

//Autoload dependencies
require_once __DIR__ . '/vendor/autoload.php';
//load pages config
require_once('controllers/controllers_config.php');
//load functions
require_once('functions/functions.php');

use Vacations\Router;
use Models\Vacation;
use Models\User;

if ($_POST){
	if(isset($_GET['action'])){
		if($_GET['action']=='requestVacation' || $_GET['action']=='approveVacation' || $_GET['action']=='rejectVacation' || $_GET['action']=='cancelVacation'){
			$obj = new Vacation;
			switch ($_GET['action']){
				case 'requestVacation':
					$userId = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_NUMBER_INT);
					$startDate = filter_input(INPUT_POST, 'startDate', FILTER_SANITIZE_STRING);
					$endDate = filter_input(INPUT_POST, 'endDate', FILTER_SANITIZE_STRING);
					$response = $obj->requestVacation($userId,$startDate,$endDate);
					break;

				case 'approveVacation':
					$vacationId = filter_input(INPUT_POST, 'vacationId', FILTER_SANITIZE_NUMBER_INT);
					$userId = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_NUMBER_INT);
					$response = $obj->approveVacation($vacationId,$userId);
					break;

				case 'rejectVacation':
					$vacationId = filter_input(INPUT_POST, 'vacationId', FILTER_SANITIZE_NUMBER_INT);
					$response = $obj->rejectVacation($vacationId);
					break;

				case 'cancelVacation':
					$vacationId = filter_input(INPUT_POST, 'vacationId', FILTER_SANITIZE_NUMBER_INT);
					$userId = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_NUMBER_INT);
					$response = $obj->cancelVacation($vacationId,$userId);
					break;

			}			
		}
		if($_GET['action']=='addUser'){
			$obj = new User;
			$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
			$days = filter_input(INPUT_POST, 'days', FILTER_SANITIZE_NUMBER_INT);
			$response = $obj->addUser($name,$days);
		}
		if($response){
				echo 'Success';
			}
			else{
				echo $obj->error;
			}
	}
	die();
} 

if (isset($_GET['controller']) && isset($_GET['action'])) {
    $controller = filter_input(INPUT_GET, 'controller', FILTER_SANITIZE_STRING);
    $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
}
else {
    $controller = 'vacations';
    $action = 'approved';
}

$router = new Router;

// check if requested page is allowed otherwise show error page.
if (array_key_exists($controller, $controllers)) {
    if (in_array($action, $controllers[$controller])) {
        $router->call($controller, $action);
    } 
    else {
       $router->call('pages', 'error');
    }
} 
else {
    $router->call('pages', 'error');
}

?>