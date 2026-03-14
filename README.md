# Gym Genesis - Sistema de Gerenciamento de Academia

![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg) ![MariaDB](https://img.shields.io/badge/Database-MariaDB-orange.svg) ![Architecture](https://img.shields.io/badge/Architecture-MVC-green.svg) ![Security](https://img.shields.io/badge/Security-XSS%2FCSFR%20Protected-red.svg)

## Visão Geral

O **Gym Genesis** é um sistema completo e robusto para gerenciamento de academias, projetado para otimizar a administração, o acompanhamento de alunos e a interação entre professores e estudantes. Desenvolvido em PHP com uma arquitetura MVC (Model-View-Controller) e utilizando MariaDB como banco de dados, o sistema oferece uma solução escalável e segura para academias de todos os portes.

Recentemente, o projeto passou por uma refatoração significativa para aprimorar sua arquitetura, segurança e usabilidade, introduzindo um sistema de roteamento moderno, proteção contra vulnerabilidades comuns e dashboards dedicados para diferentes perfis de usuário.

## Funcionalidades

O sistema Gym Genesis oferece um conjunto abrangente de funcionalidades, divididas por perfil de usuário:

### Dashboard do Administrador

*   **Gestão de Usuários:** Cadastro, edição e exclusão de administradores, professores e alunos.
*   **Gestão de Planos:** Criação, atualização e remoção de planos de assinatura da academia.
*   **Gestão de Produtos:** Controle de estoque, preços e categorias de produtos da loja virtual.
*   **Gestão de Pedidos:** Visualização e atualização do status de pedidos realizados na loja.
*   **Visão Geral:** Métricas e resumos importantes sobre o funcionamento da academia.

### Dashboard do Professor

*   **Gestão de Alunos:** Visualização e acompanhamento dos alunos sob sua responsabilidade.
*   **Gestão de Treinos:** Criação, edição e atribuição de treinos personalizados para os alunos.
*   **Gestão de Dietas:** Criação, edição e atribuição de planos de dieta para os alunos.
*   **Visão Geral:** Resumo das atividades e alunos gerenciados.

### Dashboard do Aluno

*   **Meus Treinos:** Acesso aos treinos atribuídos pelo professor, com detalhes e histórico.
*   **Minhas Dietas:** Acesso aos planos de dieta recomendados.
*   **Progresso:** Acompanhamento visual do desempenho e evolução (funcionalidade a ser expandida).
*   **Perfil:** Visualização e edição de informações pessoais.

## Tecnologias Utilizadas

*   **Backend:** PHP 8.1+
*   **Banco de Dados:** MariaDB
*   **Servidor Web:** Apache
*   **Gerenciamento de Dependências:** Composer
*   **Contêineres:** Docker, Docker Compose
*   **Frontend:** HTML5, CSS3, JavaScript (básico)

## Arquitetura

O projeto foi refatorado para seguir o padrão **Model-View-Controller (MVC)**, promovendo a separação de responsabilidades e facilitando a manutenção e escalabilidade do código. 

### Componentes Principais:

*   **Router:** Gerencia as rotas da aplicação, direcionando as requisições para os Controllers apropriados.
*   **Controllers:** Processam as requisições do usuário, interagem com os Services e preparam os dados para as Views.
*   **Services:** Contêm a lógica de negócio da aplicação, orquestrando as operações dos Repositories.
*   **Repositories:** Abstraem a camada de acesso a dados, interagindo diretamente com o banco de dados via PDO.
*   **Views:** Responsáveis pela apresentação dos dados ao usuário, utilizando templates PHP.
*   **Middleware:** Implementado para controle de autenticação e autorização, garantindo que apenas usuários com permissões adequadas acessem certas áreas.
*   **Helpers:** Funções utilitárias para segurança (XSS, CSRF, hash de senhas) e validação.

## Estrutura de Pastas

A estrutura de diretórios do projeto reflete a arquitetura MVC:

```
gym_genesis/
├── app/
│   ├── Controllers/       # Lógica de controle da aplicação
│   ├── Helpers/           # Funções utilitárias e de segurança
│   ├── Middleware/        # Lógica de autenticação e autorização
│   ├── Models/            # Modelos de dados (futura expansão)
│   ├── Repositories/      # Camada de acesso a dados
│   ├── Services/          # Lógica de negócio
│   └── Views/             # Templates HTML/PHP para a interface do usuário
├── code/                  # Código legado (a ser refatorado ou removido)
├── db/                    # Scripts SQL do banco de dados
├── public/                # Ponto de entrada da aplicação (DocumentRoot)
│   ├── css/
│   ├── js/
│   └── index.php
├── routes/                # Definição das rotas da aplicação
│   ├── Router.php
│   └── web.php
├── .env.example           # Exemplo de arquivo de variáveis de ambiente
├── .gitignore             # Arquivos e pastas a serem ignorados pelo Git
├── composer.json          # Dependências do Composer
├── DEPLOY.md              # Guia de deploy para produção
└── README.md              # Este arquivo
```

## Instalação

### Pré-requisitos

Certifique-se de ter as seguintes ferramentas instaladas em seu sistema:

*   [Git](https://git-scm.com/)
*   [Docker](https://www.docker.com/get-started) e [Docker Compose](https://docs.docker.com/compose/install/)

### Instalação com Docker (Recomendado)

Esta é a maneira mais fácil de configurar o ambiente de desenvolvimento, pois o Docker Compose irá gerenciar o servidor web (Apache), o PHP e o banco de dados (MariaDB).

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/Kingnike1/gym_genesis.git
    cd gym_genesis
    ```

2.  **Configure as variáveis de ambiente:**
    Crie um arquivo `.env` na raiz do projeto, copiando o `.env.example`:
    ```bash
    cp .env.example .env
    ```
    Edite o arquivo `.env` e preencha as variáveis de ambiente, como credenciais do banco de dados e configurações da aplicação. Exemplo:
    ```dotenv
    DB_HOST=db
    DB_NAME=gym_genesis
    DB_USER=root
    DB_PASSWORD=root_password
    APP_ENV=development
    ```

3.  **Suba os serviços Docker:**
    ```bash
    docker-compose up --build -d
    ```
    Isso irá construir as imagens Docker, criar os contêineres e iniciá-los em segundo plano.

4.  **Importe o banco de dados:**
    Acesse o contêiner do MariaDB e importe o esquema do banco de dados:
    ```bash
    docker-compose exec db mysql -u root -pYOUR_DB_PASSWORD gym_genesis < db/banco.sql
    ```
    Substitua `YOUR_DB_PASSWORD` pela senha definida no seu arquivo `.env`.

5.  **Acesse a aplicação:**
    A aplicação estará disponível em `http://localhost:8080` (ou a porta configurada no `docker-compose.yml`).

### Instalação Manual (Alternativa)

Se você preferir configurar o ambiente manualmente, siga estes passos:

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/Kingnike1/gym_genesis.git
    cd gym_genesis
    ```

2.  **Instale as dependências do Composer:**
    ```bash
    composer install
    ```

3.  **Configure o banco de dados MariaDB:**
    *   Crie um banco de dados chamado `gym_genesis`.
    *   Importe o arquivo `db/banco.sql` para o seu banco de dados.

4.  **Configure as variáveis de ambiente:**
    Crie um arquivo `.env` na raiz do projeto, copiando o `.env.example` e preenchendo as credenciais do banco de dados e outras configurações.

5.  **Configure o servidor web (Apache):**
    Configure um VirtualHost para apontar o `DocumentRoot` para a pasta `public/` do projeto. Certifique-se de que o `mod_rewrite` esteja habilitado.

6.  **Acesse a aplicação:**
    Acesse a URL configurada para o seu VirtualHost.

## Uso

Após a instalação, você pode acessar a tela de login em `/gym_genesis/login`.

### Credenciais de Teste (Exemplo - para desenvolvimento)

*   **Administrador:** `admin@gym.com` / `senha123`
*   **Professor:** `professor@gym.com` / `senha123`
*   **Aluno:** `aluno@gym.com` / `senha123`

Após o login, você será redirecionado para o dashboard correspondente ao seu perfil.

## Segurança

O Gym Genesis foi desenvolvido com foco em segurança, incorporando as seguintes medidas:

*   **Proteção XSS (Cross-Site Scripting):** Todas as saídas de dados nas views são sanitizadas usando `htmlspecialchars` para prevenir a injeção de scripts maliciosos.
*   **Proteção CSRF (Cross-Site Request Forgery):** Tokens CSRF são gerados e validados em todos os formulários POST para garantir que as requisições venham de fontes legítimas.
*   **Senhas Hashed:** As senhas dos usuários são armazenadas no banco de dados utilizando o algoritmo `bcrypt`, garantindo que não sejam armazenadas em texto plano.
*   **Variáveis de Ambiente:** Credenciais sensíveis (como as do banco de dados) são carregadas de um arquivo `.env` e não são expostas no código-fonte ou no controle de versão.
*   **Middleware de Autenticação/Autorização:** Garante que apenas usuários autenticados e com os papéis corretos possam acessar determinadas funcionalidades e dashboards.

## Contribuição

Contribuições são bem-vindas! Se você deseja contribuir para o projeto, por favor, siga os seguintes passos:

1.  Faça um fork do repositório.
2.  Crie uma nova branch para sua feature (`git checkout -b feature/minha-nova-feature`).
3.  Faça suas alterações e commit (`git commit -m 'feat: adiciona nova feature'`).
4.  Envie para o seu fork (`git push origin feature/minha-nova-feature`).
5.  Abra um Pull Request para a branch `main` do repositório original.

## Licença

Este projeto está licenciado sob a Licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## Contato

Para dúvidas ou sugestões, entre em contato com o mantenedor do projeto.

--- 

**Desenvolvido por Manus AI**
