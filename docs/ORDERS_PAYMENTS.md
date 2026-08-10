# Pedidos e pagamentos

O backend é a fonte de verdade para preço, subtotal, desconto, frete, total e estoque. `pedido_comercial` e `pedido_item_registro` preservam snapshots de preço. A criação do pedido bloqueia os produtos com `FOR UPDATE`, valida estoque e baixa o saldo na mesma transação.

`pagamento` usa chave de idempotência e identificador externo do gateway. Dados de cartão não devem ser armazenados. Webhooks futuros devem validar assinatura antes de chamar `PaymentRepository::applyGatewayResult`.
