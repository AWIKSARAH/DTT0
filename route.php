<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/model/userModel.php';
require __DIR__ . '/controller/userController.php';

require_once './controller/uploadImage.php';

require __DIR__ . '/model/documentModel.php';
require __DIR__ . '/controller/documentController.php';

require __DIR__ . '/model/typeModel.php';
require __DIR__ . '/controller/typeController.php';

require __DIR__ . '/model/templateModel.php';
require __DIR__ . '/controller/templateController.php';

include('database.php');
$userModel = new UserModel($db);
$userController = new UserController($userModel);

$templateModel = new TemplateModel($db);
$templateController = new TemplateController($templateModel);

$typeModel = new TypeModel($db);
$typeController = new TypeController($typeModel);

$documentModel = new DocumentModel($db);
$documentController = new DocumentController($documentModel);

$request = $_SERVER['REQUEST_URI'];

$viewDir = '/view/';

$request = strtok($request, '?');
$request = rtrim($request, '/');

switch ($request) {
    case '/DTT':
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
    case '/DTT/get_template_names':
        $templateController->getTemplateNames();
        break;
    case '/DTT/get_template':
        if (isset($_GET['name'])) {
            $templateName = $_GET['name'];
            $templateController->getTemplate($templateName);
        } else {
            echo json_encode(['success' => false, 'message' => 'Template name not provided']);
        }
        break;

    case '/DTT/get_templateById':
        if (isset($_GET['id'])) {
            $templateid = $_GET['id'];
            $templateController->getTemplateId($templateid);
        } else {
            echo json_encode(['success' => false, 'message' => 'Template name not provided']);
        }
        break;

    case '/DTT/create_document':
        $documentController->create();
        break;

    case '/DTT/upload':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['type'])) {
            $type = $_GET['type'];
            uploadImage($type);
        }
        break;

    case '/DTT/get_documents':
        $documentController->readAll();
        break;
    case '/DTT/get_templates':
        $templateController->readAll();
        break;

    case '/DTT/delete_document':
        $documentController->deleteDocument();
        break;
    case '/DTT/delete_template':
        $templateController->delete();
        break;

    case '/DTT/delete_type':
        $typeController->delete();
        break;
    case '/DTT/get_document':
        $documentController->readById();
        break;


    case '/DTT/add_type':
        $typeController->addType();
        break;





    case '/DTT/update_type':
        $typeController->updateType();
        break;

    case '/DTT/get_types':
        $typeController->getAllTypes();
        break;
    case '/DTT/update_document':
        $documentController->updateDocument();
        break;
    case '/DTT/get_templates_by_type':
        if (isset($_GET['type_id'])) {
            $templateController->getTemplatesByType($_GET["type_id"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Template name not provided']);
        }
        break;

    default:
        http_response_code(404);
        require __DIR__ . $viewDir . '404.php';
}