<?php


session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/model/UserModel.php';
require __DIR__ . '/controller/UserController.php';

require __DIR__ . '/model/templateModel.php';
require __DIR__ . '/controller/templateController.php';

include('database.php');

$userModel = new UserModel($db);
$userController = new UserController($userModel);

$templateModel = new TemplateModel($db);
$templateController = new TemplateController($templateModel);

$request = $_SERVER['REQUEST_URI'];

$viewDir = '/view/';

$request = strtok($request, '?');
$request = rtrim($request, '/');

switch ($request) {
    case '/DTT':
        // var_dump($_REQUEST);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $userController->loginUser();
        } else {
            require __DIR__ . $viewDir . 'login.php';
        }
        break;

    case '/DTT/register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userController->registerUser();
        } else {
            require __DIR__ . $viewDir . 'register.php';
        }
        break;

    case '/DTT/home':

        require __DIR__ . $viewDir . 'home.php';

        break;

    case '/DTT/dash':

        require __DIR__ . $viewDir . 'dash.php';

        break;

    case '/DTT/save_template':
        $templateController->create();
        break;
    default:
        http_response_code(404);
        require __DIR__ . $viewDir . '404.php';
}