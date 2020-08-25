<?php

namespace OnzaMe\Helpers;

use Illuminate\Support\Str;

class Slug
{
    public static function generateSlug($string, $underline)
    {
        if ($underline == 0) {
            $underline = '';
        } else {
            $underline = '_'.$underline;
        }
        return Str::slug($string . $underline);
    }

    public static function getUniqueSlug($models, $string, $id = null, $count = 0)
    {
        $slug = Slug::generateSlug($string, $count);
        foreach ($models as $model) {
            $_count = $model->where('slug', $slug)->where('id', '!=', $id)->count();
            if ($_count > 0) {
                $count++;
                return Slug::getUniqueSlug($models, $string, $id, $count);
            }
        }
        return $slug;
    }
}
