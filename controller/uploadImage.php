<?php


function uploadImage($type)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $uploadDir = 'uploads/';
            $uploadedFile = $_FILES['image'];

            if ($uploadedFile['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
                $type = explode('/', $type);
                $typeFile = array_shift($type);
                $timestamp = time();
                $filename = $typeFile . '-' . $timestamp . '.' . $ext;
                $targetPath = $uploadDir . $filename;

                if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
                    http_response_code(200);
                    echo json_encode(['success' => true, 'filename' => $filename]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Error uploading file']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            error_log('An error occurred: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    }
}