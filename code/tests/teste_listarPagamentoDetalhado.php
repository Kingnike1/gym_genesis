<?php
require_once '../../code/funcao.php';

header('Content-Type: application/json');


$idpagamento2 = null;

$json = json_encode(listarPagamentosDetalhados($idpagamento2)
, JSON_UNESCAPED_UNICODE);
echo $json;


