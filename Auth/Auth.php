<?php

namespace Auth;

use Exception;
use JWT\JWTcodec;
use Model\RefreshToken;
use ResponseHandling\ResponseCode;
use CustomeException\InvalidFormatException;
use CustomeException\InvalidSignatureException;
use CustomeException\TokenExpiredException;


class Auth extends RefreshToken
{
    use JWTcodec,ResponseCode;
    private int $user_id;

    public function authenticateAccessToken(): bool
    {
        if (! preg_match("/^Bearer\s+(.*)$/", $_SERVER["HTTP_AUTHORIZATION"], $matches)) {
            $this->respondBadRequest("incomplete authorization header");
            return false;
        }

        try {
            $payload = $this->decode($matches[1]);
        } catch(InvalidFormatException){
            $this->respondUnauthorized("invalid token format");
            return false;
        }catch (InvalidSignatureException) {
            $this->respondUnauthorized("Invalid Signature");
            return false;
        }  catch (TokenExpiredException) {
            $this->respondUnauthorized("Token has expired");
            return false;
        } catch (Exception $e) {
            $this->respondUnauthorized($e->getMessage());
            return false;
        }

        
        $refresh_tokenn = $this->getByToken($matches[1]);
        if ($refresh_tokenn) {
            $this->respondBadRequest("Use access token instead refresh token");
            exit;
        }
        $user = $this->getById($payload["sub"]);
        if (!$user) {
            $this->respondUnauthorized("Token is invalid. The user associated with this token has been deleted.");
            exit;
        }
        $this->user_id = $payload["sub"];
        return true;
    }

    public function getUserId() : int
    {
        return $this->user_id;
    }
}
