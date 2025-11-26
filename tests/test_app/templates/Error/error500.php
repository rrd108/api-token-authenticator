<?php
/**
 * Error 500 template for test application
 */
?>
<h1>An Internal Error Has Occurred</h1>
<?php if (isset($message)): ?>
    <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
