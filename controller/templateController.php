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
                    echo json_encode(array('success' => true, 'message' => 'Template created successfully'));
                } else {
                    echo json_encode(array('success' => false, 'message' => 'Failed to create template'));
                }
            } catch (Exception $e) {
                error_log('An error occurred: ' . $e->getMessage()); // Log the error message
                echo json_encode(array('success' => false, 'message' => 'An error occurred: ' . $e->getMessage()));
            }
        }
    }



    public function list()
    {
        $templates = $this->templateModel->getTemplates();
        include 'views/list_templates.php';
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

    public function delete($id)
    {
        if ($this->templateModel->deleteTemplate($id)) {
            header('Location: templates.php');
        } else {
            $error = 'Failed to delete template.';
            $templates = $this->templateModel->getTemplates();
            include 'views/list_templates.php';
        }
    }
}