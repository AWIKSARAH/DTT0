<?php
class DocumentModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createDocument($templateId, $userId, $dataContent)
    {
        $query = "INSERT INTO document (template_id, user_id, data_content) VALUES (:template_id, :user_id, :data_content)";
        $stmt = $this->db->prepare($query);
        $dataContentJson = json_encode($dataContent);

        $stmt->bindParam(':template_id', $templateId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':data_content', $dataContentJson);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getDocumentsByUserId($userId)
    {
        $query = "SELECT * FROM document WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateDocument($documentId, $dataContent)
    {
        $query = "UPDATE document SET data_content = :data_content WHERE document_id = :document_id";
        $stmt = $this->db->prepare($query);
        $dataContentJson = json_encode($dataContent);


        $stmt->bindParam(':data_content', $dataContentJson);
        $stmt->bindParam(':document_id', $documentId);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getDocumentsById($id)
    {
        $query = "SELECT d.*, t.template_name,t.template_structure FROM document d
                  LEFT JOIN template t ON d.template_id = t.template_id
                  WHERE d.document_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllDocuments()
    {
        $query = "SELECT * FROM document";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteDocument($documentId)
    {
        $query = "DELETE FROM document WHERE document_id = :document_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':document_id', $documentId);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}