<?php
class TypeController
{
    private $typeModel;

    public function __construct($typeModel)
    {
        $this->typeModel = $typeModel;
    }

    public function addType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $typeName = $_POST['type_name'];

            $success = $this->typeModel->addType($typeName);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Type added successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add type.']);
            }
        }
    }

    public function getAllTypes()
    {
        try {
            $types = $this->typeModel->getAllTypes();
            echo json_encode(['success' => true, 'types' => $types]);
        } catch (Exception $e) {
            error_log('An error occurred: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function getTemplatesByType($typeId)
    {
        try {
            $templates = $this->typeModel->getTemplatesByType($typeId);
            echo json_encode(['success' => true, 'templates' => $templates]);
        } catch (Exception $e) {
            error_log('An error occurred: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function getDocumentsByType($typeId)
    {
        try {
            $documents = $this->typeModel->getDocumentsByType($typeId);
            echo json_encode(['success' => true, 'documents' => $documents]);
        } catch (Exception $e) {
            error_log('An error occurred: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function delete()
    {
        $typeId = $_GET['id'];

        if (!is_numeric($typeId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid type ID provided.']);
            return;
        }

        $success = $this->typeModel->deleteTypeAndRelatedData($typeId);
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete type and related data.']);
        }
    }

    public function updateType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $typeId = $_POST['type_id'];
            $typeName = $_POST['type_name'];

            $success = $this->typeModel->updateType($typeId, $typeName);

            if ($success) {
                $response = ['success' => true];
            } else {
                $response = ['success' => false, 'message' => 'Failed to update type.'];
            }

            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }
}