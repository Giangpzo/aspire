<?php

namespace Tests\Helpers;

class Helpers
{
    public static function generate_string_by_length(int $length)
    {
        $baseChar = '';
        for ($i = 0; $i < $length; $i++) {
            $baseChar = $baseChar . '1';
        }
        return $baseChar;
    }
}