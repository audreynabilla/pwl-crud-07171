<?php
class UserModel
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findByUsername($username)
    {
        $stmt = $this->conn->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function findById($id)
    {
        $stmt = $this->conn->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createUser($username, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        return $stmt->execute([$username, $hash]);
    }

    public function verifyLogin($username, $password)
    {
        $user = $this->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }
}
