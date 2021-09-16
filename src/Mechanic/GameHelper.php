<?php

namespace App\Mechanic;

class GameHelper
{

    public static function clamp($current, $min, $max) {
        return max($min, min($max, $current));
    }

    public static function safeArrayElement($name, $array){
        if (array_key_exists($name, $array)){
            return $array[$name];
        }
        return null;
    }
}