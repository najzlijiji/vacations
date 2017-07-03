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
	// ideally all this would be routed to API
	if(isset($_GET['action'])){
		if($_GET['action']=='requestVacation' || $_GET['action']=='approveVacation' || $_GET['action']=='rejectVacation' || $_GET['action']=='cancelVacation'){
			$obj = new Vacation;
			switch ($_GET['action']){
				case 'requestVacation':
					$userId = filterInt('userId',"POST");
					$startDate = filterString('startDate',"POST");
					$endDate = filterString('endDate',"POST");
					$response = $obj->requestVacation($userId,$startDate,$endDate);
					break;

				case 'approveVacation':
					$vacationId = filterInt('vacationId',"POST");
					$userId = filterInt('userId',"POST");
					$response = $obj->approveVacation($vacationId,$userId);
					break;

				case 'rejectVacation':
					$vacationId = filterInt('vacationId',"POST");
					$response = $obj->rejectVacation($vacationId);
					break;

				case 'cancelVacation':
					$vacationId = filterInt('vacationId',"POST");
					$userId = filterInt('userId',"POST");
					$response = $obj->cancelVacation($vacationId,$userId);
					break;
			}			
		}
		if($_GET['action']=='addUser'){
			$obj = new User;
			$name = filterString('name',"POST");
			$days = filterInt('days',"POST");
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
    $controller = filterString('controller',"GET");
    $action = filterString('action', "GET");
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