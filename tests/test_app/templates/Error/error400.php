<?php

/**
 * Error 400 template for test application
 */

?>
<h1>Bad Request</h1>
<?php if (isset($message)) : ?>
    <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
