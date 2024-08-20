<?php

namespace Controller;


use Model\Post;
use Validations\PostValidation;

class PostController extends Post
{

    use PostValidation;

    protected function Create(int $user_id)
    {
        $data = $this->validateInput(true);
        if ($data === false) {
            return;
        }
        $id = $this->createPost($data, $user_id);
        $this->respondCreated("Task created, id= $id");
    }

    protected function GetAll(): void
    {
        $data = $this->validateFilterAndPagination();
        if (!$data) {
            return;
        }

        $resault = $this->getAllPosts($data["title"], $data["page"], $data["limit"]);
        $title = $data['title'];
        if (!$resault) {
            echo json_encode(["message" => "No record found title $title"]);
        } else {
            echo json_encode($resault);
        }
    }

    protected function GetUserPost(string $id): void
    {

        $resault = $this->getPostsByUserId($id);
        if (!$resault) {
            $this->respondNotFound("Posts for that user_id:$id not found.");
            return;
        }
        echo json_encode($resault);
    }

    protected function GetById(?string $id, int $user_id): array
    {

        $data = $this->getPostById($id, $user_id);
        if (!$data) {
            $this->respondNotFound("Post with id:$id not found");
            exit;
        }

        return $data;
    }

    protected function Update(string $id, int $user_id): void
    {
        $data = $this->validateInput(false);
        if (!$data) {
            return;
        }
        $rows = $this->updatePost($id, $data, $user_id);
        if ($rows === 2) {
            echo json_encode(["message" => "title or content key required to update the post."]);
        } elseif ($rows === 0) {
            echo json_encode(["message" => "No changes were made as the provided data is the same as what's already in the database."]);
        } else {
            echo json_encode(["message" => "The post was successfully updated.", "rows" => $rows]);
        }
    }

    protected function Delete(string $id, int $user_id): void
    {
        $rows  = $this->deletePost($id, $user_id);
        echo json_encode(["message" => "Task deleted", "rows" => $rows]);
    }
}
