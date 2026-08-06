# Arquitetura oficial do Gym Genesis

## Direção adotada

A aplicação orientada a objetos em `app/` é a implementação oficial. O ponto de entrada HTTP é exclusivamente `public/index.php`, que carrega `bootstrap/app.php`, registra as rotas e despacha a requisição.

```text
public/index.php
  -> bootstrap/app.php
  -> routes/web.php
  -> Router
  -> Middleware
  -> Controller
  -> Service
  -> Repository
  -> Database
```

## Responsabilidades

- `public/`: front controller e arquivos públicos. Não deve conter regra de negócio.
- `bootstrap/`: inicialização do ambiente e composição de dependências.
- `routes/`: declaração de rotas e Router.
- `app/Controllers/`: coordenação HTTP e renderização.
- `app/Services/`: casos de uso e regras que coordenam operações.
- `app/Repositories/`: persistência e consultas SQL.
- `app/Views/`: apresentação, sem SQL ou regra de negócio.
- `app/Middleware/`: autenticação, autorização e filtros HTTP.
- `app/Container/`: resolução simples de dependências.

## Injeção de dependências

Controllers novos devem receber Services pelo construtor. Services recebem Repositories pelo construtor. O Router resolve controllers pelo `Container`.

```php
final class ExampleController
{
    public function __construct(
        private readonly ExampleService $service
    ) {
    }
}
```

O container utiliza autowiring por tipos concretos. Bindings manuais devem ser usados apenas para interfaces, configurações ou integrações externas.

## Código legado

Os diretórios procedurais existentes continuam preservados temporariamente para não interromper funcionalidades ainda não migradas. Eles não devem receber novas funcionalidades.

- `controller/`
- `code/`
- `public/php/`
- páginas procedurais diretamente em `public/`
- protótipos em `template/`

Cada módulo deve ser migrado separadamente. Depois que uma rota equivalente em `app/` for validada, o endpoint legado correspondente deve ser desativado e removido em uma entrega específica.

## Regras de evolução

1. Não criar novos endpoints fora do Router oficial.
2. Não acessar o banco em Views ou Controllers.
3. Não instanciar Repositories dentro de Controllers novos.
4. Manter uma única implementação ativa por funcionalidade.
5. Remover código legado somente após confirmar substituição funcional.
6. Evitar interfaces e camadas sem uma necessidade concreta.

## Limitações atuais

A migração completa do legado não foi realizada nesta stack porque exige validação funcional módulo a módulo. O container foi introduzido e o fluxo de autenticação passou a usar injeção por construtor como padrão inicial. Os demais controllers serão migrados progressivamente nas stacks de seus domínios.
