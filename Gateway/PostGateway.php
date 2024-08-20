<?php

namespace Gateway;

use Config\Database;
use Controller\PostController;
use Validations\AuthValidation;
use Validations\PostValidation;


class PostGateway extends PostController
{

    use PostValidation, AuthValidation;
    public function processEndpoint(string $method, ?string $id, ?string $uriSegments,Database $db): void
    {

        $user_id = $this->Authpost($db);

        if ($method === 'POST' && !isset($id)) {

            $this->Create($user_id);

        } elseif ($method === 'GET' && !isset($id)) {

            $this->GetAll();

        } elseif ($method === 'GET' && isset($id) && isset($uriSegments) && $uriSegments === 'users') {

            $this->GetUserPost($id);

        } else {

            $data = $this->GetById($id, $user_id);

            if ($method === 'GET' && isset($id)) {

                echo json_encode($data);

            } elseif ($method === 'PATCH' && isset($id)) {

                $this->Update($id, $user_id);

            } elseif ($method === 'DELETE' && isset($id)) {

                $this->Delete($id, $user_id);

            } else {
                $this->respondMethodNotAllowed("GET,POST,PATCH,DELETE");
            }
        }
    }
}
