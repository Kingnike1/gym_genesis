# Runbook de produção

## Sinais de saúde
- `/health`: processo HTTP vivo, sem dependência de banco.
- `/ready`: aplicação pronta e banco acessível.
- logs PSR-3 em stderr com `request_id`.

Monitore disponibilidade, taxa de 5xx, latência, uso de CPU/memória/disco, conexões/latência do banco, falhas de pagamento/webhook e volume de tentativas de login/reset.

## Alertas iniciais
Defina alertas quando `/ready` falhar repetidamente, houver aumento sustentado de 5xx, latência superar o limite operacional, disco/volume de banco se aproximar da capacidade ou backups deixarem de ser produzidos. Ajuste limites após observar a carga real.

## Backup
Execute `bash scripts/backup.sh` por agenda do provedor/cron em host protegido. O diretório de backup deve residir em armazenamento criptografado e preferencialmente fora do mesmo host do banco. Monitore sucesso, tamanho e idade do último backup.

## Restore
Nunca teste restore diretamente em produção. Restaure primeiro em ambiente isolado usando `CONFIRM_RESTORE=YES bash scripts/restore.sh <arquivo>`, valide migrations/dados e documente o resultado.

## Incidente
1. Identifique request IDs e período afetado.
2. Preserve logs e evidências.
3. Se necessário, coloque integrações mutáveis em pausa no provedor.
4. Corrija via hotfix derivado de `main` quando o incidente estiver em produção.
5. Valide em staging.
6. Publique e monitore recuperação.
7. Registre causa, impacto, correção e ações preventivas.

## Rollback
Prefira reimplantar a imagem/tag anterior. Migrations destrutivas não devem ser revertidas automaticamente; avalie compatibilidade do esquema antes de rollback da aplicação. Se uma migration for incompatível, use migration corretiva e restore somente quando necessário e validado.

## Release
`main` representa produção. Antes da promoção: CI verde, staging validado, migrations testadas, backups recentes/restauráveis, changelog atualizado e plano de rollback conhecido.
