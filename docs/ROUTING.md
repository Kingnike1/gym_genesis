# Roteamento HTTP

## Princípios

O `Router` é a fonte única das rotas da aplicação orientada a objetos.

- caminhos são registrados sem o diretório físico do projeto;
- a base da aplicação é detectada por `SCRIPT_NAME`;
- mudanças de estado não usam `GET`;
- rotas protegidas recebem middleware no próprio registro;
- parâmetros numéricos usam restrição explícita.

## Parâmetros

```php
Router::get('/users/{id:\d+}', 'UserController@show');
```

O valor de `id` é encaminhado ao método do controller na ordem em que aparece na rota.

## Grupos e middleware

```php
Router::group('/admin', [
    static fn () => AuthMiddleware::requireUserType(1),
], static function (): void {
    Router::get('/users', 'UserController@index');
});
```

## Métodos suportados

- `GET`
- `POST`
- `PUT`
- `PATCH`
- `DELETE`

Formulários HTML podem usar `POST` com o campo oculto `_method`:

```html
<form method="post" action="/admin/users/10">
    <input type="hidden" name="_method" value="DELETE">
    <button type="submit">Excluir</button>
</form>
```

Somente `PUT`, `PATCH` e `DELETE` são aceitos como override, e apenas quando a requisição real é `POST`.

## Geração de URLs

Para evitar caminhos fixos como `/gym_genesis`, use:

```php
Router::url('/login');
Router::url('/admin/dashboard');
```

O helper inclui automaticamente o diretório-base quando a aplicação estiver instalada em subdiretório.

## Respostas

- rota inexistente: `404`;
- caminho existente com método incorreto: `405` e cabeçalho `Allow`;
- acesso sem autenticação: redirecionamento para login;
- perfil sem permissão: `403`.

## Exclusões

Rotas antigas como `/admin/users/delete/{id}` não devem ser restauradas como `GET`. A interface precisa utilizar formulário `POST` com `_method=DELETE` e token CSRF.
