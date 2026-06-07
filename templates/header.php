<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
wp_body_open();

use Jasanika\Core\ThemeRenderer;

$renderer = ThemeRenderer::getInstance();

if ($renderer) {
    $renderer->getHeaderRenderer()->render();
}