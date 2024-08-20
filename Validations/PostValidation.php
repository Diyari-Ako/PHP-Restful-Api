<?php

namespace Validations;

use ResponseHandling\ResponseCode;

trait PostValidation
{
    use ResponseCode;

    protected function validateInput(bool $is_new): array | false
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);

        if (is_null($data) || empty($data)) {
            $this->respondUnprocessableEntity(["Input data is required."]);
            return false;
        }

        $errors = $this->getInputValidation($data, $is_new);

        if (! empty($errors)) {

            $this->respondUnprocessableEntity($errors);
            return false;
        }
        return $data;
    }

    protected function getInputValidation(array $data, bool $is_new): array
    {

        $errors = [];

        if ($is_new) {
            if (empty($data["title"]) || empty($data["content"])) {

                $errors[] = "title and content is required";
            }
        }

        return $errors;
    }

    protected function getFilterAndPaginationValidation(array $data, ?int $page, ?int $limit): array
    {
        $errors = [];


        if (empty($data["title"])) {
            $errors[] = "Title is required to filter the records.";
        }


        if (empty($page)) {
            $errors[] = "Page is required.";
        } elseif (!filter_var($page, FILTER_VALIDATE_INT) || $page <= 0) {
            $errors[] = "Page must be a positive integer.";
        }


        if (empty($limit)) {
            $errors[] = "Limit is required.";
        } elseif (!filter_var($limit, FILTER_VALIDATE_INT) || $limit <= 0) {
            $errors[] = "Limit must be a positive integer.";
        }

        return $errors;
    }


    protected function validateFilterAndPagination(): array | false
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);

        $page = isset($_GET['page']) ? (int)$_GET['page'] : null;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;

        $errors = $this->getFilterAndPaginationValidation($data, $page, $limit);


        if (! empty($errors)) {

            $this->respondUnprocessableEntity($errors);
            return false;
        }
        $data = [
            "title" => $data['title'],
            "page" => $page,
            "limit" => $limit
        ];
        return $data;
    }
}
