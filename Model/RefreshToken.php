<?php

namespace Model;

use PDO;
use JWT\JWTcodec;
use Config\Database;


class RefreshToken
{

    use JWTcodec;

    private PDO $conn;
    private string $key;

    public function __construct(Database $database, string $key = self::KEY)
    {

        $this->conn = $database->getConnection();
        $this->key = $key;
    }

    protected function create(string $token, int $expiry): bool
    {

        $hash = hash_hmac("sha256", $token, $this->key);

        $sql = "INSERT INTO refresh_token (token_hash, expires_at)
                VALUES (:token_hash, :expires_at)";


        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":token_hash", $hash, PDO::PARAM_STR);
        $stmt->bindValue(":expires_at", $expiry, PDO::PARAM_INT);

        return $stmt->execute();
    }

    protected function delete(string $token): int
    {
        $hash = hash_hmac("sha256", $token, $this->key);

        $sql = "DELETE FROM refresh_token
                WHERE token_hash = :token_hash";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":token_hash", $hash, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    protected function getByToken(string $token): array | false
    {
        $hash = hash_hmac("sha256", $token, $this->key);

        $sql = "SELECT *  FROM refresh_token
                WHERE token_hash = :token_hash";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":token_hash", $hash, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function getByUsername(string $username): array | false
    {
        $sql = "SELECT * FROM users WHERE username=:username";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":username", $username, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function getById(int $id): array | false
    {
        $sql = "SELECT * FROM users WHERE id=:id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteExpired(): int
    {
        $sql = "DELETE FROM refresh_token
            WHERE expires_at < UNIX_TIMESTAMP()";

        $stmt = $this->conn->query($sql);

        return $stmt->rowCount();
    }
}
