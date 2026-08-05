# Testes

Este é o diretório oficial para a futura suíte automatizada do Gym Genesis.

## Situação atual

Os testes existentes em `code/tests/` são scripts manuais ligados ao código procedural. Eles ainda não foram movidos porque utilizam caminhos relativos e dependências globais; uma movimentação puramente estrutural poderia torná-los inutilizáveis.

## Próxima evolução

A Stack 21 deverá:

- instalar e configurar PHPUnit;
- separar testes unitários, de integração e HTTP;
- criar banco exclusivo de testes;
- substituir gradualmente os scripts manuais;
- remover `code/tests/` quando suas responsabilidades estiverem cobertas.

Nenhuma imagem, log, banco ou resultado gerado durante testes deve ser versionado.
