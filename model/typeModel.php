<?php
class TypeModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllTypes()
    {
        $query = "SELECT * FROM type_info";
        $statement = $this->db->prepare($query);
        $statement->execute();

        $types = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $types;
    }
    public function getTemplatesByType($typeId)
    {
        $query = "SELECT * FROM template WHERE type_id = :typeId";
        $statement = $this->db->prepare($query);
        $statement->bindValue(':typeId', $typeId, PDO::PARAM_INT);
        $statement->execute();

        $templates = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $templates;
    }

    public function getDocumentsByType($typeId)
    {
        $query = "SELECT * FROM document WHERE type_id = :typeId";
        $statement = $this->db->prepare($query);
        $statement->bindValue(':typeId', $typeId, PDO::PARAM_INT);
        $statement->execute();

        $documents = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $documents;
    }

    public function addType($typeName)
    {
        $query = "INSERT INTO type_info (type_name) VALUES (:type_name)";
        $statement = $this->db->prepare($query);
        $statement->bindParam(':type_name', $typeName, PDO::PARAM_STR);

        try {
            $success = $statement->execute();
            return $success;
        } catch (PDOException $e) {
            return false;
        }
    }
    public function deleteTypeAndRelatedData($typeId)
    {
        $this->db->beginTransaction();

        try {
            $documentQuery = "DELETE FROM document WHERE template_id IN (
                SELECT template_id FROM template WHERE type_id = :type_id
            )";
            $documentStmt = $this->db->prepare($documentQuery);
            $documentStmt->bindParam(':type_id', $typeId);
            $documentStmt->execute();

            $templateQuery = "DELETE FROM template WHERE type_id = :type_id";
            $templateStmt = $this->db->prepare($templateQuery);
            $templateStmt->bindParam(':type_id', $typeId);
            $templateStmt->execute();

            $typeQuery = "DELETE FROM type_info WHERE type_id = :type_id";
            $typeStmt = $this->db->prepare($typeQuery);
            $typeStmt->bindParam(':type_id', $typeId);
            $typeStmt->execute();

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollback();
            return false;
        }
    }



    public function updateType($typeId, $typeName)
    {
        $query = "UPDATE type_info SET type_name = :type_name WHERE type_id = :type_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':type_id', $typeId);
        $stmt->bindParam(':type_name', $typeName);
        return $stmt->execute();
    }

}