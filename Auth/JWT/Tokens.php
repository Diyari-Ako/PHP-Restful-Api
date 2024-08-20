<?php

namespace JWT;

trait Tokens
{
    use JWTcodec;

    protected function createToken(array $user) :array
    {
        $payload = [
            "sub" => $user["id"],
            "name" => $user["username"],
            "exp" => time() + 3600      // 1 hour  
        ];
        $access_token = $this->encode($payload);

        $refresh_token_expiry = time() + 432000;    //5 days  
        
        $refresh_token = $this->encode([
            "sub" => $user["id"],
            "exp" => $refresh_token_expiry
        ]);

         $tokens=[
            "refresh_token"=>$refresh_token,
            "refresh_token_expiry"=>$refresh_token_expiry,
            "access_token"=>$access_token];
         return $tokens;
    }
}
