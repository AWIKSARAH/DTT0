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
            $templateName = $_POST['template_name'];
            $templateStructure = json_encode($_POST['template_structure']);

            try {

                if ($this->templateModel->createTemplate($templateName, $templateStructure)) {
                    http_response_code(200);
                    echo json_encode(['message' => 'Template created successfully']);

                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Failed to create template']);
                    // echo "lae";
                }
            } catch (Exception $e) {
                http_response_code(400);
                error_log('An error occurred: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
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

            if ($this->templateModel->updateTemplate($id, $newTemplateName, $newTemplateStructure)) {
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
}