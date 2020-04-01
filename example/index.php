<?php
/**
 * @author Alex Milenin
 * @email  admin@azrr.info
 * @copyright Copyright (c)Alex Milenin (https://azrr.info/)
 */

$template = new \Azurre\Template\Engine(__DIR__ . '/templates');
try {
    echo $template->render('content.phtml', ['test' => 'OK']);
} catch (\Azurre\Template\Exception $e) {
    echo 'Template error: ' . $e->getMessage();
} catch (\Exception $e) {
    echo $e->getMessage();
}
