# Recuperação de senha

Fluxo oficial:

1. usuário solicita recuperação por e-mail;
2. resposta HTTP é sempre genérica para evitar enumeração de contas;
3. token aleatório de 32 bytes é gerado;
4. somente `sha256(token)` é armazenado;
5. token expira em 30 minutos e invalida tokens anteriores;
6. redefinição exige confirmação da nova senha;
7. após uso, todos os tokens pendentes são invalidados;
8. `session_version` é incrementado para invalidar sessões existentes.

As mensagens são enviadas via PHPMailer usando `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS` e `MAIL_FROM_NAME`.
