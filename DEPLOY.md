# Guia de Deploy - Gym Genesis

Este documento descreve os passos necessários para realizar o deploy do sistema Gym Genesis em um ambiente de produção.

## Pré-requisitos

- Servidor Web (Apache ou Nginx)
- PHP 8.1 ou superior
- Extensões PHP: `pdo`, `pdo_mysql`, `mbstring`, `openssl`
- MariaDB 10.4 ou superior
- Composer

## Passos para Deploy

1. **Clonar o Repositório:**
   ```bash
   git clone https://github.com/Kingnike1/gym_genesis.git
   cd gym_genesis
   ```

2. **Instalar Dependências:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Configurar Variáveis de Ambiente:**
   Copie o arquivo `.env.example` para `.env` e preencha com as informações do seu ambiente de produção.
   ```bash
   cp .env.example .env
   ```
   **Importante:** Certifique-se de definir `APP_ENV=production` e gerar uma chave secreta forte para a aplicação.

4. **Configurar o Banco de Dados:**
   Importe o esquema do banco de dados localizado em `db/banco.sql` para o seu servidor MariaDB.
   ```bash
   mysql -u seu_usuario -p seu_banco < db/banco.sql
   ```

5. **Configurar o Servidor Web:**
   Aponte o `DocumentRoot` do seu servidor web para o diretório `public/` do projeto.

   **Exemplo Apache (VirtualHost):**
   ```apache
   <VirtualHost *:80>
       ServerName seu-dominio.com
       DocumentRoot /caminho/para/gym_genesis/public

       <Directory /caminho/para/gym_genesis/public>
           AllowOverride All
           Require all granted
       </Directory>

       ErrorLog ${APACHE_LOG_DIR}/gym_genesis_error.log
       CustomLog ${APACHE_LOG_DIR}/gym_genesis_access.log combined
   </VirtualHost>
   ```

6. **Permissões de Arquivo:**
   Certifique-se de que o servidor web tenha permissões de escrita nos diretórios necessários (ex: logs, uploads).
   ```bash
   chown -R www-data:www-data /caminho/para/gym_genesis
   chmod -R 755 /caminho/para/gym_genesis
   ```

## Segurança

- Mantenha o arquivo `.env` fora do alcance público.
- Use HTTPS em produção.
- O sistema já inclui proteção contra XSS e CSRF através do `SecurityHelper`.
- As senhas são armazenadas de forma segura usando `bcrypt`.

## Suporte

Para mais informações ou suporte, entre em contato com a equipe de desenvolvimento.
