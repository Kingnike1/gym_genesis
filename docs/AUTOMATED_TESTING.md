# Testes automatizados

A suíte oficial usa PHPUnit. Testes unitários ficam em `tests/Unit`; testes que dependem de infraestrutura ficam em `tests/Integration`.

## Comandos

- `composer test`
- `composer test:unit`
- `composer test:integration`

Use sempre um banco exclusivo de testes, preferencialmente `gym_genesis_test`, configurado por `.env.testing`. Nunca execute a suíte contra produção.

A cobertura deve crescer priorizando autenticação, autorização, multiacademia, migrations, estoque, pedidos, pagamentos, recuperação de senha, uploads e regras de negócio críticas.
