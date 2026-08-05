# Configuração de segurança

Este documento registra as configurações mínimas da **Stack 01 — Segurança emergencial**.

## Arquivo de ambiente

1. Copie `.env.example` para `.env`.
2. Gere senhas exclusivas para o banco e SMTP.
3. Gere `APP_SECRET` com pelo menos 32 bytes aleatórios.
4. Nunca envie `.env` para o Git.

Exemplo para gerar uma chave:

```bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

## Produção

Defina obrigatoriamente:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com
APP_FORCE_HTTPS=true
```

A aplicação deve utilizar um usuário próprio do banco, com acesso apenas ao banco do Gym Genesis. O usuário `root` deve ficar restrito à administração do MariaDB e não pode ser utilizado pela aplicação.

## Credenciais expostas

A remoção do `.env` do estado atual do repositório não apaga versões antigas do histórico. Antes de publicar uma versão, troque todas as credenciais que já tenham sido utilizadas, incluindo banco, SMTP e chaves da aplicação.

Uma reescrita do histórico é uma operação destrutiva e deve ser feita apenas com todos os colaboradores avisados. Quando necessária, utilize uma ferramenta apropriada, como `git filter-repo`, faça backup e force a atualização de todas as cópias locais.

## Cabeçalhos e HTTPS

O front controller aplica cabeçalhos básicos de segurança e pode forçar HTTPS quando `APP_ENV=production` e `APP_FORCE_HTTPS=true`.

O servidor ou proxy reverso também deve:

- possuir certificado TLS válido;
- redirecionar HTTP para HTTPS;
- proteger o diretório raiz e permitir acesso público apenas a `public/`;
- impedir acesso direto ao `.env` e a arquivos internos.

## Validação antes do deploy

- [ ] `.env` não está rastreado pelo Git.
- [ ] `APP_DEBUG=false`.
- [ ] `APP_URL` utiliza HTTPS.
- [ ] `APP_FORCE_HTTPS=true`.
- [ ] `APP_SECRET` foi gerado aleatoriamente.
- [ ] A aplicação não utiliza o usuário `root`.
- [ ] Credenciais anteriormente utilizadas foram trocadas.
- [ ] O servidor aponta o DocumentRoot para `public/`.
