# test_teste_geral.sh

#!/bin/bash

# ==================== ALIMENTO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=alimento"

echo "========== ALIMENTO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"Arroz","calorias":130,"proteinas":2.5,"carboidratos":28,"gorduras":0.3}' | jq
echo -e "\n"

echo "========== ALIMENTO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== ALIMENTO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"Arroz Integral","calorias":120,"proteinas":2.7,"carboidratos":25,"gorduras":0.4}' | jq
echo -e "\n"

echo "========== ALIMENTO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== ALIMENTO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"

echo "========== ALIMENTO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== ASSINATURA ====================
API_URL="http://localhost:83/public/api/index.php?entidade=assinatura"

echo "========== ASSINATURA - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"plano_id":1,"status":"ativa","data_inicio":"2024-01-01","data_fim":"2024-12-31"}' | jq
echo -e "\n"

echo "========== ASSINATURA - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== ASSINATURA - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"plano_id":1,"status":"inativa","data_inicio":"2024-01-01","data_fim":"2024-06-30"}' | jq
echo -e "\n"

echo "========== ASSINATURA - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== ASSINATURA - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"

echo "========== ASSINATURA - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== AULA_AGENDADA ====================
API_URL="http://localhost:83/public/api/index.php?entidade=aula_agendada"

echo "========== AULA_AGENDADA - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"aula_id":1,"data":"2024-07-01","hora":"10:00"}' | jq
echo -e "\n"

echo "========== AULA_AGENDADA - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== AULA_AGENDADA - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"aula_id":1,"data":"2024-07-02","hora":"11:00"}' | jq
echo -e "\n"

echo "========== AULA_AGENDADA - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== AULA_AGENDADA - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"

echo "========== AULA_AGENDADA - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== AVALIACAO_FISICA ====================
API_URL="http://localhost:83/public/api/index.php?entidade=avaliacao_fisica"

echo "========== AVALIACAO_FISICA - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"peso":70,"altura":1.75,"data":"2024-06-01"}' | jq
echo -e "\n"

echo "========== AVALIACAO_FISICA - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== AVALIACAO_FISICA - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"peso":72,"altura":1.75,"data":"2024-06-15"}' | jq
echo -e "\n"

echo "========== AVALIACAO_FISICA - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== AVALIACAO_FISICA - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"

echo "========== AVALIACAO_FISICA - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== CARGO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=cargo"

echo "========== CARGO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"Instrutor"}' | jq
echo -e "\n"

echo "========== CARGO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== CARGO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"Personal Trainer"}' | jq
echo -e "\n"

echo "========== CARGO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== CARGO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"

echo "========== CARGO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== CATEGORIA_PRODUTO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=categoria_produto"

echo "========== CATEGORIA_PRODUTO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"Suplementos"}' | jq
echo -e "\n"

echo "========== CATEGORIA_PRODUTO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== CATEGORIA_PRODUTO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"Acessórios"}' | jq
echo -e "\n"

echo "========== CATEGORIA_PRODUTO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== CATEGORIA_PRODUTO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"

echo "========== CATEGORIA_PRODUTO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== CUPOM_DESCONTO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=cupom_desconto"

echo "========== CUPOM_DESCONTO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"codigo":"PROMO10","desconto":10,"validade":"2024-12-31"}' | jq
echo -e "\n"

echo "========== CUPOM_DESCONTO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== CUPOM_DESCONTO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"codigo":"PROMO20","desconto":20,"validade":"2024-12-31"}' | jq
echo -e "\n"

echo "========== CUPOM_DESCONTO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== CUPOM_DESCONTO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"

echo "========== CUPOM_DESCONTO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== DIETA_ALIMENTAR ====================
API_URL="http://localhost:83/public/api/index.php?entidade=dieta_alimentar"

echo "========== DIETA_ALIMENTAR - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"dieta_id":1,"data_inicio":"2024-06-01","data_fim":"2024-07-01"}' | jq
echo -e "\n"

echo "========== DIETA_ALIMENTAR - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== DIETA_ALIMENTAR - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"dieta_id":1,"data_inicio":"2024-06-15","data_fim":"2024-07-15"}' | jq
echo -e "\n"

echo "========== DIETA_ALIMENTAR - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== DIETA_ALIMENTAR - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"

echo "========== DIETA_ALIMENTAR - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== DIETA ====================
API_URL="http://localhost:83/public/api/index.php?entidade=dieta"

echo "========== DIETA - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"Dieta Low Carb","descricao":"Poucos carboidratos"}' | jq
echo -e "\n"

echo "========== DIETA - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== DIETA - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"Dieta Cetogênica","descricao":"Muito baixa em carboidratos"}' | jq
echo -e "\n"

echo "========== DIETA - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== DIETA - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"

echo "========== DIETA - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== ENDERECO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=endereco"

echo "========== ENDERECO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"tipo":"usuario","cep":"12345-678","rua":"Rua Teste","numero":"100","complemento":"Apto 202","bairro":"Centro","cidade":"São Paulo","estado":"SP"}' | jq
echo -e "\n"

echo "========== ENDERECO - LISTAR =========="
curl -s "$API_URL&acao=listar" \
    -H "Content-Type: application/json" \
    -d '{"tipo":"usuario"}' | jq
echo -e "\n"

echo "========== ENDERECO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"tipo":"usuario","cep":"54321-000","rua":"Rua Atualizada","numero":"200","complemento":"Casa","bairro":"Bairro Novo","cidade":"Rio de Janeiro","estado":"RJ"}' | jq
echo -e "\n"

echo "========== ENDERECO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" \
    -H "Content-Type: application/json" \
    -d '{"tipo":"usuario"}' | jq
echo -e "\n"

echo "========== ENDERECO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"tipo":"usuario"}' | jq
echo -e "\n"

echo "========== ENDERECO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" \
    -H "Content-Type: application/json" \
    -d '{"tipo":"usuario"}' | jq
echo -e "\n"

# ==================== EXERCICIO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=exercicio"   
echo "========== EXERCICIO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"Agachamento","descricao":"Exercício para pernas","grupo_muscular":"Pernas"}' | jq
echo -e "\n"
echo "========== EXERCICIO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== EXERCICIO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"Supino","descricao":"Exercício para peito","grupo_muscular":"Peito"}' | jq
echo -e "\n"
echo "========== EXERCICIO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== EXERCICIO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"    
echo "========== EXERCICIO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
