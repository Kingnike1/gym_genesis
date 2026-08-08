# Stack 10 — Alunos

O módulo de alunos passa a usar a tabela `aluno` como perfil operacional do vínculo do usuário com uma academia.

## Princípios

- `usuario` continua sendo a identidade global.
- `academia_usuario` define o vínculo e o papel na academia.
- `aluno` guarda dados específicos do aluno dentro da academia.
- um usuário pode ter um perfil de aluno diferente em academias diferentes.
- toda consulta de aluno é filtrada por `academia_id`.

## Dados principais

- matrícula única dentro da academia;
- unidade;
- nome e identificação;
- telefone e contato de emergência;
- objetivo e observações;
- status `ativo`, `inativo` ou `suspenso`;
- timestamps de criação e atualização.

## Histórico

Treinos, avaliações, dietas e matrículas devem continuar em suas tabelas próprias. O perfil de aluno não armazena cópias desses históricos.

## Busca

`AlunoRepository::search()` suporta busca por nome, matrícula ou CPF, filtro de status e paginação limitada a 100 registros por chamada.

## Segurança

O `AlunoRepository` é `academyScoped`; `find`, `all` e `delete` herdados do `BaseRepository` exigem contexto de academia, e os métodos específicos também incluem `academia_id`.