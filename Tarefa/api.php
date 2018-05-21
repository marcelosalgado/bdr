<?php

require_once('autoload.php');
require_once('config.php');

$errorMessage = null;

$tarefas = new Tarefa\Classes\Tarefas($dbInfo);

echo $tarefas->respondeRequisicao();
exit;
