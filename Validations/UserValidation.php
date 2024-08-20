<?php

namespace Validations;

use Model\User;
use ResponseHandling\ResponseCode;

trait UserValidation
{

    use ResponseCode;

    protected function validateInput(User $obj, bool $is_new): false | array
    {

        $data = (array) json_decode(file_get_contents("php://input"), true);

        if (is_null($data) || empty($data)) {
            $this->respondUnprocessableEntity(["Input data is required."]);
            return false;
        }

        if ($is_new) {
            $errors = $this->registerValidation($data, $obj);
        } else {
            $errors = $this->updateUserValidation($data, $obj);
        }

        if (! empty($errors)) {

            $this->respondUnprocessableEntity($errors);
            return false;
        }
        return $data;
    }

    protected function registerValidation(array $data, User $obj): array
    {
        $errors = [];


        if (empty($data["username"]) || empty($data["address"]) || empty($data["age"]) || empty($data["email"]) || empty($data["password"])) {
            $errors[] = "All fields (username, address, age, email, password) are required.";
            return $errors;
        }


        if (!filter_var($data["age"], FILTER_VALIDATE_INT)) {
            $errors[] = "Age must be an integer.";
        } elseif ($data["age"] < 18) {
            $errors[] = "You must be at least 18 years old.";
        }


        if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Oops! It looks like the email address you entered isn't valid.";
        }


        if (strlen($data["password"]) < 8) {
            $errors[] = "Your password must be at least 8 characters long. Please enter a longer password and try again.";
        }


        if ($obj->isDuplicateUsername($data["username"])) {
            $errors[] = "The username you entered is already in use. Please choose another.";
        }
        if ($obj->isDuplicateEmail($data["email"])) {
            $errors[] = "The email you entered is already in use. Please choose another.";
        }

        return $errors;
    }


    protected function updateUserValidation(array $data, User $obj): array
    {
        $errors = [];


        if (!empty($data["username"])) {

            if ($obj->isDuplicateUsername($data["username"])) {
                $errors[] = "The username you entered is already in use. Please choose another.";
            }
        }

        if (!empty($data["email"])) {
            if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Oops! It looks like the email address you entered isn't valid.";
            } else {

                if ($obj->isDuplicateEmail($data["email"])) {
                    $errors[] = "The email you entered is already in use. Please choose another.";
                }
            }
        }

        if (!empty($data["age"])) {
            if (!filter_var($data["age"], FILTER_VALIDATE_INT)) {
                $errors[] = "Age must be an integer.";
            } elseif ($data["age"] < 18) {
                $errors[] = "You must be at least 18 years old.";
            }
        }

        if (!empty($data["password"])) {
            if (strlen($data["password"]) < 8) {
                $errors[] = "Your password must be at least 8 characters long. Please enter a longer password and try again.";
            }
        }

        return $errors;
    }


    protected function loginValidation(): array | false
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);

        if (! array_key_exists("username", $data) || ! array_key_exists("password", $data)) {

            return false;
        }
        return $data;
    }
}
