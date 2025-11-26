<?php
/**
 * Error 400 template for test application
 */
?>
<h1>Bad Request</h1>
<?php if (isset($message)): ?>
    <p><?= h($message) ?></p>
<?php endif; ?>
