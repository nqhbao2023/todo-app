<?php

if (!function_exists('formValue')) {
    function formValue($name, $model = null, $default = '') {
        return old($name, $model ? $model->$name ?? $default : $default);
    }
}
