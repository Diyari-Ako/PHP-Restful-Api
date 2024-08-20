<?php

namespace Gateway;


use Config\Database;
use Controller\UserController;
use Validations\AuthValidation;
use Validations\UserValidation;


class UserGateway extends UserController{

    use UserValidation,AuthValidation;


    public function processEndpoint(string $method, ?string $uriSegments,Database $db): void
    {
       
        $user_id = $this->AuthUser($uriSegments,$db);

        $user_object =  $this->getObject();

        if ($method === 'POST' && $uriSegments === 'register') {

           $this->Creat($user_object);

        } elseif ($method === 'POST' && $uriSegments === 'login') {

            $this->login();

        } elseif ($method === 'POST' && $uriSegments === 'logout') {

            $this->logout();

        } elseif ($method === 'POST' && $uriSegments === "refresh") {

            $this->refresh();

        } elseif ($method === 'GET' && !isset($uriSegments)) {

            $this->getAllUsers();

        } elseif ($method === 'GET' && isset($uriSegments)) {

            $this->GetById($uriSegments);

        } elseif ($method === 'PATCH' && !isset($uriSegments)) {

            $this->Update($user_object,$user_id);
           
        } elseif ($method === 'DELETE' && !isset($uriSegments)) {

            $this->Delete($user_id);
            
        } else {
            $this->respondMethodNotAllowed("GET,POST,PATCH,DELETE");
        }
    }
}