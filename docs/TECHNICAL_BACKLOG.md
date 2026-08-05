# Gym Genesis — Backlog Técnico de Evolução

## Objetivo geral

Evoluir o Gym Genesis para uma plataforma profissional de gestão de academias, preparada para atender múltiplas academias, sem tornar o código desnecessariamente complexo.

A evolução deve preservar o rumo atual do projeto: PHP, PDO, MySQL, Composer, Controllers, Services, Repositories, Views, Middleware e Docker.

A estratégia é evoluir gradualmente: consolidar a arquitetura atual, remover o legado com segurança, reforçar autenticação e autorização, automatizar testes e preparar o sistema para operar com academias reais.

## Prioridades

- **P0 — Crítico:** segurança, credenciais, sessões e acesso indevido.
- **P1 — Importante:** arquitetura, banco, multiacademia, módulos centrais e produção.
- **P2 — Evolução:** qualidade, automação, integrações e performance.
- **P3 — Refinamento:** experiência visual e acabamento.

## Visão das stacks

| Stack | Área | Prioridade |
|---|---|---:|
| 01 | Segurança emergencial | P0 |
| 02 | Limpeza do repositório | P0 |
| 03 | Sessões e autenticação | P0 |
| 04 | Autorização e permissões | P0 |
| 05 | Router e requisições HTTP | P1 |
| 06 | Consolidação da arquitetura | P1 |
| 07 | Banco de dados e migrations | P1 |
| 08 | Multiacademia | P1 |
| 09 | Usuários e perfis | P1 |
| 10 | Alunos | P1 |
| 11 | Professores | P1 |
| 12 | Treinos | P1 |
| 13 | Dietas | P1 |
| 14 | Avaliações físicas | P1 |
| 15 | Planos e matrículas | P1 |
| 16 | Loja e estoque | P2 |
| 17 | Pedidos e pagamentos | P1 |
| 18 | Uploads e arquivos | P1 |
| 19 | Recuperação de senha | P1 |
| 20 | Validação e tratamento de erros | P1 |
| 21 | Testes automatizados | P1 |
| 22 | Qualidade de código | P2 |
| 23 | Logs e auditoria | P1 |
| 24 | Docker e ambiente | P1 |
| 25 | CI/CD | P2 |
| 26 | Performance | P2 |
| 27 | Frontend e experiência | P3 |
| 28 | LGPD e proteção de dados | P1 |
| 29 | API e integrações | P2 |
| 30 | Monitoramento e produção | P2 |

## Stack 01 — Segurança emergencial

- [ ] Remover `.env` do Git e manter apenas `.env.example`.
- [ ] Remover segredos do histórico e trocar credenciais expostas.
- [ ] Criar usuário próprio do banco, sem utilizar `root`.
- [ ] Desativar exibição pública de erros em produção.
- [ ] Definir `APP_ENV`, `APP_DEBUG`, `APP_URL` e `APP_SECRET`.
- [ ] Garantir HTTPS e cabeçalhos básicos de segurança.

**Aceite:** nenhum segredo no Git, aplicação sem usuário root e produção sem exibir detalhes internos.

## Stack 02 — Limpeza do repositório

- [ ] Remover `vendor/`, logs, uploads e arquivos temporários do Git.
- [ ] Remover `composer-setup.php`, backups e cópias obsoletas.
- [ ] Organizar documentação em `docs/` e testes em `tests/`.
- [ ] Criar `.dockerignore` e fortalecer `.gitignore`.
- [ ] Identificar claramente código ativo e legado.

**Aceite:** o repositório contém apenas código, configurações de exemplo, migrations, testes e documentação necessários.

## Stack 03 — Sessões e autenticação

- [ ] Centralizar inicialização de sessão.
- [ ] Regenerar ID após login e mudança de privilégio.
- [ ] Configurar cookies `HttpOnly`, `Secure` e `SameSite`.
- [ ] Implementar expiração por inatividade.
- [ ] Alterar logout para POST com CSRF.
- [ ] Criar rate limiting e bloqueio temporário no login.
- [ ] Utilizar `PASSWORD_DEFAULT` e rehash quando necessário.

**Aceite:** ciclo de sessão seguro, logout protegido e tentativas excessivas limitadas.

## Stack 04 — Autorização e permissões

- [ ] Criar middlewares por perfil.
- [ ] Aplicar autenticação e autorização diretamente nas rotas.
- [ ] Criar políticas de acesso por recurso e proprietário.
- [ ] Impedir acesso por troca de IDs na URL.
- [ ] Garantir isolamento por academia.
- [ ] Padronizar respostas 403.

**Aceite:** cada usuário acessa apenas recursos permitidos e vinculados à sua academia.

## Stack 05 — Router e HTTP

- [ ] Implementar parâmetros `{id}` e validação.
- [ ] Implementar grupos, prefixos e middlewares por rota.
- [ ] Suportar GET, POST, PUT, PATCH e DELETE.
- [ ] Retornar 404 e 405 corretamente.
- [ ] Remover exclusões e mudanças de estado por GET.
- [ ] Remover caminhos fixos como `/gym_genesis`.

## Stack 06 — Consolidação da arquitetura

- [ ] Definir `app/` como código oficial.
- [ ] Utilizar `public/index.php` como front controller.
- [ ] Migrar módulos legados gradualmente.
- [ ] Remover duplicações e endpoints antigos após substituição.
- [ ] Manter Views sem SQL, Controllers enxutos e Repositories focados em persistência.
- [ ] Criar injeção de dependências simples por construtor.

## Stack 07 — Banco e migrations

- [ ] Definir esquema oficial.
- [ ] Criar migrations, seeders, índices e constraints.
- [ ] Padronizar tabelas e colunas.
- [ ] Revisar foreign keys e cascatas.
- [ ] Utilizar transações em operações compostas.
- [ ] Criar banco isolado para testes.

## Stack 08 — Multiacademia

Estratégia inicial: uma aplicação e um banco, com isolamento obrigatório por `academia_id`.

- [ ] Criar `academias`, `unidades` e vínculos de usuários.
- [ ] Adicionar `academia_id` às entidades de negócio.
- [ ] Criar contexto da academia atual.
- [ ] Impedir consultas sem contexto de academia.
- [ ] Permitir configurações, identidade visual e status por academia.
- [ ] Criar auditoria e testes de isolamento.

**Aceite:** dados de uma academia nunca aparecem em outra.

## Stacks 09 a 15 — Núcleo da academia

- [ ] Centralizar usuários e perfis com enums e DTOs.
- [ ] Profissionalizar cadastro e histórico de alunos.
- [ ] Criar gestão de professores, vínculos, especialidades e CREF.
- [ ] Estruturar treinos com exercícios, séries, carga, vigência e histórico.
- [ ] Estruturar dietas com responsável, validade e controle de acesso.
- [ ] Estruturar avaliações físicas com histórico e dados sensíveis protegidos.
- [ ] Criar planos e matrículas com renovação, suspensão, cancelamento e histórico.

## Stacks 16 e 17 — Loja, estoque, pedidos e pagamentos

- [ ] Criar produtos, categorias, SKU e movimentações de estoque.
- [ ] Impedir estoque negativo.
- [ ] Recalcular valores de pedidos no backend.
- [ ] Utilizar transações para pedido, estoque e pagamento.
- [ ] Criar idempotência e webhooks autenticados.
- [ ] Não armazenar dados de cartão.

## Stacks 18 e 19 — Arquivos e recuperação de senha

- [ ] Criar serviço de armazenamento e validar MIME real.
- [ ] Bloquear arquivos executáveis e gerar nomes aleatórios.
- [ ] Manter uploads fora do código e do Git.
- [ ] Unificar recuperação de senha.
- [ ] Armazenar somente hash do token, com expiração e uso único.
- [ ] Aplicar rate limiting e invalidar sessões anteriores.

## Stacks 20 a 25 — Qualidade e entrega

- [ ] Centralizar validações, DTOs, exceções e tratamento global de erros.
- [ ] Instalar PHPUnit e criar testes unitários, integração e HTTP.
- [ ] Adotar PSR-12, PHPStan e formatação automatizada.
- [ ] Implementar logs PSR-3 e trilha de auditoria.
- [ ] Revisar Docker com build multi-stage, usuário não-root e health check.
- [ ] Criar CI/CD com lint, análise estática, testes e auditoria de dependências.

## Stacks 26 a 30 — Crescimento e produção

- [ ] Criar paginação, índices e medição de consultas lentas.
- [ ] Padronizar frontend e escolher um framework visual principal.
- [ ] Mapear dados pessoais e sensíveis conforme LGPD.
- [ ] Criar API `/api/v1`, tokens, rate limiting e documentação OpenAPI.
- [ ] Preparar integrações com pagamentos, catracas e serviços externos.
- [ ] Criar staging, monitoramento, alertas, backups testados e rollback.

## Ordem recomendada

1. Segurança: Stacks 01 a 04.
2. Base técnica: Stacks 05, 06, 07 e 20.
3. Multiacademia e identidade: Stacks 08 a 11.
4. Núcleo operacional: Stacks 12 a 15.
5. Área comercial: Stacks 16 a 19.
6. Qualidade e produção: Stacks 21 a 25.
7. Crescimento: Stacks 26 a 30.

## Fluxo obrigatório de cada stack

1. Analisar o código atual.
2. Identificar dependências e código legado relacionado.
3. Definir a menor mudança segura.
4. Criar ou atualizar testes.
5. Implementar sem aumentar complexidade desnecessariamente.
6. Executar testes e revisar segurança.
7. Documentar decisões e impactos.
8. Remover ou desativar o código antigo substituído.

## Definição de pronto

- [ ] Objetivo e critérios de aceite cumpridos.
- [ ] Código revisado e coerente com a arquitetura atual.
- [ ] Segurança, autorização e isolamento por academia revisados.
- [ ] Migrations e testes executados.
- [ ] Nenhum segredo, debug ou arquivo temporário incluído.
- [ ] Código legado substituído removido ou desativado.
- [ ] Documentação atualizada.
- [ ] Sem aumento desnecessário de complexidade.

## Princípios

- **Simplicidade:** não criar abstrações sem benefício real.
- **Segurança:** nenhuma entrada é confiável.
- **Isolamento:** toda consulta deve respeitar a academia atual.
- **Evolução gradual:** migrar um módulo por vez.
- **Fonte única:** uma implementação oficial por funcionalidade.
- **Testabilidade:** regras importantes devem ser automatizadas.
- **Rastreabilidade:** ações sensíveis devem ser auditáveis.
