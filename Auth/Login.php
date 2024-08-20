<?php

namespace Auth;

use JWT\Tokens;
use Validations\UserValidation;
use Model\RefreshToken;

class Login extends RefreshToken
{
    use UserValidation, Tokens;

    public function login(): void
    {
        $data = $this->loginValidation();

        if (!$data) {
            $this->respondBadRequest("messing login credentials (username,passowrd) required");
            exit;
        }

        $user = $this->getByUsername($data["username"]);

        if (!$user) {
            $this->respondBadRequest("invalid username");
            exit;
        }

        if (! password_verify($data["password"], $user["password"])) {
            $this->respondBadRequest("invalid password");
            exit;
        }

        $tokens = $this->createToken($user);

        $this->create($tokens["refresh_token"], $tokens["refresh_token_expiry"]);

        echo json_encode([
            "access_token" => $tokens["access_token"],
            "refresh_token" => $tokens["refresh_token"]
        ]);
    }
}
