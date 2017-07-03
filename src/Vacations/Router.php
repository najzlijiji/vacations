<?php
namespace Vacations;
use Controllers\VacationsController;
use Controllers\UsersController;
use Controllers\PagesController;

class Router{

    /**
     *
     * Call controller 
     * @param string $controller
     * @param string $action
     */
	public function call(string $controller, string $action){
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
        $controller->{ $action }();
    }
}

?>
