<?php

require_once '../funcao.php';

$data_fim = '2020-03-04';
$idusuario = 1;


if (!empty(gerenciarVencimento($idusuario, $data_fim))){
    echo "funcionou";
}