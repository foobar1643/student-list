<?php

namespace App\Model\Helper;

class TokenHelper {

    public static function generate_token() {
        $source = str_split('abcdefghijklmnopqrstuvwxyz'
          .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
          .'0123456789');
        for($i = 0; $i < 40; $i++) $result .= $source[rand(0, count($source))];
        return $result;
    }

}