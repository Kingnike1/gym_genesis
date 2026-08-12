# CI/CD

Todo PR deve passar pelo workflow `CI`: validação do Composer, instalação pelo lockfile, migrations em MariaDB de teste, `composer check`, `composer audit --locked` e build da imagem Docker.

## Branch protection

Após o workflow estar verde, configure `develop` e `main` para exigir os checks de CI e Pull Request antes do merge.

## Lockfile

O `composer.lock` deve estar coerente com `composer.json`. Como este ambiente conectado ao GitHub não executa Composer, as novas dependências exigem regeneração local do lockfile antes da aprovação final: `composer update`, revisar o diff e commitar apenas o lock esperado.

## Releases

`release.yml` valida a imagem para `staging` ou `production` e usa GitHub Environments para permitir aprovação manual. O passo de publicação real deve ser conectado ao provedor escolhido; não há credenciais ou infraestrutura inventadas no repositório.
