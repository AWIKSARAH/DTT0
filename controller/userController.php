<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class UserController
{
    private $userModel;

    public function __construct($userModel)
    {
        $this->userModel = $userModel;
    }

    public function registerUser()
    {
        $error_message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $isAdmin = isset($_POST['isAdmin']) ? $_POST['isAdmin'] : false;

            if (empty($username) || empty($email) || empty($password)) {
                $error_message = "Please fill all the required fields";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_message = "Invalid Email !";
            } else {
                $userId = $this->userModel->createUser($username, $email, $password, $isAdmin);

                if ($userId != false) {
                    echo "Registration successful. Redirecting to login page...";
                    header("Location: /DTT/");
                    exit;
                } else {
                    $error_message = "Error while registering user ";
                }
            }

            include('view/register.php');
        }

    }


    public function loginUser()
    {
        $error_message = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = $_POST['username'];
            $password = $_POST['password'];

            if (empty($username) || empty($password)) {
                $error_message = "Please Enter the username and password";
            } else {
                $user = $this->userModel->getUserByUsername($username);
                if ($user && password_verify($password, $user['password'])) {
                    echo "login";
                    $_SESSION["user_id"] = $user['id'];
                    $_SESSION["username"] = $user['username'];
                    if ($user['isAdmin'] === 0) {
                        header("Location: /DTT/home");
                    } else {
                        header("Location: /DTT/dash");

                    }
                } else {
                    $error_message = "Invalid username or password.";

                }
            }
        }
    }
}