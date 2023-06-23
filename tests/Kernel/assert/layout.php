<?php

declare(strict_types=1);

if (!(isset($errorBlocking) && false === $errorBlocking)) {
    $message = $messageDefault ?? '';
}
?>

<div id="status-code"><?php echo $statusCode ?? 500; ?></div>

<div id="content">
    <p id="title"><?php echo $title ?? ''; ?></p>
    <p id="sub-title"><?php echo $code ?? 0; ?> <?php echo $message ?? ''; ?></p>
</div>