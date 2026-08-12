# API e integrações

A API pública começa em `/api/v1` e usa tokens opacos. O valor bruto do token é exibido somente no momento da emissão; o banco armazena apenas SHA-256.

## Escopos
Tokens recebem uma lista explícita de escopos, por exemplo `students:read`. `*` deve ser reservado para integrações administrativas altamente controladas.

## Isolamento
Cada token pertence a uma academia e usuário ativos. O middleware resolve o mesmo `AcademyContext` usado pela aplicação web, impedindo acesso cross-tenant.

## Rate limiting
Limite inicial: 120 requisições/minuto por token. Em implantação com várias instâncias, substituir o contador local por Redis ou serviço equivalente.

## Webhooks
Assinaturas HMAC SHA-256 devem ser verificadas com `WebhookVerifier`. Eventos externos devem ter ID idempotente e ser registrados em `webhook_evento` antes do processamento. Segredos de webhook ficam somente em variáveis/secret manager.

## Contrato
`docs/openapi.yaml` é a fonte do contrato HTTP. Integrações com catracas, pagamentos, WhatsApp/e-mail e emissão fiscal devem entrar por adapters/serviços, nunca acessar diretamente o banco.
