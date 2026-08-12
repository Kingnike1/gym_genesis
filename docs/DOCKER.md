# Docker e ambientes

A imagem de aplicação usa PHP 8.2, PDO MySQL, OPcache, Apache com `public/` como DocumentRoot e executa como `www-data` na porta 8080. Dependências de produção são instaladas em estágio separado do Composer.

## Desenvolvimento/homologação local

1. Crie `.env` a partir de `.env.example`.
2. Defina `DB_PASSWORD` e `DB_ROOT_PASSWORD` com valores locais fortes.
3. Execute `docker compose up --build -d`.
4. Execute migrations dentro do container da aplicação antes de usar o sistema.
5. Acesse `/health` para verificar disponibilidade.

Dados do MariaDB e uploads ficam em volumes nomeados. Segredos não são copiados para a imagem.
