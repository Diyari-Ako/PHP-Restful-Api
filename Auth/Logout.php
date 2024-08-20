<?php


namespace Auth;


use JWT\JWTcodec;
use Model\RefreshToken;
use Validations\UserValidation;
use Validations\AuthValidation;

class Logout extends RefreshToken
{
    use UserValidation,JWTcodec,AuthValidation;

    public function logout() : void
    {
        $data = $this->CheckRefreshToken();

        $payload = $this->CheckDecode($data);

        $user_id = $payload["sub"];

        $refresh_tokenn = $this->getByToken($data["token"]);
        if (!$refresh_tokenn) {
            $this->respondBadRequest("Invalid token (not in the white list)");
            exit;
        }

        $user = $this->getById($user_id);
        if (!$user) {
            $this->respondUnauthorized("Token is invalid. The user associated with this token has been deleted.");
            exit;
        }
        
        $rows = $this->delete($data["token"]);
        
        echo json_encode(["message" => "Refresh token deleted", "rows" => $rows]);
        
    }
}
