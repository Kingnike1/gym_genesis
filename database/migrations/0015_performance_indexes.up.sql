CREATE INDEX idx_academia_usuario_usuario_ativo ON academia_usuario (usuario_id, ativo);
CREATE INDEX idx_aluno_academia_status_nome ON aluno (academia_id, status, nome);
CREATE INDEX idx_professor_academia_status_nome ON professor (academia_id, status, nome);
CREATE INDEX idx_ficha_treino_academia_aluno_status ON ficha_treino (academia_id, aluno_id, status);
CREATE INDEX idx_plano_alimentar_academia_aluno_status ON plano_alimentar (academia_id, aluno_id, status);
CREATE INDEX idx_avaliacao_fisica_aluno_data ON avaliacao_fisica_registro (academia_id, aluno_id, data_avaliacao);
CREATE INDEX idx_produto_academia_status_categoria ON produto (academia_id, status, categoria);
CREATE INDEX idx_pedido_comercial_academia_usuario_status ON pedido_comercial (academia_id, usuario_id, status);
CREATE INDEX idx_pagamento_comercial_academia_pedido_status ON pagamento_comercial (academia_id, pedido_id, status);
