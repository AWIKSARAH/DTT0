<?php
class TemplateModel
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createTemplate($templateName, $templateStructure, $typeId)
    {
        $query = "INSERT INTO template (template_name, template_structure, type_id) VALUES(:template_name, :template_structure, :type_id)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':template_name', $templateName);
        $stmt->bindParam(':template_structure', $templateStructure);
        $stmt->bindParam(':type_id', $typeId);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }


    public function getTemplateById($templateId)
    {
        $query = "SELECT template_id, template_name, template_structure, created_by FROM template WHERE template_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $templateId);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $template = $result->fetch_assoc();
            return $template;
        }

        return null;
    }
    public function deleteTemplate($templateId)
    {
        $this->db->beginTransaction();

        try {
            $documentQuery = "DELETE FROM document WHERE template_id = :template_id";
            $documentStmt = $this->db->prepare($documentQuery);
            $documentStmt->bindParam(':template_id', $templateId);
            $documentStmt->execute();

            $templateQuery = "DELETE FROM template WHERE template_id = :template_id";
            $templateStmt = $this->db->prepare($templateQuery);
            $templateStmt->bindParam(':template_id', $templateId);
            $templateStmt->execute();


            $typeQuery = "DELETE FROM type_info WHERE ";
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollback();
            return false;
        }
    }


    public function updateTemplate($templateId, $templateName, $templateStructure, $typeId)
    {
        $query = "UPDATE template SET template_name = :template_name, template_structure = :template_structure, type_id = :type_id WHERE template_id = :template_id";
        $stmt = $this->db->prepare($query);

        $stmt->bindValue(':template_name', $templateName);
        $stmt->bindValue(':template_structure', $templateStructure);
        $stmt->bindValue(':type_id', $typeId);
        $stmt->bindValue(':template_id', $templateId);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }



    public function getTemplateNames()
    {
        $query = "SELECT template_id, template_name FROM template";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $templates = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $templateId = $row['template_id'];
            $templateName = $row['template_name'];
            $templates[] = ['template_id' => $templateId, 'template_name' => $templateName];
        }

        return $templates;
    }


    public function getAllTemplates()
    {
        $query = "SELECT template.*, type.type_name FROM template LEFT JOIN type ON template.type_id = type.type_id";
        $statement = $this->db->prepare($query);
        $statement->execute();

        $templates = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $templates;
    }
    public function getTemplateDataById($templateId)
    {
        try {
            $query = "SELECT template.*, type_info.type_name FROM template LEFT JOIN type_info ON template.type_id = type_info.type_id WHERE template.template_id = :template_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':template_id', $templateId);
            $stmt->execute();
            $templateData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($templateData) {
                $templateData['fields'] = json_decode($templateData['template_structure'], true);
                return $templateData;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }
    public function getTemplatesByType($typeId)
    {
        $query = "SELECT * FROM template WHERE type_id = :typeId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":typeId", $typeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}