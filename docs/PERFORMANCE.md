# Performance

A estratégia é medir antes de cachear.

## Padrões
- listagens novas devem preferir `BaseRepository::paginate()` com máximo de 100 itens por página;
- consultas frequentes por academia/status/aluno possuem índices dedicados na migration `0015_performance_indexes`;
- requisições acima de `SLOW_REQUEST_MS` (750 ms por padrão) são registradas como warning;
- `SELECT *` e carregamentos sem limite devem ser removidos gradualmente dos fluxos de listagem;
- cache só deve ser introduzido após identificação de leitura repetida e estável.

Antes do merge, compare `EXPLAIN` das consultas críticas antes/depois dos índices e valide que escrita não sofreu regressão relevante.
