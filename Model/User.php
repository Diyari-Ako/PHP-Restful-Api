<?php

namespace Model;

use PDO;
use Auth\Login;
use Auth\Logout;
use JWT\Refresh;
use Config\Database;

class User
{

    private PDO $conn;
    private Database $db;

    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->conn = $database->getConnection();
    }

    protected function register(array $data): void
    {

        $sql = "INSERT INTO users (username,address,age,email,password)
                VALUES (:username,:address,:age,:email,:password)";

        $username = $data["username"];

        $stmt = $this->conn->prepare($sql);

        $password_hash = password_hash($data["password"], PASSWORD_DEFAULT);

        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->bindValue(":address", $data["address"], PDO::PARAM_STR);
        $stmt->bindValue(":age", $data["age"], PDO::PARAM_INT);
        $stmt->bindValue(":email", $data["email"], PDO::PARAM_STR);
        $stmt->bindValue(":password", $password_hash, PDO::PARAM_STR);

        $stmt->execute();

        echo json_encode("Thank  you $username  for registering");
    }

    protected function login(): void
    {
        (new Login($this->db))->login();
    }
    protected function logout()
    {
        (new Logout($this->db))->logout();
    }
    protected function refresh(): void
    {
        (new Refresh($this->db))->refreshToken();
    }

    protected function getAllUsers(): void
    {
        $sql = "SELECT username,address,age,email FROM users";

        $stmt = $this->conn->query($sql);

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($data);
    }

    protected function getUserById(string $id): array | false
    {
        $sql = "SELECT id,username,address,age,email FROM users 
                WHERE id=:id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    protected function updateUser(string $id, array $data): int
    {
        $fields = [];

        if (!empty($data["username"])) {
            $fields["username"] = [$data["username"], PDO::PARAM_STR];
        }
        if (!empty($data["address"])) {
            $fields["address"] = [$data["address"], PDO::PARAM_STR];
        }
        if (!empty($data["email"])) {
            $fields["email"] = [$data["email"], PDO::PARAM_STR];
        }
        if (!empty($data["age"])) {
            $fields["age"] = [$data["age"], PDO::PARAM_INT];
        }
        if (!empty($data["password"])) {
            $fields["password"] = [password_hash($data["password"], PASSWORD_DEFAULT), PDO::PARAM_STR];
        }

        if (empty($fields)) {
            return 2;
        } else {
            $sets = array_map(function ($value) {
                return "$value = :$value";
            }, array_keys($fields));

            $sql = "UPDATE users"
                . " SET " . implode(", ", $sets)
                . " WHERE id = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            foreach ($fields as $name => $values) {
                $stmt->bindValue(":$name", $values[0], $values[1]);
            }

            $stmt->execute();

            return $stmt->rowCount();
        }
    }



    protected function deleteUser($id): int
    {
        $sql = "DELETE FROM users WHERE id= :id ";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    protected function getObject()
    {
        return new User($this->db);
    }
    public function isDuplicateEmail($email): bool
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);


        return $result['count'] > 0;
    }
    public function isDuplicateUsername($username): bool
    {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);


        return $result['count'] > 0;
    }
}
