<?php


require_once '../funcao.php';

$data_aula = '2023-03-04'; 
$dia_semana = 'Segunda';
$hora_inicio = '02:32:42'; 
$hora_fim = '02:39:42';
$idaula = 1;

if (!empty((editarAulaAgendada($data_aula, $dia_semana, $hora_inicio, $hora_fim, $idaula)))){
    echo "funcionou";
}
