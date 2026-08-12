# Gym Genesis — Deploy de Staging no Render

Este documento descreve o ambiente público de homologação do Gym Genesis. O staging é destinado exclusivamente a dados fictícios e testes manuais.

## Arquitetura

O `render.yaml` provisiona os dois serviços no mesmo Blueprint:

- Aplicação: Render Web Service (Docker), `gym-genesis-staging`.
- Banco: Render Private Service com MySQL 8, `gym-genesis-mysql-staging`.
- Persistência do banco: Render Disk de 10 GB montado em `/var/lib/mysql`.
- Comunicação app → banco: rede privada do Render, sem exposição pública do MySQL.
- Runtime da aplicação: PHP 8.2 + Apache.
- Branch atual de staging: `stack-30-monitoramento-producao`.
- Porta HTTP interna: `8080`.
- Health check: `GET /ready`.
- Migrations: executadas antes do Apache iniciar.
- HTTPS: fornecido pelo Render na URL pública da aplicação.

## O que é automático

Ao criar o Blueprint, o Render cria:

1. o Private Service MySQL 8;
2. o disco persistente do banco;
3. o database `gym_genesis_staging`;
4. o usuário `gym_genesis`;
5. uma senha aleatória para o usuário do banco;
6. uma senha aleatória para o root do MySQL;
7. o Web Service do Gym Genesis;
8. `DB_HOST` apontando automaticamente para o hostname privado do MySQL;
9. `DB_PASSWORD` recebendo automaticamente a mesma senha gerada no serviço MySQL;
10. `APP_SECRET` aleatório para o staging.

As credenciais do banco não precisam ser copiadas manualmente nem ficam armazenadas no GitHub.

## O que você ainda precisa informar no Render

O Blueprint pede apenas os valores SMTP marcados como `sync: false`:

- `MAIL_HOST`
- `MAIL_USERNAME`
- `MAIL_PASSWORD`
- `MAIL_FROM_ADDRESS`

Se você ainda não for testar recuperação de senha/e-mail, use uma conta SMTP exclusiva de staging quando chegar nessa etapa. Não use credenciais pessoais em documentação ou commits.

## Provisionamento

1. No Render, escolha **New > Blueprint**.
2. Conecte o repositório `Kingnike1/gym_genesis`.
3. Selecione a branch `stack-30-monitoramento-producao` caso o Render solicite a branch do Blueprint.
4. O Render detectará o `render.yaml` na raiz.
5. Confirme a criação de:
   - `gym-genesis-mysql-staging` — Private Service / MySQL 8 / Starter / disco 10 GB;
   - `gym-genesis-staging` — Web Service / Docker.
6. Preencha somente os segredos SMTP solicitados.
7. Confirme a aplicação do Blueprint.

## Custo e persistência

Private Services não possuem instância gratuita no Render e discos persistentes exigem serviço pago. Por isso o MySQL usa o plano `starter` e um disco de 10 GB. O Web Service da aplicação permanece em `free` no staging para reduzir custo.

O disco em `/var/lib/mysql` é obrigatório para preservar o banco entre reinícios e novos deploys.

## Inicialização da aplicação

O Web Service usa:

```sh
/bin/sh scripts/start-render.sh
```

O script tenta aplicar as migrations. Se o MySQL ainda estiver inicializando, ele aguarda e tenta novamente. Somente depois de uma execução bem-sucedida das migrations o Apache é iniciado.

Fluxo esperado:

```text
MySQL privado inicia
        ↓
disco /var/lib/mysql disponível
        ↓
aplicação encontra DB_HOST pela rede privada
        ↓
migrations 0001–0017
        ↓
Apache inicia na porta 8080
        ↓
GET /ready retorna HTTP 200
        ↓
URL pública pronta para homologação
```

## Verificações após o deploy

1. Abra `https://<servico>.onrender.com/health`.
   - Esperado: HTTP 200 e `{"status":"ok","service":"gym-genesis"}`.
2. Abra `https://<servico>.onrender.com/ready`.
   - Esperado: HTTP 200 e `{"status":"ready","database":"ok"}`.
3. Abra `/login` e valide a aplicação pelo navegador.
4. Execute `tests/MANUAL_HOMOLOGATION.md` usando somente dados fictícios.

## Observação sobre uploads

O banco possui persistência por disco. O Web Service da aplicação está no plano gratuito e não possui Render Disk; portanto arquivos enviados para o filesystem local do app podem não sobreviver a redeploys. Isso é aceitável para a primeira homologação funcional, mas o teste definitivo de persistência de uploads deve usar armazenamento persistente antes da produção.

## Segurança de staging

- `APP_ENV=staging`.
- `APP_DEBUG=false`.
- `APP_SECRET` é gerado pelo Render.
- MySQL fica apenas na rede privada.
- Senhas do banco são geradas pelo Render e compartilhadas entre serviços sem commit.
- Não usar dados pessoais reais durante homologação.
- Não compartilhar credenciais administrativas em documentação ou issues.

## Deploy automático

A aplicação utiliza `autoDeployTrigger: checksPass`. O Render só deve publicar novos commits da branch de staging quando os checks associados ao commit estiverem aprovados.

## Antes de produção

O staging não substitui validação de produção. Antes de promover para `main`, execute:

- checklist manual completo;
- backup lógico com `mysqldump` e restore em staging;
- teste de `/health` e `/ready` com indisponibilidade real do banco;
- rollback do aplicativo conforme o runbook;
- validação das configurações SMTP e URLs externas;
- definição de armazenamento persistente/objeto para uploads;
- confirmação de que nenhum dado real foi utilizado no staging.
