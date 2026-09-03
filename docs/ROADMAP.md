# Gym Genesis — Roadmap de Continuidade

Este roadmap parte da auditoria de 2026-09-03. Ele não repete as 30 stacks históricas; organiza somente o trabalho que permanece necessário para transformar a branch de desenvolvimento acumulada em uma linha de integração confiável, homologada e pronta para release.

## Regras deste roadmap

- Cada Sprint deve ser pequena, testável e reversível.
- Não misturar feature nova com saneamento, integração ou correção de infraestrutura.
- Toda alteração deve preservar multiacademia, autenticação, autorização e migrations.
- Nunca marcar teste como aprovado sem execução registrada.
- Antes de iniciar uma Sprint, ler `CLAUDE.md`, `docs/PROJECT_STATUS.md`, `docs/ROADMAP.md` e `docs/ARCHITECTURE.md`.

## Sprint 01 — Segurança e saneamento da linha atual — P0

**Objetivo:** tornar a branch ativa segura e coerente com as intenções das antigas Stacks 01 e 02.

**Motivo:** a branch `stack-30-monitoramento-producao` ainda rastreia `.env`, contém `.vscode/launch.json`, mantém `.gitignore` inconsistente e não incorpora os PRs independentes das Stacks 01/02.

**Tarefas:**
- comparar Stack 01 e Stack 02 com a branch ativa;
- aplicar somente as mudanças ainda necessárias, resolvendo conflitos conscientemente;
- remover `.env` do rastreamento sem expor seu conteúdo;
- revisar histórico e decidir se credenciais antigas precisam de rotação;
- corrigir `.gitignore` sem ignorar `composer.json`/`composer.lock`;
- retirar configurações pessoais/artefatos claramente gerados que não são necessários;
- confirmar que Composer e Docker continuam reproduzíveis.

**Dependências:** nenhuma Sprint nova; depende apenas da branch ativa auditada.

**Áreas prováveis:** `.env`, `.env.example`, `.gitignore`, `.dockerignore`, `.vscode/`, documentação de segurança, arquivos afetados pelas branches `stack-01-*` e `stack-02-*`.

**Testes necessários:**
- `git ls-files` não deve listar `.env`;
- `composer validate --strict`;
- `composer install` a partir de clone limpo;
- `composer audit --locked`;
- Docker build;
- inspeção de diff para garantir ausência de segredos.

**Critérios de aceite:** nenhum segredo/configuração local rastreado; Composer files versionados corretamente; CI verde; nenhuma alteração funcional intencional.

**Riscos:** conflitos com mudanças acumuladas das Stacks 03–30; remoção indevida de arquivo legado ainda consumido.

## Sprint 02 — Runtime Docker e smoke HTTP — P0

**Objetivo:** garantir que a imagem que a CI constrói realmente inicia e responde HTTP.

**Motivo:** o Dockerfile atual escuta `10000`, enquanto os Compose publicam/checavam `8080`; a CI apenas faz build.

**Tarefas:**
- definir uma política única de porta para Docker local, produção e Render;
- alinhar Dockerfile e Compose sem alterar comportamento de aplicação;
- adicionar smoke test de container em CI ou script de validação;
- validar `/ping.html`, `/health` e `/ready` em container real;
- confirmar graceful startup/shutdown.

**Dependências:** Sprint 01.

**Áreas prováveis:** `Dockerfile`, `docker-compose.yml`, `docker-compose.production.yml`, `render.yaml`, `scripts/start-render.sh`, CI.

**Testes necessários:** Docker build; Docker run/Compose up; HTTP 200 de `/ping.html` e `/health`; `/ready` 200 com DB e 503 sem DB.

**Critérios de aceite:** clone limpo sobe de forma reproduzível; CI testa mais que build; documentação de porta coincide com código.

**Riscos:** particularidades de `PORT` no Render e diferença entre ambiente local/cloud.

## Sprint 03 — Consolidar Git e branch de integração — P1

**Objetivo:** fazer `develop` representar o estado real e encerrar a cadeia histórica de PRs de forma auditável.

**Motivo:** `main`/`develop` estão 359 commits atrás e os PRs 1–30 continuam draft/abertos.

**Tarefas:**
- revisar ancestry e conteúdo efetivo das branches;
- escolher estratégia de integração (merge sequencial, consolidação controlada ou nova PR agregadora);
- atualizar `develop` somente após CI e revisão;
- fechar/retargetar PRs históricos de forma coerente;
- definir branch protection e checks obrigatórios;
- manter `main` sem promoção até homologação.

**Dependências:** Sprints 01 e 02.

**Áreas prováveis:** Git/GitHub, branch rules, PR metadata, docs de branching.

**Testes necessários:** CI no commit exato que entrará em `develop`; compare de refs; verificação de migrations e Docker smoke.

**Critérios de aceite:** `develop` contém a versão validada; nenhum PR histórico cria ambiguidade; branch rules protegem integração.

**Riscos:** merges duplicados e conflitos por cadeia empilhada.

## Sprint 04 — Homologação de segurança e multiacademia — P1

**Objetivo:** provar manualmente e por integração os limites de acesso mais críticos.

**Motivo:** código e smoke tests existem, mas cobertura automatizada é pequena e a homologação manual consolidada não está concluída.

**Tarefas:**
- executar testes AUTH/TENANT do `tests/MANUAL_HOMOLOGATION.md`;
- testar IDOR, troca de academia e papéis cruzados;
- testar sessão, timeout, logout, reset de senha e rate limit;
- transformar os casos estáveis de maior risco em testes automatizados quando viável;
- registrar todos os resultados.

**Dependências:** `develop` consolidada e ambiente executável.

**Áreas prováveis:** Middleware, Security, Tenancy, AuthController, Repositories tenant-aware, tests.

**Testes necessários:** duas academias; usuários com papéis diferentes; IDs cruzados; sessão; CSRF; reset.

**Critérios de aceite:** nenhum acesso cross-tenant; nenhuma mudança de estado sem autorização; resultados registrados.

**Riscos:** dados de teste mal preparados gerarem falso positivo.

## Sprint 05 — Homologação dos módulos web existentes — P1

**Objetivo:** provar quais fluxos modernos realmente estão completos para Admin, Professor e Aluno.

**Motivo:** muitos domínios possuem camada de persistência, mas a superfície HTTP não é igualmente completa.

**Tarefas:**
- executar checklist de usuários, planos, produtos, pedidos, treinos, dietas, avaliações e perfil;
- registrar rotas inexistentes ou telas sem fluxo completo;
- separar bug de “backend disponível, UI ainda ausente”;
- não criar novas telas nesta Sprint; apenas mapear e corrigir o estado documental.

**Dependências:** Sprint 04.

**Áreas prováveis:** `routes/web.php`, Controllers, Services, Views, `tests/MANUAL_HOMOLOGATION.md`.

**Testes necessários:** CRUD e permissões por módulo; validações e erros 403/404/405/409/422.

**Critérios de aceite:** matriz objetiva de módulos completos/parciais; nenhuma funcionalidade declarada completa sem fluxo comprovado.

**Riscos:** legado mascarar ausência de rota moderna.

## Sprint 06 — Fechar superfícies parciais priorizadas — P1

**Objetivo:** após a Sprint 05, fechar apenas as lacunas necessárias para a primeira versão homologável.

**Motivo:** existem estruturas sem Controller/rota completa comprovada: matrículas, gestão profissional específica, arquivos, privacidade, tokens/API e pagamentos externos.

**Tarefas:** definir quais desses fluxos pertencem realmente à primeira versão; criar Sprints menores por módulo escolhido; remover do escopo de release o que não for necessário.

**Dependências:** decisão de produto após Sprint 05.

**Áreas prováveis:** Controllers, Services, Repositories, Views, routes, tests.

**Testes necessários:** específicos de cada fluxo escolhido, sempre com tenancy e autorização.

**Critérios de aceite:** escopo v1 explícito; nenhum módulo “meio exposto” declarado concluído.

**Riscos:** transformar esta Sprint em pacote gigante. Se mais de um domínio exigir implementação, dividir em Sprints independentes.

## Sprint 07 — Expandir testes automatizados de alto risco — P1

**Objetivo:** reduzir dependência de homologação manual repetitiva.

**Motivo:** a suíte identificada tem cobertura pequena frente ao tamanho do domínio.

**Tarefas:**
- testes HTTP do Router/middleware;
- integração de autenticação e tenancy;
- testes de transação de estoque/pedidos;
- testes de reset de senha;
- testes de API scopes;
- smoke de container.

**Dependências:** contratos dos módulos homologados estáveis.

**Áreas prováveis:** `tests/Unit`, `tests/Integration`, possível diretório HTTP, CI.

**Testes necessários:** a própria Sprint é de testes; medir estabilidade/repetibilidade.

**Critérios de aceite:** principais riscos P0/P1 executados automaticamente em PR.

**Riscos:** testes excessivamente acoplados ao schema/fixtures.

## Sprint 08 — Inventário e aposentadoria segura do legado — P2

**Objetivo:** reduzir duplicidade entre `app/` e código procedural.

**Motivo:** `controller/`, `code/`, páginas procedurais e testes ad hoc continuam grandes e ambíguos.

**Tarefas:**
- inventariar cada endpoint/arquivo legado e consumidor;
- marcar: usado, substituído, desconhecido;
- remover somente itens comprovadamente substituídos;
- mover testes ad hoc relevantes para `tests/`;
- impedir novas features no legado.

**Dependências:** homologação dos módulos modernos.

**Áreas prováveis:** `controller/`, `code/`, `public/php/`, `template/`, `checklist/`, `document/`, `err/`.

**Testes necessários:** regressão por fluxo antes/depois de cada remoção.

**Critérios de aceite:** nenhuma remoção sem substituição comprovada; redução mensurável de duplicidade.

**Riscos:** chamadas relativas/links antigos não detectados.

## Sprint 09 — Staging completo, persistência e operação — P1/P2

**Objetivo:** transformar o staging em ambiente confiável de homologação recorrente.

**Motivo:** Render/Aiven foi preparado, mas a execução pós-fix não está documentada como aprovada e uploads do Render Free são efêmeros.

**Tarefas:**
- validar Render/Aiven após deploy do commit integrado;
- configurar SMTP de staging;
- validar `/health` e `/ready` em falha real de DB;
- testar backup + restore;
- testar rollback;
- decidir storage persistente para uploads antes de produção;
- remover ou desativar diagnósticos temporários quando não forem mais necessários.

**Dependências:** runtime Docker e `develop` consolidados.

**Áreas prováveis:** Render, Aiven, scripts, storage, runbooks.

**Testes necessários:** health/readiness, backup/restore, redeploy, restart, uploads, rollback.

**Critérios de aceite:** staging reproduzível e checklist operacional aprovado.

**Riscos:** limites dos planos gratuitos e filesystem efêmero.

## Sprint 10 — Release candidate — P1

**Objetivo:** preparar uma versão candidata sem adicionar funcionalidades.

**Motivo:** separar estabilização de desenvolvimento contínuo.

**Tarefas:**
- criar `release/x.y.z` a partir de `develop` validada;
- executar CI completa e checklist manual selecionado;
- atualizar changelog/versionamento/README;
- validar migração em cópia de dados compatível;
- validar plano de rollback;
- corrigir somente bugs de release.

**Dependências:** Sprints críticas anteriores concluídas e escopo v1 decidido.

**Áreas prováveis:** Git, docs, VERSION, CHANGELOG, migrations, infra.

**Testes necessários:** regressão completa, smoke HTTP, segurança, tenancy, operação.

**Critérios de aceite:** release candidate aprovada para promoção; `main` continua protegida até aprovação explícita.

**Riscos:** introduzir feature durante estabilização.

## Backlog P3 após primeira release

- refinamento visual e acessibilidade avançada;
- expansão da API;
- integrações de catraca/gateway conforme clientes reais;
- observabilidade externa e alertas;
- cache distribuído apenas se métricas justificarem;
- otimizações de UX guiadas por uso real.