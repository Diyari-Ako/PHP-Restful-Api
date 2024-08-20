<?php

namespace Controller;

use Model\User;
use Validations\UserValidation;

class UserController extends User
{
    use UserValidation;

    protected function Creat(User $user_object): void
    {
        $data = $this->validateInput($user_object, true);
        if (!$data) {
            return;
        }
        $this->register($data);
    }
    protected function GetById(string $uriSegments)
    {
        $data = $this->getUserById($uriSegments);
        if (!$data) {
            $this->respondNotFound("User with id:$uriSegments not found");
            return;
        }
        echo json_encode($data);
    }

    protected function Update(User $user_object, int $user_id): void
    {
        $data = $this->validateInput($user_object, false);
        if (!$data) {
            return;
        }
        $rows = $this->updateUser($user_id, $data);
        if ($rows === 2) {
            echo json_encode(["message" => "No user info entered to update"]);
        } elseif ($rows === 0) {
            echo json_encode(["message" => "No changes were made as the provided data is the same as what's already in the database."]);
        } else {
            echo json_encode(["message" => "The user information was successfully updated.", "rows" => $rows]);
        }
    }

    protected function Delete(int $user_id)
    {
        $rows = $this->deleteUser($user_id);
        echo json_encode(["message" => "User deleted", "rows" => $rows]);
    }
}
