<?php

class DocumentController
{
    private $documentModel;

    public function __construct($documentModel)
    {
        $this->documentModel = $documentModel;
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true);

            $templateId = $data['template_id'];
            $userId = $_SESSION["user_id"];
            $dataContent = $data['data_content'];

            try {
                if ($this->documentModel->createDocument($templateId, $userId, $dataContent)) {
                    http_response_code(200);
                    echo json_encode(['success' => true, 'message' => 'Document created successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to create document']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                error_log('An error occurred: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
            }
        }
    }


    public function readByUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $userId = $_GET['user_id'];

            try {
                $documents = $this->documentModel->getDocumentsByUserId($userId);
                http_response_code(200);
                echo json_encode(['success' => true, 'documents' => $documents]);
            } catch (Exception $e) {
                http_response_code(500);
                error_log('An error occurred: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
            }
        }
    }


    public function readById()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $id = $_GET['id'];

            try {
                $documents = $this->documentModel->getDocumentsById($id);
                if (!empty($documents)) {
                    $document = $documents[0];

                    http_response_code(200);
                    echo json_encode(['success' => true, 'document' => $document]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Document not found']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                error_log('An error occurred: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
            }
        }
    }



    public function updateDocument()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true);

            $documentId = $data['document_id'];
            $updatedDataContent = $data['data_content'];
            try {
                if ($this->documentModel->updateDocument($documentId, $updatedDataContent)) {
                    http_response_code(200);
                    echo json_encode(['success' => true, 'message' => 'Document updated successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to update document']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                error_log('An error occurred: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
            }
        }
    }


    public function readAll()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $documents = $this->documentModel->getAllDocuments();
                http_response_code(200);
                echo json_encode(['success' => true, 'documents' => $documents]);
            } catch (Exception $e) {
                http_response_code(500);
                error_log('An error occurred: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
            }
        }
    }


    public function deleteDocument()
    {
        $documentId = $_GET['id'];
        try {
            $success = $this->documentModel->deleteDocument($documentId);
            if ($success) {
                http_response_code(200);
                echo json_encode(['success' => true, 'message' => 'Document deleted successfully.']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to delete document: ' . $e->getMessage()]);
        }
    }
}