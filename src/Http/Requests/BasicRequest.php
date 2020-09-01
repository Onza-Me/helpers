<?php

namespace OnzaMe\Helpers\Http\Requests;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BasicRequest extends FormRequest
{
    public function getUser(): Authenticatable
    {
        $user = $this->user();
        return $user;
    }
    public function getUserId(): int
    {
        return $this->getUser()->id;
    }

    public function getRouteParameter(string $paramName, $default = null)
    {
        return $this->route()->parameter($paramName, $default);
    }

    public function getRouteParameterId()
    {
        return $this->getRouteParameter('id');
    }

    public function isOwner(string $modelClass, $id): bool
    {
        return $modelClass::query()
            ->where('id', $id)
            ->where('user_id', $this->getUserId())
            ->exists();
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'error' => [
                "message" => [
                    "title" => "Данные не прошли проверку",
                    "description" => "Введите корректные данные"
                ],
                "fields" => $validator->errors()
            ]
        ], 422));
    }
}
