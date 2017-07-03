<?php
namespace Vacations;
use Controllers\VacationsController;
use Controllers\UsersController;
use Controllers\PagesController;

class Router{

	public function call($controller, $action){
        require_once('controllers/' . $controller . '_controller.php');

        switch($controller) {
            case 'vacations':
                $controller = new VacationsController();
                break;
            case 'users':
                $controller = new UsersController();
                break;
            case 'pages':
                $controller = new PagesController();
                break;
      }

        return $controller->{ $action }();
    }
}

?>
