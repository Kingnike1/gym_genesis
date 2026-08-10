# Uploads e arquivos

A aplicação usa `StorageInterface` e `LocalStorage` em desenvolvimento. Arquivos ficam em `storage/uploads`, fora de `public/`.

Regras: MIME real via `finfo`, lista fechada de tipos, limite de 5 MB, nomes aleatórios, metadados em `arquivo`, arquivos privados por padrão e autorização por proprietário/academia. Nunca confiar em extensão ou nome enviado pelo usuário.

Adapters externos como S3/R2 podem implementar o mesmo contrato sem alterar regras de negócio.
