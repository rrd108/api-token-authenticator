<?php
/**
 * Error 500 template for test application
 */
?>
<h1>An Internal Error Has Occurred</h1>
<?php if (isset($message)): ?>
    <p><?= h($message) ?></p>
<?php endif; ?>
