<?php


namespace JWT;


use Model\RefreshToken;
use Validations\UserValidation;
use Validations\AuthValidation;

class Refresh extends RefreshToken
{
    use JWTcodec, UserValidation, Tokens, AuthValidation;

    public function refreshToken(): void
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

        $this->delete($data["token"]);

        $tokens = $this->createToken($user);

        $this->create($tokens["refresh_token"], $tokens["refresh_token_expiry"]);

        echo json_encode([
            "access_token" => $tokens["access_token"],
            "refresh_token" => $tokens["refresh_token"]
        ]);
    }
}
