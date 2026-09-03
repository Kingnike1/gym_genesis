# Gym Genesis — Staging gratuito com Render + Aiven MySQL

Este documento descreve o ambiente público de homologação do Gym Genesis usando somente planos gratuitos para a aplicação e o banco.

## Arquitetura

- Aplicação: Render Web Service Free, `gym-genesis-staging`.
- Runtime: Docker com PHP 8.2 + Apache.
- Banco: Aiven for MySQL Free Tier.
- Conexão app → banco: conexão pública protegida por TLS.
- Branch de staging: `stack-30-monitoramento-producao`.
- Porta HTTP interna: `8080`.
- Health check: `GET /ready`.
- Migrations: executadas antes do Apache iniciar.
- HTTPS da aplicação: fornecido pelo Render.

## Custos

- Render Web Service Free: R$ 0 para homologação, sujeito aos limites do plano gratuito.
- Aiven MySQL Free Tier: R$ 0, sem cartão e sem limite de tempo enquanto o plano gratuito estiver disponível e o serviço permanecer dentro das regras do provedor.

O MySQL pago do Render foi removido do `render.yaml`. Criar o Blueprint atual não provisiona nenhum Private Service ou disco pago.

## 1. Criar o banco gratuito no Aiven

1. Crie uma conta no Aiven.
2. Abra **Services > Create service**.
3. Escolha **MySQL**.
4. Escolha o **Free tier**.
5. Dê um nome como `gym-genesis-staging`.
6. Aguarde o serviço ficar disponível.
7. Na tela **Overview / Connection information**, copie:
   - Host
   - Port
   - User
   - Password
   - Database (normalmente `defaultdb`)
8. Baixe o **CA Certificate** do serviço.

O plano gratuito do Aiven fornece um nó com 1 CPU, 1 GB de RAM, 1 GB de disco e backups, adequado para staging e homologação.

## 2. Criar o Blueprint no Render

1. No Render, escolha **New > Blueprint**.
2. Conecte `Kingnike1/gym_genesis`.
3. Se solicitado, selecione `stack-30-monitoramento-producao`.
4. O Render detectará `render.yaml`.
5. O Blueprint deve mostrar somente o serviço `gym-genesis-staging`, no plano `free`.

Durante a criação, o Render solicitará as variáveis marcadas com `sync: false`.

Preencha com os dados do Aiven:

- `DB_HOST` = Host do Aiven
- `DB_PORT` = Port do Aiven
- `DB_NAME` = Database do Aiven, por exemplo `defaultdb`
- `DB_USER` = User do Aiven, normalmente `avnadmin`
- `DB_PASSWORD` = Password do Aiven
- `DB_SSL_CA` = conteúdo completo do arquivo CA Certificate baixado do Aiven

Cole `DB_SSL_CA` incluindo as linhas `BEGIN CERTIFICATE` e `END CERTIFICATE`. O Render aceita valores de ambiente multilinha.

## SMTP

O Blueprint também solicita:

- `MAIL_HOST`
- `MAIL_USERNAME`
- `MAIL_PASSWORD`
- `MAIL_FROM_ADDRESS`

Se você ainda não for validar recuperação de senha ou envio de e-mail, configure depois no painel do Render antes desses testes específicos. Não coloque credenciais reais no GitHub.

## Inicialização

O serviço executa:

```sh
/bin/sh scripts/start-render.sh
```

O script:

1. grava temporariamente o CA do Aiven em `/tmp/aiven-ca.pem`;
2. ativa a verificação TLS no PDO MySQL;
3. tenta executar as migrations;
4. repete a tentativa se o banco ainda estiver indisponível;
5. inicia o Apache apenas quando as migrations terminam com sucesso.

Fluxo esperado:

```text
Aiven MySQL Free online
        ↓
Render inicia o container
        ↓
CA TLS é preparado
        ↓
conexão segura ao Aiven
        ↓
migrations 0001–0017
        ↓
Apache inicia na porta 8080
        ↓
GET /ready retorna 200
        ↓
URL pública pronta para homologação
```

## Verificações após o deploy

1. Abra `https://<servico>.onrender.com/health`.
   - Esperado: HTTP 200 e `{"status":"ok","service":"gym-genesis"}`.
2. Abra `https://<servico>.onrender.com/ready`.
   - Esperado: HTTP 200 e `{"status":"ready","database":"ok"}`.
3. Abra `/login`.
4. Execute `tests/MANUAL_HOMOLOGATION.md` usando somente dados fictícios.

## Limitações do staging gratuito

O Render Free pode dormir após um período sem tráfego e levar cerca de um minuto para voltar. O filesystem do Web Service é efêmero, portanto uploads locais podem desaparecer em reinícios ou redeploys. O Aiven Free possui 1 GB de armazenamento e não possui SLA de produção.

Essas limitações são aceitáveis para homologação, mas não devem ser tratadas como arquitetura final de produção.

## Segurança

- `APP_ENV=staging`.
- `APP_DEBUG=false`.
- `APP_SECRET` é gerado pelo Render.
- credenciais do banco ficam apenas nas variáveis secretas do Render;
- conexão com Aiven usa TLS e CA do serviço;
- não use dados pessoais reais no staging;
- não compartilhe senha do Aiven em issues, documentação ou commits.

## Antes de produção

Antes de promover para produção:

- completar o checklist manual;
- testar backup e restore;
- testar indisponibilidade real do banco em `/ready`;
- testar rollback;
- definir armazenamento persistente para uploads;
- revisar limites e escolher infraestrutura de produção adequada.
