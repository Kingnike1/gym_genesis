# Gym Genesis — Checklist Mestre de Homologação Manual

> Fonte: arquivos `tests/stack-08/MANUAL.md` até `tests/stack-30/MANUAL.md` encontrados na branch acumulada `stack-30-monitoramento-producao`.
>
> As Stacks 01–07 não possuem `MANUAL.md` nessa cadeia acumulada; por isso não foram inventados testes para elas.

## Como usar

Para cada teste, preencha `Resultado obtido` e marque um status final:

- [ ] PASSOU
- [ ] FALHOU
- [ ] BLOQUEADO
- [x] NÃO EXECUTADO

## Resumo

- Total de testes manuais consolidados: **53**
- Executados: **0**
- Pendentes: **53**

---

# 1. Multiacademia e identidade

## TENANT-001 — Migração de dados legados para academia padrão
**Origem:** Stack 08  
**Prioridade:** Crítica  
**Objetivo:** Confirmar que registros existentes recebem `academia_id` sem perda de dados.  
**Pré-requisitos:** Cópia descartável do banco legado.  
**Como executar:** 1. Restaurar a cópia do banco legado. 2. Executar `composer migrate`. 3. Conferir `academias`, `unidades`, `academia_usuario` e registros operacionais.  
**Resultado esperado:** Uma academia/unidade padrão é criada, usuários e registros antigos ficam vinculados e nenhuma informação existente é perdida.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## TENANT-002 — Isolamento entre duas academias
**Origem:** Stack 08  
**Prioridade:** Crítica  
**Objetivo:** Impedir que uma academia visualize registros de outra.  
**Pré-requisitos:** Duas academias com usuários e registros de teste.  
**Como executar:** Criar registros equivalentes em ambas e alternar o contexto ao acessar listagens e URLs de recursos.  
**Resultado esperado:** Cada contexto retorna somente registros da academia ativa; IDs cruzados não são acessíveis.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## TENANT-003 — Seleção de academia sem vínculo
**Origem:** Stack 08  
**Prioridade:** Crítica  
**Objetivo:** Bloquear troca de contexto para academia não autorizada.  
**Pré-requisitos:** Usuário autenticado e academia sem vínculo.  
**Como executar:** Enviar `POST /academy/select` com CSRF válido e ID de academia sem vínculo com o usuário.  
**Resultado esperado:** Requisição rejeitada; sessão permanece na academia anterior.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## ROLE-001 — Usuário com papéis diferentes em duas academias
**Origem:** Stack 09  
**Prioridade:** Crítica  
**Objetivo:** Garantir que a mesma conta possa ser Administrador em uma academia e Aluno em outra.  
**Pré-requisitos:** Duas academias e um usuário vinculado às duas.  
**Como executar:** Definir Admin na Academia A e Aluno na B; acessar `/admin/dashboard` em A; trocar para B e tentar `/admin/dashboard` e `/student/dashboard`.  
**Resultado esperado:** Em A, painel administrativo permitido; em B, `/admin/dashboard` retorna 403 e `/student/dashboard` é permitido.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## ROLE-002 — Desativação somente do vínculo da academia
**Origem:** Stack 09  
**Prioridade:** Alta  
**Objetivo:** Confirmar que desativar vínculo em uma academia não inutiliza a conta em outra.  
**Pré-requisitos:** Conta vinculada a duas academias.  
**Como executar:** Desativar o vínculo na Academia A e depois trocar para a Academia B.  
**Resultado esperado:** Acesso à Academia A bloqueado; Academia B continua acessível.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## AUTH-001 — Último login
**Origem:** Stack 09  
**Prioridade:** Média  
**Objetivo:** Confirmar atualização de `usuario.last_login_at`.  
**Pré-requisitos:** Usuário de teste ativo.  
**Como executar:** Consultar `last_login_at`, realizar login válido e consultar novamente.  
**Resultado esperado:** Campo atualizado para o momento do login.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## AUTH-002 — Hash de senha não aparece nas listagens
**Origem:** Stack 09  
**Prioridade:** Crítica  
**Objetivo:** Garantir que views administrativas não recebam hash de senha.  
**Pré-requisitos:** Acesso administrativo.  
**Como executar:** Inspecionar dados da listagem e edição de usuários.  
**Resultado esperado:** Nenhum campo `senha` ou hash é entregue às views.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## DB-001 — Migração e rollback da Stack 09
**Origem:** Stack 09  
**Prioridade:** Alta  
**Objetivo:** Validar `0004_user_roles_status`.  
**Pré-requisitos:** Banco descartável.  
**Como executar:** `composer migrate` → conferir colunas → `composer migrate:rollback` → conferir remoção → `composer migrate`.  
**Resultado esperado:** Migração, rollback e nova migração finalizam sem erro.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

---

# 2. Alunos e professores

## STUDENT-001 — Cadastro de aluno
**Origem:** Stack 10  
**Prioridade:** Alta  
**Objetivo:** Criar perfil de aluno com matrícula e dados básicos.  
**Pré-requisitos:** Usuário com papel Aluno na academia ativa.  
**Como executar:** Cadastrar perfil com matrícula única.  
**Resultado esperado:** Registro criado com `academia_id`, `unidade_id` e `usuario_id` corretos.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## STUDENT-002 — Suspensão sem exclusão
**Origem:** Stack 10  
**Prioridade:** Alta  
**Objetivo:** Suspender aluno preservando histórico.  
**Como executar:** Alterar status de ativo para `suspenso` e consultar novamente.  
**Resultado esperado:** Registro continua existindo com status `suspenso`.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## STUDENT-003 — Isolamento visual entre academias
**Origem:** Stack 10  
**Prioridade:** Crítica  
**Objetivo:** Impedir mistura de alunos entre academias.  
**Como executar:** Cadastrar alunos em duas academias, alternar contexto e consultar a lista.  
**Resultado esperado:** Cada academia mostra somente seus próprios alunos.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## STUDENT-004 — Contato de emergência e observações
**Origem:** Stack 10  
**Prioridade:** Média  
**Objetivo:** Validar persistência de dados complementares.  
**Como executar:** Preencher contato de emergência, objetivo e observações; salvar e reabrir.  
**Resultado esperado:** Valores persistem corretamente.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## PROFESSOR-001 — Cadastro profissional
**Origem:** Stack 11  
**Prioridade:** Alta  
**Objetivo:** Criar perfil com nome, CREF, telefone e bio.  
**Como executar:** Criar usuário Professor, cadastrar perfil e reabrir os dados.  
**Resultado esperado:** Dados persistidos na academia correta e CREF normalizado.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## PROFESSOR-002 — Especialidades e unidades
**Origem:** Stack 11  
**Prioridade:** Alta  
**Objetivo:** Validar múltiplas unidades e especialidades.  
**Como executar:** Associar duas unidades e duas especialidades, salvar e consultar.  
**Resultado esperado:** Vínculos corretos, sem duplicação.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## PROFESSOR-003 — Vincular e desvincular aluno
**Origem:** Stack 11  
**Prioridade:** Alta  
**Objetivo:** Validar ciclo professor/aluno.  
**Como executar:** Vincular aluno, consultar `students()`, desvincular e consultar novamente.  
**Resultado esperado:** Aluno aparece com vínculo ativo e desaparece após desativação.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## PROFESSOR-004 — Isolamento entre academias
**Origem:** Stack 11  
**Prioridade:** Crítica  
**Objetivo:** Impedir professor de visualizar alunos de outra academia.  
**Como executar:** Criar dados em duas academias e alternar contexto.  
**Resultado esperado:** Nenhuma listagem cruza tenants.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

---

# 3. Treinos

## WORKOUT-001 — Criar ficha completa
**Origem:** Stack 12  
**Prioridade:** Alta  
**Objetivo:** Criar ficha para aluno vinculado com exercícios ordenados.  
**Como executar:** Entrar como professor, escolher aluno vinculado, adicionar nome, descrição, vigência e ao menos dois exercícios.  
**Resultado esperado:** Ficha aparece para professor e aluno correto.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## WORKOUT-002 — Bloquear aluno não vinculado
**Origem:** Stack 12  
**Prioridade:** Crítica  
**Objetivo:** Impedir atribuição de treino a aluno fora da responsabilidade do professor.  
**Como executar:** Alterar manualmente `aluno_id` do request para aluno não vinculado.  
**Resultado esperado:** HTTP 403 e nenhuma ficha criada.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## WORKOUT-003 — Visualização do aluno
**Origem:** Stack 12  
**Prioridade:** Crítica  
**Objetivo:** Aluno vê apenas suas próprias fichas.  
**Como executar:** Criar fichas para dois alunos; entrar como um deles e tentar acessar ficha do outro por ID.  
**Resultado esperado:** Próprias fichas aparecem; ficha alheia retorna 404/nega acesso.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## WORKOUT-004 — Iniciar e concluir treino
**Origem:** Stack 12  
**Prioridade:** Alta  
**Objetivo:** Validar histórico real de execução.  
**Como executar:** Iniciar treino como aluno, concluir e consultar `execucao_treino`.  
**Resultado esperado:** Início e conclusão registrados com academia, ficha e aluno corretos.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## WORKOUT-005 — Editar ficha
**Origem:** Stack 12  
**Prioridade:** Alta  
**Objetivo:** Validar atualização e versionamento.  
**Como executar:** Editar nome, exercícios e carga.  
**Resultado esperado:** Dados atualizados, ordem consistente e `versao` +1.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## WORKOUT-006 — Encerrar ficha
**Origem:** Stack 12  
**Prioridade:** Alta  
**Objetivo:** Impedir nova execução em ficha encerrada mantendo histórico.  
**Como executar:** Definir status `encerrado` e tentar iniciar treino.  
**Resultado esperado:** Histórico consultável e nova execução rejeitada.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

---

# 4. Dietas e avaliações

## DIET-001 — Criar plano alimentar completo
**Origem:** Stack 13  
**Prioridade:** Alta  
**Objetivo:** Criar plano com responsável, validade, refeições, itens e substituições.  
**Como executar:** Entrar com usuário autorizado, criar plano para aluno da mesma academia e adicionar ao menos duas refeições.  
**Resultado esperado:** Plano salvo integralmente e exibido ao aluno correto.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## DIET-002 — Acesso do aluno
**Origem:** Stack 13  
**Prioridade:** Crítica  
**Objetivo:** Garantir que aluno veja apenas o próprio plano.  
**Como executar:** Entrar como Aluno A e tentar acessar por URL o plano do Aluno B.  
**Resultado esperado:** Lista contém somente planos de A e plano de B retorna 404.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## DIET-003 — Validade inválida
**Origem:** Stack 13  
**Prioridade:** Alta  
**Objetivo:** Rejeitar `data_fim` anterior a `data_inicio`.  
**Como executar:** Informar data final anterior à inicial.  
**Resultado esperado:** Erro de validação e nenhum plano gravado.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## DIET-004 — Editar e verificar versão
**Origem:** Stack 13  
**Prioridade:** Alta  
**Objetivo:** Preservar rastreabilidade da edição.  
**Como executar:** Editar nome/refeições e consultar versão/histórico.  
**Resultado esperado:** `versao` aumenta e histórico registra snapshot.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## DIET-005 — Política profissional
**Origem:** Stack 13  
**Prioridade:** Crítica  
**Objetivo:** Validar que prescrição alimentar siga política profissional definida pela academia.  
**Como executar:** Revisar profissionais autorizados e verificar qualificação/registro do responsável.  
**Resultado esperado:** Somente profissionais formalmente autorizados usam criação/edição.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## EVAL-001 — Criar avaliação
**Origem:** Stack 14  
**Prioridade:** Alta  
**Objetivo:** Validar cadastro e cálculo de IMC.  
**Como executar:** Entrar como aluno, cadastrar peso, altura e gordura corporal e abrir detalhe.  
**Resultado esperado:** Avaliação criada, IMC calculado no backend e visível só ao próprio aluno.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## EVAL-002 — Tentativa de editar histórico
**Origem:** Stack 14  
**Prioridade:** Alta  
**Objetivo:** Validar imutabilidade.  
**Como executar:** Chamar rota de edição/alteração de avaliação antiga.  
**Resultado esperado:** HTTP 405 e registro original preservado.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## EVAL-003 — Evolução
**Origem:** Stack 14  
**Prioridade:** Média  
**Objetivo:** Validar comparação entre avaliações.  
**Como executar:** Criar duas avaliações em datas distintas e abrir progresso.  
**Resultado esperado:** Peso inicial, atual e variação coerentes.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

---

# 5. Planos, estoque, pedidos e pagamentos

## MEMBERSHIP-001 — Ciclo de matrícula
**Origem:** Stack 15  
**Prioridade:** Alta  
**Objetivo:** Validar criação, suspensão e cancelamento.  
**Como executar:** Criar plano, matricular aluno, suspender e cancelar.  
**Resultado esperado:** Status atualizado e histórico preservado em cada mudança.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## MEMBERSHIP-002 — Alteração do plano
**Origem:** Stack 15  
**Prioridade:** Alta  
**Objetivo:** Preservar histórico financeiro.  
**Como executar:** Matricular em plano de R$100 e depois alterar plano para R$120.  
**Resultado esperado:** Matrícula existente mantém R$100 em `valor_contratado`.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## STOCK-001 — Entrada e saída
**Origem:** Stack 16  
**Prioridade:** Alta  
**Objetivo:** Validar movimentações e saldo.  
**Como executar:** Criar produto, registrar entrada de 10 e saída de 3.  
**Resultado esperado:** Saldo 7 e duas movimentações coerentes.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## STOCK-002 — Estoque insuficiente
**Origem:** Stack 16  
**Prioridade:** Crítica  
**Objetivo:** Impedir saldo negativo.  
**Como executar:** Tentar vender quantidade maior que o saldo.  
**Resultado esperado:** Operação rejeitada sem alterar estoque.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## ORDER-001 — Pedido com estoque
**Origem:** Stack 17  
**Prioridade:** Crítica  
**Objetivo:** Validar criação, cálculo no backend e baixa de estoque.  
**Como executar:** Criar produto com saldo 5 e pedir 2 unidades.  
**Resultado esperado:** Pedido usa preço do banco, item snapshot e saldo final 3.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## ORDER-002 — Repetir requisição
**Origem:** Stack 17  
**Prioridade:** Crítica  
**Objetivo:** Validar idempotência.  
**Como executar:** Repetir a mesma criação com a mesma chave.  
**Resultado esperado:** Nenhum pedido/pagamento duplicado.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

---

# 6. Uploads e recuperação de senha

## FILE-001 — Upload permitido
**Origem:** Stack 18  
**Prioridade:** Alta  
**Objetivo:** Validar JPEG/PNG/WebP/PDF permitidos fora de `public/`.  
**Como executar:** Enviar arquivo permitido e conferir metadados/caminho.  
**Resultado esperado:** Nome aleatório, MIME correto e arquivo em `storage/uploads`.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## FILE-002 — Upload malicioso
**Origem:** Stack 18  
**Prioridade:** Crítica  
**Objetivo:** Bloquear executáveis disfarçados.  
**Como executar:** Tentar enviar PHP renomeado para imagem.  
**Resultado esperado:** Upload rejeitado.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## RESET-001 — Fluxo completo por e-mail
**Origem:** Stack 19  
**Prioridade:** Crítica  
**Objetivo:** Validar solicitação, recebimento e troca de senha.  
**Pré-requisitos:** SMTP configurado.  
**Como executar:** Solicitar recuperação, abrir link e definir nova senha.  
**Resultado esperado:** Senha antiga deixa de funcionar e nova senha autentica.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## RESET-002 — Link reutilizado
**Origem:** Stack 19  
**Prioridade:** Crítica  
**Objetivo:** Validar token de uso único.  
**Como executar:** Reutilizar o mesmo link após troca bem-sucedida.  
**Resultado esperado:** Link rejeitado como inválido/expirado.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

---

# 7. Erros, testes e qualidade

## HTTP-001 — Página inexistente
**Origem:** Stack 20  
**Prioridade:** Alta  
**Objetivo:** Validar 404 amigável.  
**Como executar:** Abrir URL inexistente.  
**Resultado esperado:** HTTP 404, mensagem simples e código de referência.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## HTTP-002 — Método incorreto
**Origem:** Stack 20  
**Prioridade:** Alta  
**Objetivo:** Validar HTTP 405.  
**Como executar:** Enviar GET para rota que aceita apenas POST.  
**Resultado esperado:** HTTP 405 e cabeçalho `Allow`.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## HTTP-003 — Erro de produção
**Origem:** Stack 20  
**Prioridade:** Crítica  
**Objetivo:** Garantir que detalhes internos não vazem.  
**Como executar:** Com `APP_DEBUG=false`, provocar erro controlado em homologação.  
**Resultado esperado:** Mensagem genérica com `request_id`, sem stack trace.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## QA-001 — Execução local da suíte
**Origem:** Stack 21  
**Prioridade:** Alta  
**Objetivo:** Validar instalação/configuração do PHPUnit localmente.  
**Como executar:** Configurar `.env.testing`, usar banco descartável, executar `composer install` e `composer test`.  
**Resultado esperado:** PHPUnit executa Unit e Integration sem usar banco de produção.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## QA-002 — Revisão do gate local
**Origem:** Stack 22  
**Prioridade:** Alta  
**Objetivo:** Validar ferramentas de qualidade.  
**Como executar:** Rodar `composer check` após `composer install`.  
**Resultado esperado:** Ferramentas analisam o código moderno e o legado excluído não domina o relatório.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

---

# 8. Logs, Docker e CI/CD

## LOG-001 — Logs no Docker/local
**Origem:** Stack 23  
**Prioridade:** Alta  
**Objetivo:** Confirmar logs estruturados com `request_id` sem segredos.  
**Como executar:** Subir aplicação, provocar erro controlado e inspecionar stdout/stderr.  
**Resultado esperado:** Log com `request_id`, sem senha/token/cookie.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## DOCKER-001 — Subida completa do ambiente
**Origem:** Stack 24  
**Prioridade:** Crítica  
**Objetivo:** Validar aplicação, MariaDB, migrations, health e volumes em conjunto.  
**Como executar:** Configurar `.env`, `docker compose up --build -d`, aplicar migrations, abrir `/health`, fazer login e reiniciar containers.  
**Resultado esperado:** Health `ok`, login funciona e dados persistem após reinício.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## CI-001 — Proteção de branch
**Origem:** Stack 25  
**Prioridade:** Alta  
**Objetivo:** Garantir que `main` e `develop` bloqueiem merge com CI falhando.  
**Como executar:** Configurar branch protection e abrir PR de teste com check falhando.  
**Resultado esperado:** GitHub bloqueia merge até checks obrigatórios ficarem verdes.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

---

# 9. Performance e interface

## PERF-001 — Listagem com volume
**Origem:** Stack 26  
**Prioridade:** Alta  
**Objetivo:** Validar responsividade e paginação sob volume.  
**Como executar:** Popular banco de teste com centenas/milhares de registros, navegar listagens e observar logs de slow request.  
**Resultado esperado:** Paginação consistente e nenhum carregamento ilimitado novo.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## UI-001 — Fluxo visual dos três perfis
**Origem:** Stack 27  
**Prioridade:** Alta  
**Objetivo:** Validar navegação, mensagens e logout nos dashboards.  
**Como executar:** Entrar como admin, professor e aluno; navegar todos os links em desktop e celular.  
**Resultado esperado:** Layout consistente, sem links fixos `/gym_genesis`, logout POST funciona e conteúdo é legível.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

---

# 10. LGPD, API e produção

## PRIVACY-001 — Processo de solicitação do titular
**Origem:** Stack 28  
**Prioridade:** Alta  
**Objetivo:** Validar registro, acompanhamento e conclusão de solicitação sem exclusão automática.  
**Como executar:** Em homologação, abrir solicitação de exportação/eliminação, revisar dados e registrar decisão.  
**Resultado esperado:** Solicitação possui status/histórico claro e nenhuma ação destrutiva ocorre sem decisão explícita.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## API-001 — Consumo externo da API
**Origem:** Stack 29  
**Prioridade:** Crítica  
**Objetivo:** Validar autenticação/paginação externa sem sessão web.  
**Como executar:** Emitir token `students:read`; usar curl/Postman em `/api/v1/me` e `/api/v1/students?page=1&per_page=10` com Bearer token.  
**Resultado esperado:** JSON consistente, somente dados da academia do token e nenhum hash/senha.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

## PROD-001 — Simulação operacional em staging
**Origem:** Stack 30  
**Prioridade:** Crítica  
**Objetivo:** Validar deploy, health, logs, backup, restore e rollback.  
**Pré-requisitos:** Ambiente de staging e release candidata.  
**Como executar:** Publicar em staging; validar `/health` e `/ready`; gerar backup; restaurar em banco descartável; provocar erro e localizar `request_id`; executar rollback para imagem anterior.  
**Resultado esperado:** Todos os procedimentos do runbook são concluídos e documentados sem usar produção para ensaio.  
**Resultado obtido:** ________________________________________  
**Status:** NÃO EXECUTADO

---

# Ordem recomendada de execução

1. `DOCKER-001`, `QA-001`, `QA-002`
2. `TENANT-001` a `TENANT-003`, `ROLE-001` e `ROLE-002`
3. `AUTH-001`, `AUTH-002`, `RESET-001`, `RESET-002`
4. `STUDENT-*` e `PROFESSOR-*`
5. `WORKOUT-*`, `DIET-*`, `EVAL-*`
6. `MEMBERSHIP-*`, `STOCK-*`, `ORDER-*`
7. `FILE-*`, `HTTP-*`, `LOG-001`
8. `UI-001`, `PERF-001`, `API-001`, `PRIVACY-001`
9. `CI-001`
10. `PROD-001`

# Critério para homologação concluída

A homologação só deve ser considerada concluída quando:

- todos os testes de prioridade **Crítica** estiverem `PASSOU`;
- nenhum teste de prioridade **Alta** estiver `FALHOU` sem correção ou justificativa formal;
- qualquer `BLOQUEADO` tiver causa registrada;
- `PROD-001` tiver sido executado em staging;
- o resultado final estiver registrado neste arquivo.