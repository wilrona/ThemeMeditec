<?php
/* Template Name: Contact page */

use Timber\Timber;

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

Timber::render(  'page-contact.twig' , $context );