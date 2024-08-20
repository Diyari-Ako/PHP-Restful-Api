<?php

namespace Validations;

use Exception;
use Auth\Auth;
use Config\Database;
use ResponseHandling\ResponseCode;
use CustomeException\InvalidFormatException;
use CustomeException\InvalidSignatureException;
use CustomeException\TokenExpiredException;


trait AuthValidation
{
    use ResponseCode;

    protected function AuthPost(Database $db): int
    {
        $auth = new Auth($db);
        if (! $auth->authenticateAccessToken()) {
            exit;
        }
        $user_id = $auth->getUserId();
        return $user_id;
    }
    protected function AuthUser(?string $uriSegments, Database $db): int
    {
        $skipAuth = false;
        if (
            $uriSegments === "login" || $uriSegments === "logout"
            || $uriSegments === "register" || $uriSegments === "refresh"
        ) {
            $skipAuth = true;
        }

        if (!$skipAuth) {
            $user_id = $this->AuthPost($db);
            return $user_id;
        }
        return 0;
    }

    protected function CheckRefreshToken(): array
    {
        $data = $this->refreshValidation();

        if (!$data) {
            $this->respondBadRequest("messing token");
            exit;
        }

        return $data;
    }

    protected function CheckDecode(array $data): array
    {
        try {
            $payload = $this->decode($data["token"]);
        } catch (InvalidFormatException) {
            $this->respondUnauthorized("invalid token format");
            exit;
        } catch (InvalidSignatureException) {
            $this->respondUnauthorized("Invalid Signature");
            exit;
        } catch (TokenExpiredException) {
            $this->respondUnauthorized("Token has expired");
            exit;
        } catch (Exception $e) {
            $this->respondUnauthorized($e->getMessage());
            exit;
        }
        return $payload;
    }
    protected function refreshValidation(): array | false
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        if (! array_key_exists("token", $data)) {
            return false;
        }
        return $data;
    }
}
