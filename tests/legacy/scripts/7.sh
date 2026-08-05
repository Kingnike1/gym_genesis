BASE_URL="http://localhost:83/public/api/index.php"

# 7. cupom_desconto
echo "========== CUPOM_DESCONTO =========="
API_URL="$BASE_URL?entidade=cupom_desconto"
echo "== CADASTRAR =="
curl -s -X POST "$API_URL&acao=cadastrar" -H "Content-Type: application/json" -d '{"codigo":"PROMO10","percentual_desconto":10.00,"valor_desconto":null,"data_validade":"2025-12-31","quantidade_uso":100,"tipo":"percentual"}' | jq
echo "== LISTAR =="
curl -s "$API_URL&acao=listar" | jq
echo "== EDITAR =="
curl -s -X POST "$API_URL&acao=editar" -H "Content-Type: application/json" -d '{"id":1,"codigo":"FIXO20","percentual_desconto":null,"valor_desconto":20.00,"data_validade":"2025-06-30","quantidade_uso":50,"tipo":"fixo"}' | jq
echo "== LISTAR NOVAMENTE =="
curl -s "$API_URL&acao=listar" | jq
echo "== DELETAR =="
curl -s -X POST "$API_URL&acao=deletar" -H "Content-Type: application/json" -d '{"id":1}' | jq
echo "== LISTAR FINAL =="
curl -s "$API_URL&acao=listar" | jq

# 8. dieta_alimentar
echo "========== DIETA_ALIMENTAR =========="
API_URL="$BASE_URL?entidade=dieta_alimentar"
echo "== CADASTRAR =="
curl -s -X POST "$API_URL&acao=cadastrar" -H "Content-Type: application/json" -d '{"alimento_id":1,"refeicao_id":1,"quantidade":"200g","observacao":"Proteína para o café da manhã"}' | jq
echo "== LISTAR =="
curl -s "$API_URL&acao=listar" | jq
echo "== EDITAR =="
curl -s -X POST "$API_URL&acao=editar" -H "Content-Type: application/json" -d '{"id":1,"alimento_id":2,"refeicao_id":2,"quantidade":"150g","observacao":"Carboidrato para o almoço"}' | jq
echo "== LISTAR NOVAMENTE =="
curl -s "$API_URL&acao=listar" | jq
echo "== DELETAR =="
curl -s -X POST "$API_URL&acao=deletar" -H "Content-Type: application/json" -d '{"id":1}' | jq
echo "== LISTAR FINAL =="
curl -s "$API_URL&acao=listar" | jq

# 9. dieta
echo "========== DIETA =========="
API_URL="$BASE_URL?entidade=dieta"
echo "== CADASTRAR =="
curl -s -X POST "$API_URL&acao=cadastrar" -H "Content-Type: application/json" -d '{"descricao":"Dieta de ganho de massa","data_inicio":"2025-01-01","data_fim":"2025-03-01","usuario_idusuario":1}' | jq
echo "== LISTAR =="
curl -s "$API_URL&acao=listar" | jq
echo "== EDITAR =="
curl -s -X POST "$API_URL&acao=editar" -H "Content-Type: application/json" -d '{"id":1,"descricao":"Dieta de definição","data_inicio":"2025-02-15","data_fim":null,"usuario_idusuario":2}' | jq
echo "== LISTAR NOVAMENTE =="
curl -s "$API_URL&acao=listar" | jq
echo "== DELETAR =="
curl -s -X POST "$API_URL&acao=deletar" -H "Content-Type: application/json" -d '{"id":1}' | jq
echo "== LISTAR FINAL =="
curl -s "$API_URL&acao=listar" | jq