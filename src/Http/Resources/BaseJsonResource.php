<?php

namespace OnzaMe\Helpers\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseJsonResource extends JsonResource
{
    protected function mergeWhenNotNull(string $fieldName)
    {
        return $this->mergeWhen(!is_null($this->{$fieldName}), [$fieldName => $this->{$fieldName}]);
    }

    protected function mergeWhenLoaded(string $relationship, string $fieldName = null)
    {
        $fieldName = empty($fieldName) ? $relationship : $fieldName;
        if (!$this->resource->relationLoaded($relationship)) {
            return null;
        }
        if ($this->resource->{$relationship} === null) {
            return null;
        }
        return $this->merge([
            $fieldName => $this->resource->{$relationship}
        ]);
    }

    protected function mergeDefaultColumns(array $defaultColumns = ['id', 'created_at', 'updated_at'])
    {
        $defaultColumnValues = [];
        foreach ($defaultColumns as $columnName) {
            $defaultColumnValues[] = $this->mergeWhenNotNull($columnName);
        }
        return $this->merge($defaultColumnValues);
    }

    protected function mergeColumns(array $columns, array $defaultColumns = ['id', 'created_at', 'updated_at'], bool $whenNotNull = true)
    {
        $columnValues = [];
        foreach (array_merge($columns, $defaultColumns) as $index => $columnName) {
            if (is_int($index)) {
                $columnValues[] = $this->handleMerge($columnName, $whenNotNull);
            } else {
                $value = $columnName($index);
                if ($whenNotNull && $value === null) {
                    continue;
                }
                $columnValues[] = $this->merge([
                    $index => $value
                ]);
            }
        }
        return $this->merge($columnValues);
    }

    private function handleMerge(string $columnName, bool $whenNotNull = true)
    {
        if ($whenNotNull) {
             return $this->mergeWhenNotNull($columnName);
        }
        return $this->merge([$columnName => $this->{$columnName}]);
    }
}
