# Logs e auditoria

O Gym Genesis usa PSR-3/Monolog para logs técnicos e mantém auditoria de negócio separada no banco.

## Regras

- logs da aplicação vão para `php://stderr` por padrão, ideal para Docker;
- cada requisição recebe `request_id` para correlação;
- quando disponíveis, `user_id` e `academy_id` são adicionados como contexto;
- senhas, tokens, cookies, credenciais SMTP e dados de cartão nunca devem ser registrados;
- ações de negócio sensíveis continuam usando a trilha `auditoria_academia`.

Variáveis: `LOG_LEVEL` e `LOG_STREAM`.
