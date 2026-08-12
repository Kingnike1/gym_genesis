<?php

use App\Routes\Router;

?>
<h1>Recuperar senha</h1>
<?php if (!empty($successMessage)) :
    ?><p><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></p><?php
endif; ?>
<form method="post" action="<?= htmlspecialchars(Router::url('/password/forgot'), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <label>E-mail <input type="email" name="email" required autocomplete="email"></label>
    <button type="submit">Enviar instruções</button>
</form>
