# Multiacademia — Gym Genesis

## Objetivo

A aplicação passa a operar com múltiplas academias no mesmo banco, mantendo isolamento obrigatório por `academia_id` nas entidades operacionais.

## Modelo

- `academias`: organização principal.
- `unidades`: filiais/unidades pertencentes a uma academia.
- `academia_usuario`: vínculo entre identidade global (`usuario`) e academia.
- `auditoria_academia`: trilha de eventos sensíveis por academia.

A identidade de login continua global. O acesso aos dados da aplicação ocorre dentro de um `AcademyContext` resolvido a partir da sessão e validado contra `academia_usuario`.

## Compatibilidade com dados existentes

A migration `0002_multiacademia` cria uma academia chamada `Gym Genesis Legado` e uma `Unidade Principal`, vincula todos os usuários existentes e associa os registros operacionais existentes a essa academia antes de tornar `academia_id` obrigatório.

Isso evita perda de dados durante a transição.

## Entidades isoladas diretamente

Nesta etapa, recebem `academia_id`:

- plano;
- assinatura;
- funcionario;
- treino;
- aula_agendada;
- avaliacao_fisica;
- dieta;
- endereco;
- forum;
- historico_peso;
- historico_treino;
- pedido;
- produto;
- cupom_desconto;
- meta_usuario;
- perfil_professor;
- perfil_usuario;
- resposta_forum.

Tabelas filhas como `item_pedido`, `refeicao`, `dieta_alimentar` e `treino_exercicio` permanecem isoladas por meio do pai. Catálogos como `alimento` e `exercicio` continuam globais nesta etapa.

## Contexto atual

`App\Tenancy\AcademyContext` é a única fonte de verdade para a academia ativa da requisição.

Regras:

1. o usuário precisa estar autenticado;
2. o vínculo precisa estar ativo;
3. a academia precisa estar ativa;
4. se não houver academia selecionada, é usado o vínculo principal;
5. uma academia diferente só pode ser selecionada se o usuário possuir vínculo ativo com ela.

A rota `POST /academy/select` permite a mudança de contexto e exige CSRF.

## Repositories

Repositories de entidades operacionais devem usar o modo `academyScoped` do `BaseRepository` e adicionar `academia_id` também em SQL customizado.

Uma consulta operacional sem contexto de academia deve falhar em vez de retornar dados globais.

## Auditoria

`App\Tenancy\AcademyAudit` registra academia, usuário, ação, recurso, identificador e contexto JSON. A troca de academia já é auditada. Outras ações sensíveis devem adotar o mesmo serviço nas stacks de domínio correspondentes.

## Configurações por academia

`academias` suporta nome, nome fantasia, CNPJ, telefone, e-mail, logo, status e configurações JSON. `AcademyRepository` oferece leitura e atualização da academia atual e listagem de unidades.

## Perfis

Nesta Stack 8, `tipo_usuario` continua pertencendo à identidade global por compatibilidade. Perfis/papéis diferentes para o mesmo usuário em academias distintas devem ser tratados na Stack 9, evitando ampliar esta migração além do necessário.

## Teste de isolamento

Após aplicar as migrations em banco descartável:

```bash
composer test:tenancy
```

O teste cria duas academias dentro de uma transação, adiciona um plano em cada uma e confirma que o repository retorna apenas os dados da academia selecionada. Ao final, a transação é revertida.

## Validação antes do merge

```bash
composer migrate
composer migrate:status
composer test:tenancy
```

Também devem ser testados manualmente:

- login de usuário legado;
- resolução automática da academia principal;
- listagem de usuários, planos, produtos e pedidos;
- professor visualizando treinos/dietas;
- aluno visualizando avaliações;
- tentativa de selecionar academia sem vínculo;
- tentativa de acessar recurso usando ID pertencente a outra academia.
