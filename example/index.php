<?php
/**
 * @author Alex Milenin
 * @email  admin@azrr.info
 * @copyright Copyright (c)Alex Milenin (https://azrr.info/)
 */

include '../vendor/autoload.php';
$template = new \Azurre\Template\Engine(__DIR__ . '/templates');
try {
    echo $template->render('module/content.phtml', ['username' => 'Username']);
//    echo $template->render('single.phtml');
} catch (\Azurre\Template\Exception $e) {
    echo 'Template error: ' . $e->getMessage();
} catch (\Exception $e) {
    echo $e->getMessage();
}
