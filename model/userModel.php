<?php
class UserModel
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }
    /**
     * Insert a User into the DB.
     */
    public function createUser($username, $email, $password, $isAdmin)
    {
        try {
            $hashPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("INSERT INTO user (username,email,password,isAdmin) VALUES(?,?,?,?)");
            $stmt->execute([$username, $email, $hashPassword, $isAdmin]);
            return $this->db->lastInsertId();

        } catch (PDOException $e) {
            echo "Error while adding new user :" . $e->getMessage();
            return false;
        }
    }



    /**
     * Get The user inforrmation By the Username 
     */
    public function getUserByUsername($username)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM user WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user;
        } catch (\PDOException $e) {
            echo "Eroor while fetching user : " . $e->getMessage();
            return false;
        }
    }
}


?>