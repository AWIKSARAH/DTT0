<?php

class TemplateController
{
    private $templateModel;

    public function __construct($templateModel)
    {
        $this->templateModel = $templateModel;
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $requestData = json_decode(file_get_contents("php://input"), true);
            error_log('Decoded Data: ' . print_r($requestData, true));

            if (isset($requestData['template_name'], $requestData['template_structure'], $requestData['type_id'])) {
                $templateName = $requestData['template_name'];
                $templateStructure = json_encode($requestData['template_structure']);
                $typeId = $requestData['type_id'];

                try {
                    if ($this->templateModel->createTemplate($templateName, $templateStructure, $typeId)) {
                        http_response_code(200);
                        echo json_encode(['message' => 'Template created successfully']);
                    } else {
                        http_response_code(400);
                        echo json_encode(['message' => 'Failed to create template']);
                    }
                } catch (Exception $e) {
                    http_response_code(400);
                    error_log('An error occurred: ' . $e->getMessage());
                    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
                }
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Invalid data format']);
            }
        }
    }



    public function getTemplateNames()
    {
        try {
            $templateNames = $this->templateModel->getTemplateNames();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'template_names' => $templateNames]);
        } catch (Exception $e) {
            error_log('An error occurred: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }



    public function getTemplate($templateName)
    {
        try {
            $templateData = $this->templateModel->getTemplateDataByName($templateName);
            echo json_encode(['success' => true, 'fields' => $templateData]);
        } catch (Exception $e) {
            error_log('An error occurred: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }


    public function getTemplateId($templateName)
    {
        try {
            $templateData = $this->templateModel->getTemplateDataById($templateName);
            echo json_encode(['success' => true, 'fields' => $templateData]);
        } catch (Exception $e) {
            error_log('An error occurred: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function readAll()
    {
        try {
            $templates = $this->templateModel->getAllTemplates();
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'templates' => $templates]);
        } catch (Exception $e) {
            error_log('An error occurred: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newTemplateName = $_POST['template_name'];
            $newTemplateStructure = $_POST['template_structure'];
            $newTypeId = $_POST['type_id']; // Add this line to retrieve type_id from the form

            if ($this->templateModel->updateTemplate($id, $newTemplateName, $newTemplateStructure, $newTypeId)) {
                header('Location: templates.php');
            } else {
                $error = 'Failed to update template.';
                include 'views/update_template.php';
            }
        } else {
            $template = $this->templateModel->getTemplateById($id);
            include 'views/update_template.php';
        }
    }

    public function delete()
    {
        $id = $_GET['id'];


        try {
            $success = $this->templateModel->deleteTemplate($id);
            if ($success) {
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Template deleted successfully.']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete Template: ' . $e->getMessage()]);
        }
    }

    public function getTemplatesByType($typeId)
    {
        $templates = $this->templateModel->getTemplatesByType($typeId);
        if ($templates) {
            $response = array("success" => true, "templates" => $templates);
        } else {
            $response = array("success" => false, "error" => "Failed to fetch templates by type.");
        }
        echo json_encode($response);
    }
}