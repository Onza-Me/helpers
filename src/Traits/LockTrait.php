<?php

namespace OnzaMe\Helpers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

trait LockTrait
{
    public function isLocked(Model $model)
    {
        return Cache::get($this->getCacheKey($model), false);
    }

    public function lock(Model $model) : bool
    {
        if($this->isLocked($model))
            return false;

        Cache::add($this->getCacheKey($model), true);

        return true;
    }

    public function unlock(Model $model)
    {
        if (!$this->isLocked($model)) {
            return;
        }
        Cache::forget($this->getCacheKey($model));
    }

    protected function getCacheKey(Model $model) : string
    {
        return get_class($model).'_'.$model->id;
    }

}
