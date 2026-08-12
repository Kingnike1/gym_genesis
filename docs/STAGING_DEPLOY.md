# Gym Genesis — Deploy de Staging no Render

Este documento descreve o ambiente público de homologação do Gym Genesis. O staging é destinado exclusivamente a dados fictícios e testes manuais.

## Arquitetura

- Aplicação: Render Web Service (Docker)
- Runtime: PHP 8.2 + Apache
- Branch atual de staging: `stack-30-monitoramento-producao`
- Porta HTTP interna: `8080`
- Health check: `GET /ready`
- Banco: MySQL/MariaDB externo e exclusivo de staging
- Migrations: executadas no início do container antes do Apache
- HTTPS: fornecido pelo Render na URL pública

## Provisionamento

1. No Render, crie um novo Blueprint a partir do repositório `Kingnike1/gym_genesis`.
2. O Render detectará o arquivo `render.yaml` na raiz.
3. Confirme a criação do serviço `gym-genesis-staging`.
4. Preencha os segredos solicitados pelo Blueprint:
   - `DB_HOST`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASSWORD`
   - `MAIL_HOST`
   - `MAIL_USERNAME`
   - `MAIL_PASSWORD`
   - `MAIL_FROM_ADDRESS`
5. Use um banco MySQL/MariaDB vazio e exclusivo para homologação.
6. Inicie o deploy.

## Requisitos do banco

- MySQL 8+ ou MariaDB 11.x recomendado.
- Porta padrão `3306` (ajuste `DB_PORT` no Render caso necessário).
- O host precisa ser acessível pelo serviço Render.
- Não reutilize banco de produção.

## Inicialização

O comando de inicialização configurado no Blueprint é:

```sh
php scripts/migrate.php migrate && exec apache2-foreground
```

Assim, o container só inicia o Apache se todas as migrations forem aplicadas com sucesso.

## Verificações após o deploy

1. Abra `https://<servico>.onrender.com/health`.
   - Esperado: HTTP 200 e `{"status":"ok","service":"gym-genesis"}`.
2. Abra `https://<servico>.onrender.com/ready`.
   - Esperado: HTTP 200 e `{"status":"ready","database":"ok"}`.
3. Abra `/login` e valide a aplicação pelo navegador.
4. Execute o checklist `tests/MANUAL_HOMOLOGATION.md` usando somente dados fictícios.

## Segurança de staging

- `APP_ENV=staging`
- `APP_DEBUG=false`
- `APP_SECRET` é gerado pelo Render.
- Credenciais não ficam versionadas no GitHub.
- Não usar dados pessoais reais durante homologação.
- Não compartilhar credenciais administrativas em documentação ou issues.

## Deploy automático

O Blueprint utiliza `autoDeployTrigger: checksPass`. O Render só deve promover um commit quando os checks associados ao commit estiverem aprovados.

## Antes de produção

O staging não substitui validação de produção. Antes de promover para `main`, execute:

- checklist manual completo;
- backup + restore em staging;
- teste de `/health` e `/ready` com indisponibilidade real do banco;
- rollback do aplicativo conforme o runbook;
- validação das configurações SMTP e URLs externas;
- confirmação de que nenhum dado real foi utilizado no staging.
