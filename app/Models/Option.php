<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $table = 'options';
    protected $fillable = ['option_name', 'option_value', 'autoload'];

    public $timestamps = true;

    public static function get($name, $default = null)
    {
        $option = static::where('option_name', $name)->first();
        return $option ? $option->option_value : $default;
    }

    public static function set($name, $value, $autoload = 'yes')
    {
        return static::updateOrCreate(
            ['option_name' => $name],
            ['option_value' => $value, 'autoload' => $autoload]
        );
    }
}
