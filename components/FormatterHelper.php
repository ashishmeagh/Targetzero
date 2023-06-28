<?php

namespace app\components;

use yii\i18n\Formatter;

class FormatterHelper extends Formatter {

    public static function asPhone($value) {
        return preg_replace("/^(\d{3})(\d{3})(\d{4})$/", "$1-$2-$3", $value);
    }

}