# Stack 11 — Professores

O perfil profissional fica separado da identidade global do usuário e pertence a uma academia.

## Estrutura

- `professor`: perfil profissional e CREF;
- `professor_unidade`: unidades onde atua;
- `professor_especialidade`: especialidades livres;
- `professor_aluno`: vínculo operacional com alunos.

## Regras

- CREF é obrigatório e único dentro da academia.
- Professor e aluno só podem ser vinculados se pertencerem à mesma academia.
- Unidades adicionadas ao professor precisam pertencer à academia ativa.
- Status profissional: `ativo`, `inativo` ou `suspenso`.
- O mesmo usuário pode possuir perfis profissionais diferentes em academias diferentes.

## Escopo

Agenda detalhada, turmas e permissões clínicas específicas não foram inventadas nesta stack. Os vínculos criados aqui serão usados pelos módulos de treino, dieta e avaliações.