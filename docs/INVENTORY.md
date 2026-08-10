# Loja e estoque

`produto` permanece o catálogo oficial. A Stack 16 adiciona SKU, custo, estoque mínimo, status e a tabela `estoque_movimentacao`.

Toda entrada, saída ou ajuste relevante deve gerar uma movimentação com saldo anterior e posterior. Saídas usam `SELECT ... FOR UPDATE` dentro de transação e nunca podem produzir estoque negativo.
