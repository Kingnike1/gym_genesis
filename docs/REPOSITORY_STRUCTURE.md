# Estrutura do Repositório

Este documento registra a situação do código após a Stack 02 e evita que código legado seja confundido com a arquitetura em evolução.

## Código ativo e direção oficial

A direção arquitetural oficial permanece:

- `app/` — Controllers, Services, Repositories, Models, Middleware e Views;
- `routes/` — roteamento da aplicação;
- `public/index.php` — ponto de entrada da arquitetura nova;
- `public/css`, `public/js` e `public/img` — ativos estáticos;
- `db/` — esquema e dados iniciais atuais, até a adoção de migrations na Stack 07;
- `docs/` — documentação oficial;
- `tests/` — destino oficial dos testes automatizados e materiais de teste organizados;
- `composer.json` e `composer.lock` — definição reproduzível das dependências;
- `Dockerfile` e `docker-compose.yml` — ambiente atual, que será aprimorado na Stack 24.

## Código legado preservado temporariamente

Os diretórios abaixo continuam no repositório porque podem sustentar funcionalidades ainda não migradas:

- `controller/` — controllers procedurais antigos;
- `code/` — funções, scripts e testes manuais legados;
- `public/php/` — endpoints procedurais antigos;
- páginas PHP diretamente em `public/` — telas e fluxos anteriores;
- `template/` — protótipos e referências visuais;
- `checklist/` — ferramenta experimental antiga;
- `document/` — documentação histórica, diagramas e rascunhos anteriores;
- `err/` — página de erro antiga.

A presença desses diretórios não significa que sejam o padrão para código novo. Eles serão inventariados e migrados gradualmente na Stack 06. Nenhum novo módulo deve ser criado neles.

## Testes

O diretório oficial é `tests/`.

Os scripts em `code/tests/` são testes manuais legados, não uma suíte automatizada. Eles foram mantidos no local nesta stack porque vários dependem de caminhos relativos e de funções procedurais. A migração para PHPUnit, com correção desses acoplamentos, pertence à Stack 21.

Arquivos de imagem gerados pelos testes não devem ser versionados.

## Arquivos que não pertencem ao Git

Os seguintes itens são gerados localmente ou em tempo de execução e não devem ser versionados:

- `vendor/`;
- `.env` e variações locais;
- logs;
- uploads de usuários;
- dados persistidos do Docker;
- arquivos temporários;
- backups e cópias de edição;
- configurações pessoais de IDE;
- artefatos gerados por testes.

## Instalação após a limpeza

Depois de clonar o repositório:

```bash
cp .env.example .env
composer install
```

Os uploads e logs devem ser criados pelo ambiente de execução, não recuperados do Git.

## Regra de evolução

Antes de remover qualquer diretório legado, deve-se:

1. identificar os fluxos que ainda o utilizam;
2. substituir esses fluxos na arquitetura oficial;
3. validar o comportamento;
4. remover o código antigo no mesmo ciclo de migração.

Isso evita uma limpeza visual que quebre funcionalidades existentes.
