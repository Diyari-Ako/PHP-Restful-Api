<?php

namespace ResponseHandling;

trait ResponseCode
{
    protected function respondMethodNotAllowed(string $allowed_methods): void
    {
        http_response_code(405);
        header("Allow: $allowed_methods");
        echo json_encode("Method Not Allowed, Allow: $allowed_methods");
    }
    protected function respondNotFound(string $message): void
    {
        http_response_code(404);
        echo json_encode(["message" => $message]);
    }
    protected function respondCreated(string $message): void
    {
        http_response_code(201);
        echo json_encode(["message" => $message]);
    }
    protected function respondUnprocessableEntity(array $errors): void
    {
        http_response_code(422);
        echo json_encode(["message" => $errors]);
    }
    protected function respondBadRequest(string $message): void
    {
        http_response_code(400);
        echo json_encode(["message" => $message]);
    }
    protected function respondUnauthorized(string $message): void
    {
        http_response_code(401);
        echo json_encode(["message" => $message]);
    }
}
