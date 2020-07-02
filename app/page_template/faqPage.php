<?php

//$home = (int) get_option('page_on_front');

$bloc_about = tr_meta_box('desc_faq');
$bloc_about->setLabel('Informations');
$bloc_about->addScreen('page');
$bloc_about->setCallback(function (){

    $form = tr_form();

    echo $form->text('faq_title')->setLabel('Titre');

});


add_action('admin_head', function () use ($bloc_about, $bloc_how, $bloc_equipe) {
    if (get_page_template_slug(get_the_ID()) === 'faq.php') :
        remove_post_type_support('page', 'editor');
    else:
        remove_meta_box($bloc_about->getId(), 'page', 'normal');
    endif;
});
