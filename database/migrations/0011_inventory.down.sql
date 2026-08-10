DROP TABLE IF EXISTS estoque_movimentacao;
DROP INDEX uk_produto_sku_academia ON produto;
ALTER TABLE produto
    DROP COLUMN status,
    DROP COLUMN estoque_minimo,
    DROP COLUMN custo,
    DROP COLUMN sku;
