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

    public function templateExists($templateName)
    {
        $query = "SELECT COUNT(*) as count FROM templates WHERE template_name = :templateName";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':templateName', $templateName);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}