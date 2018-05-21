<?php

function __autoload($class) {
    $exploded = explode('\\', $class);
    if ($exploded[0] == 'Tarefa') {
        unset($exploded[0]);
    }
    $class = implode('/', $exploded) . '.php';
    require_once($class);
}