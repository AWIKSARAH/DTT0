<?php
class TemplateModel
{

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createTemplate($templateName, $templateStructure)
    {
        $query = "INSERT INTO template (template_name, template_structure) VALUES(:template_name, :template_structure)";
        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':template_name', $templateName);
        $stmt->bindParam(':template_structure', $templateStructure);

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
        $query = "DELETE FROM template WHERE template_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $templateId);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function updateTemplate($templateId, $templateName, $templateStructure)
    {
        $query = "UPDATE template SET template_name = ?, template_structure = ? WHERE template_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssi", $templateName, $templateStructure, $templateId);

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

        // Return the array of template information
        return $templates;
    }

    public function getAllTemplates()
    {
        $query = "SELECT * FROM template";
        $statement = $this->db->prepare($query);
        $statement->execute();

        $templates = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $templates;
    }

    public function getTemplateDataByName($templateName)
    {
        try {
            $query = "SELECT * FROM template WHERE template_name = :template_name";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':template_name', $templateName);
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

    public function getTemplateDataById($templateId)
    {
        try {
            $query = "SELECT * FROM template WHERE template_id = :template_id";
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
}