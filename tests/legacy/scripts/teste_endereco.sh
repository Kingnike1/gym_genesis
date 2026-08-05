#!/bin/bash

API_URL="http://localhost:83/public/api/index.php?entidade=endereco"

echo "========== CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1,
    "tipo": "usuario",
    "cep": "12345-678",
    "rua": "Rua Teste",
    "numero": "100",
    "complemento": "Apto 202",
    "bairro": "Centro",
    "cidade": "São Paulo",
    "estado": "SP"
  }' | jq
echo -e "\n"

echo "========== LISTAR =========="
curl -s "$API_URL&acao=listar" \
  -H "Content-Type: application/json" \
  -d '{"tipo": "usuario"}' | jq
echo -e "\n"

echo "========== EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
  -H "Content-Type: application/json" \
  -d '{
    "id": 1,
    "tipo": "usuario",
    "cep": "54321-000",
    "rua": "Rua Atualizada",
    "numero": "200",
    "complemento": "Casa",
    "bairro": "Bairro Novo",
    "cidade": "Rio de Janeiro",
    "estado": "RJ"
  }' | jq
echo -e "\n"

echo "========== LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" \
  -H "Content-Type: application/json" \
  -d '{"tipo": "usuario"}' | jq
echo -e "\n"

echo "========== DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
  -H "Content-Type: application/json" \
  -d '{"id": 1, "tipo": "usuario"}' | jq
echo -e "\n"

echo "========== LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" \
  -H "Content-Type: application/json" \
  -d '{"tipo": "usuario"}' | jq
echo -e "\n"
