# Planos e matrículas

`plano_comercial` define a oferta atual da academia. `matricula` representa o contrato do aluno e mantém `valor_contratado`, datas, unidade e status. Mudanças de status são registradas em `matricula_historico`.

Status suportados: ativa, suspensa, congelada, cancelada, encerrada e inadimplente. Alterar um plano não deve reescrever matrículas já existentes.
