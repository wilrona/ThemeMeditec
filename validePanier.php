<?php

use Timber\Timber;

var_dump($_POST);

wp_redirect(get_the_permalink(tr_options_field('options.page_panier')));

die();