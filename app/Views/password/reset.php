<?php

use App\Routes\Router;

?>
<h1>Definir nova senha</h1>
<?php if (!empty($errorMessage)) :
    ?><p><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p><?php
endif; ?>
<form method="post" action="<?= htmlspecialchars(Router::url('/password/reset'), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <label>Nova senha <input type="password" name="password" minlength="10" required autocomplete="new-password"></label>
    <label>Confirmar senha <input type="password" name="password_confirmation" minlength="10" required autocomplete="new-password"></label>
    <button type="submit">Alterar senha</button>
</form>
