<?php

namespace OnzaMe\Helpers\Http\Requests;

use Illuminate\Auth\Authenticatable;
use Illuminate\Foundation\Http\FormRequest;

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
}
