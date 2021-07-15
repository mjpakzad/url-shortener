<?php
if(!function_exists('config')) {
    function config($file, $config = null) {
        $configs = require __DIR__ . '\..\configs\\' . $file . '.php';
        return $config ? $configs[$config] : $configs;
    }
}