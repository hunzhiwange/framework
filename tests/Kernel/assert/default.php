<?php

declare(strict_types=1);

/**
 * 默认模板
 */
$title = $type ?? 'Whoops!';

if (!isset($messageDefault)) {
    $messageDefault = 'Unknown error.';
}

if (isset($file, $line)) {
    $title .= sprintf('<div class="file">#FILE %s #LINE %s</div>', $file, $line);
}

require __DIR__.'/layout.php';
