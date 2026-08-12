# Qualidade de código

O código moderno do Gym Genesis segue PSR-12 e deve passar por PHPStan antes de integração.

## Comandos

- `composer lint` — verifica PSR-12.
- `composer lint:fix` — corrige automaticamente o que for seguro.
- `composer analyse` — análise estática.
- `composer check` — lint + análise + testes.

O legado não deve ser formatado em massa. Arquivos são corrigidos quando entram no fluxo moderno, reduzindo risco e evitando PRs gigantes sem mudança funcional.
