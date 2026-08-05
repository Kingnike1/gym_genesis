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

# ==================== FORUM ====================
API_URL="http://localhost:83/public/api/index.php?entidade=forum"
echo "========== FORUM - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"titulo":"Dúvidas sobre treino","descricao":"Como melhorar o agachamento?"}' | jq
echo -e "\n"
echo "========== FORUM - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== FORUM - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"titulo":"Dúvidas sobre dieta","descricao":"Qual melhor dieta para hipertrofia?"}' | jq
echo -e "\n"
echo "========== FORUM - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== FORUM - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== FORUM - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== FUNCIONARIO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=funcionario"
echo "========== FUNCIONARIO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"João Silva","cargo_id":1,"cpf":"12345678900"}' | jq
echo -e "\n"
echo "========== FUNCIONARIO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== FUNCIONARIO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"João Souza","cargo_id":1,"cpf":"12345678900"}' | jq
echo -e "\n"
echo "========== FUNCIONARIO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== FUNCIONARIO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== FUNCIONARIO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== HISTORICO_PESO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=historico_peso"
echo "========== HISTORICO_PESO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"peso":70,"data":"2024-06-01"}' | jq
echo -e "\n"
echo "========== HISTORICO_PESO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== HISTORICO_PESO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"peso":72,"data":"2024-06-15"}' | jq
echo -e "\n"
echo "========== HISTORICO_PESO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== HISTORICO_PESO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== HISTORICO_PESO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== HISTORICO_TREINO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=historico_treino"
echo "========== HISTORICO_TREINO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"treino_id":1,"data":"2024-06-01"}' | jq
echo -e "\n"
echo "========== HISTORICO_TREINO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== HISTORICO_TREINO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"treino_id":1,"data":"2024-06-15"}' | jq
echo -e "\n"
echo "========== HISTORICO_TREINO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== HISTORICO_TREINO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== HISTORICO_TREINO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== ITEM_PEDIDO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=item_pedido"
echo "========== ITEM_PEDIDO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"pedido_id":1,"produto_id":1,"quantidade":2,"preco_unitario":50.0}' | jq
echo -e "\n"
echo "========== ITEM_PEDIDO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== ITEM_PEDIDO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"pedido_id":1,"produto_id":1,"quantidade":3,"preco_unitario":45.0}' | jq
echo -e "\n"
echo "========== ITEM_PEDIDO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== ITEM_PEDIDO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== ITEM_PEDIDO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== META ====================
API_URL="http://localhost:83/public/api/index.php?entidade=meta"
echo "========== META - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"descricao":"Perder peso","data_inicio":"2024-06-01","data_fim":"2024-07-01"}' | jq
echo -e "\n"
echo "========== META - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== META - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"descricao":"Ganhar massa","data_inicio":"2024-06-15","data_fim":"2024-08-01"}' | jq
echo -e "\n"
echo "========== META - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== META - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== META - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== PAGAMENTO_DETALHE_ASSINATURA ====================
API_URL="http://localhost:83/public/api/index.php?entidade=pagamento_detalhe_assinatura"
echo "========== PAGAMENTO_DETALHE_ASSINATURA - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"pagamento_id":1,"assinatura_id":1,"valor":100.0}' | jq
echo -e "\n"
echo "========== PAGAMENTO_DETALHE_ASSINATURA - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== PAGAMENTO_DETALHE_ASSINATURA - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"pagamento_id":1,"assinatura_id":1,"valor":120.0}' | jq
echo -e "\n"
echo "========== PAGAMENTO_DETALHE_ASSINATURA - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== PAGAMENTO_DETALHE_ASSINATURA - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== PAGAMENTO_DETALHE_ASSINATURA - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== PAGAMENTO_DETALHE ====================
API_URL="http://localhost:83/public/api/index.php?entidade=pagamento_detalhe"

echo "========== PAGAMENTO_DETALHE - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"tipo":"cartao","bandeira_cartao":"Mastercard","ultimos_digitos":"5678","codigo_pix":null,"linha_digitavel_boleto":null,"pagamento_id":1}' | jq
echo -e "\n"

echo "========== PAGAMENTO_DETALHE - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== PAGAMENTO_DETALHE - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"tipo":"cartao","bandeira_cartao":"Visa","ultimos_digitos":"4321","codigo_pix":null,"linha_digitavel_boleto":null,"pagamento_id":1}' | jq
echo -e "\n"

echo "========== PAGAMENTO_DETALHE - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== PAGAMENTO_DETALHE - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"

echo "========== PAGAMENTO_DETALHE - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"


# ==================== PAGAMENTO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=pagamento"
echo "========== PAGAMENTO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"valor":100.0,"data":"2024-06-01"}' | jq
echo -e "\n"
echo "========== PAGAMENTO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== PAGAMENTO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"valor":120.0,"data":"2024-06-15"}' | jq
echo -e "\n"
echo "========== PAGAMENTO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== PAGAMENTO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== PAGAMENTO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== PEDIDO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=pedido"

echo "========== PEDIDO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"usuario_idusuario":1,"data_pedido":"2025-04-20 15:00:00","status":"processando","pagamento_id":1}' | jq
echo -e "\n"

echo "========== PEDIDO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== PEDIDO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_idusuario":1,"data_pedido":"2025-04-22 16:30:00","status":"enviado","pagamento_id":1}' | jq
echo -e "\n"

echo "========== PEDIDO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

echo "========== PEDIDO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"

echo "========== PEDIDO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== PLANO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=plano"
echo "========== PLANO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"Mensal","valor":100.0,"descricao":"Plano mensal"}' | jq
echo -e "\n"
echo "========== PLANO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== PLANO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"Trimestral","valor":250.0,"descricao":"Plano trimestral"}' | jq
echo -e "\n"
echo "========== PLANO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== PLANO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== PLANO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== PRODUTO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=produto"
echo "========== PRODUTO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"Whey Protein","preco":120.0,"categoria_id":1}' | jq
echo -e "\n"
echo "========== PRODUTO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== PRODUTO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"nome":"Creatina","preco":90.0,"categoria_id":1}' | jq
echo -e "\n"
echo "========== PRODUTO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== PRODUTO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== PRODUTO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== PROFESSOR_ALUNO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=professor_aluno"
echo "========== PROFESSOR_ALUNO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"professor_id":1,"aluno_id":1}' | jq
echo -e "\n"
echo "========== PROFESSOR_ALUNO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== PROFESSOR_ALUNO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"professor_id":1,"aluno_id":2}' | jq
echo -e "\n"
echo "========== PROFESSOR_ALUNO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== PROFESSOR_ALUNO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== PROFESSOR_ALUNO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== RECUPERACAO_SENHA ====================
API_URL="http://localhost:83/public/api/index.php?entidade=recuperacao_senha"
echo "========== RECUPERACAO_SENHA - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"token":"abc123","data_expiracao":"2024-07-01"}' | jq
echo -e "\n"
echo "========== RECUPERACAO_SENHA - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== RECUPERACAO_SENHA - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"token":"xyz789","data_expiracao":"2024-07-10"}' | jq
echo -e "\n"
echo "========== RECUPERACAO_SENHA - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== RECUPERACAO_SENHA - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== RECUPERACAO_SENHA - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== REFEICAO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=refeicao"
echo "========== REFEICAO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"dieta_id":1,"nome":"Café da manhã","horario":"08:00"}' | jq
echo -e "\n"
echo "========== REFEICAO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== REFEICAO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"dieta_id":1,"nome":"Almoço","horario":"12:00"}' | jq
echo -e "\n"
echo "========== REFEICAO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== REFEICAO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== REFEICAO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== TREINO_EXERCICIO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=treino_exercicio"
echo "========== TREINO_EXERCICIO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"treino_id":1,"exercicio_id":1,"series":3,"repeticoes":12}' | jq
echo -e "\n"
echo "========== TREINO_EXERCICIO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== TREINO_EXERCICIO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"treino_id":1,"exercicio_id":1,"series":4,"repeticoes":10}' | jq
echo -e "\n"
echo "========== TREINO_EXERCICIO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== TREINO_EXERCICIO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== TREINO_EXERCICIO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== TREINO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=treino"
echo "========== TREINO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"nome":"Treino A","descricao":"Treino de peito e tríceps"}' | jq
echo -e "\n"
echo "========== TREINO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== TREINO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{"id":1,"usuario_id":1,"nome":"Treino B","descricao":"Treino de costas e bíceps"}' | jq
echo -e "\n"
echo "========== TREINO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== TREINO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== TREINO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"

# ==================== USUARIO ====================
API_URL="http://localhost:83/public/api/index.php?entidade=usuario"
echo "========== USUARIO - CADASTRAR =========="
curl -s -X POST "$API_URL&acao=cadastrar" \
    -H "Content-Type: application/json" \
    -d '{
        "id":1,
        "nome":"Maria",
        "email":"maria@email.com",
        "senha":"123456",
        "cpf":"12345678900",
        "data_nascimento":"1990-01-01",
        "telefone":"11999999999",
        "tipo":"1",
        "numero_matricula":"20240001"
    }' | jq
echo -e "\n"
echo "========== USUARIO - LISTAR =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== USUARIO - EDITAR =========="
curl -s -X POST "$API_URL&acao=editar" \
    -H "Content-Type: application/json" \
    -d '{
        "id":1,
        "nome":"Maria Souza",
        "email":"maria@email.com",
        "senha":"654321",
        "cpf":"12345678900",
        "data_nascimento":"1990-01-01",
        "telefone":"11999999999",
        "tipo":"1",
        "numero_matricula":"20240001"
    }' | jq
echo -e "\n"
echo "========== USUARIO - LISTAR NOVAMENTE =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
echo "========== USUARIO - DELETAR =========="
curl -s -X POST "$API_URL&acao=deletar" \
    -H "Content-Type: application/json" \
    -d '{"id":1}' | jq
echo -e "\n"
echo "========== USUARIO - LISTAR FINAL =========="
curl -s "$API_URL&acao=listar" | jq
echo -e "\n"
