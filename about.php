<?php
/* Template Name: About page */

use Timber\Timber;

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

Timber::render(  'page-about.twig' , $context );