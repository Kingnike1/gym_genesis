# Frontend moderno

A camada moderna usa CSS próprio pequeno em `public/assets/css/app.css` e um layout compartilhado em `app/Views/layouts/dashboard.php`. A decisão evita carregar simultaneamente Bootstrap e Tailwind no fluxo novo e reduz dependências de build.

## Padrões
- URLs sempre por `Router::url()`;
- logout somente POST + CSRF;
- HTML responsivo e navegável por teclado;
- mensagens usam `role=alert`/`role=status`;
- tabelas devem ficar dentro de `.table-wrap`;
- novos dashboards reutilizam o layout compartilhado;
- estilos legados são migrados conforme as páginas forem substituídas, sem refatoração visual em massa.
