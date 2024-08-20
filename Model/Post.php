<?php

namespace Model;

use PDO;
use Config\Database;


class Post
{
    private PDO $conn;

    public function __construct(Database $database)
    {
        $this->conn = $database->getConnection();
    }

    protected function createPost(array $data, int $user_id): string
    {

        $sql = "INSERT INTO posts (title,content,user_id)
                VALUES (:title,:content,:user_id)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":content", $data["content"], PDO::PARAM_STR);
        $stmt->bindValue(":title", $data["title"], PDO::PARAM_STR);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }
    protected function getPostById(?string $id, int $user_id): array | false
    {
        $sql = "SELECT * FROM posts 
                WHERE id=:id AND user_id=:user_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function updatePost(string $id, array $data, int $user_id): int
    {
        $fields = [];

        if (!empty($data["title"])) {
            $fields["title"] = [$data["title"], PDO::PARAM_STR];
        }
        if (!empty($data["content"])) {
            $fields["content"] = [$data["content"], PDO::PARAM_STR];
        }


        if (empty($fields)) {
            return 2;
        } else {

            $sets = array_map(function ($value) {
                return "$value = :$value";
            }, array_keys($fields));

            $sql = "UPDATE posts"
                . " SET " . implode(", ", $sets)
                . " WHERE id = :id AND user_id=:user_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

            foreach ($fields as $name => $values) {
                $stmt->bindValue(":$name", $values[0], $values[1]);
            }

            $stmt->execute();

            return $stmt->rowCount();
        }
    }
    protected function deletePost(string $id, int $user_id): int
    {
        $sql = "DELETE FROM posts
                WHERE id= :id AND user_id=:user_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    protected function getAllPosts(string $title, int $page, int $limit): array | false
    {
        $offset = ($page - 1) * $limit;

        $sql = "SELECT user_id,title,content,created_at FROM posts
                WHERE title = :title LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    protected function getPostsByUserId($user_id): array | false
    {

        $sql = "SELECT user_id,title,content,created_at FROM posts
                 WHERE user_id=:user_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
