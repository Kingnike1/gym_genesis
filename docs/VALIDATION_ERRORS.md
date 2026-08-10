# Validação e tratamento de erros

A Stack 20 define um fluxo comum para falhas HTTP e validação.

- `Validator` concentra validações reutilizáveis e lança `ValidationException` (422).
- `NotFoundException` representa 404.
- `AuthorizationException` representa 403.
- `MethodNotAllowedException` representa 405 e inclui o cabeçalho `Allow`.
- `InvalidArgumentException` é traduzida para 422.
- `DomainException` é traduzida para 409.
- erros inesperados retornam 500 sem detalhes internos em produção.

O `ErrorHandler` gera um `request_id` para correlacionar a mensagem exibida com o log. Respostas JSON seguem `{ "error": { "status", "message", "request_id" } }`; erros de validação podem incluir `fields`.

O bootstrap mantém `display_errors=0` mesmo em debug; detalhes de exceção são renderizados somente pelo handler controlado.
