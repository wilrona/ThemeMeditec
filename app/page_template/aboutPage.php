<?php

//$home = (int) get_option('page_on_front');

$bloc_about = tr_meta_box('desc_about');
$bloc_about->setLabel('Informations');
$bloc_about->addScreen('page');
$bloc_about->setCallback(function (){

    $form = tr_form();

    echo $form->text('about_title')->setLabel('Titre');
    echo $form->text('about_subtitle')->setLabel('Sous Titre');
    echo $form->editor('about_desc')->setLabel('Content');

    echo $form->image('about_image_one')->setLabel('Image de gauche');
    echo $form->image('about_image_two')->setLabel('Image de droite');

});

$bloc_how = tr_meta_box('how_about');
$bloc_how->setLabel('Valeur de l\'entreprise');
$bloc_how->addScreen('page');
$bloc_how->setCallback(function(){
    $form = tr_form();

    echo $form->repeater('how_about')->setFields([
        $form->text('title')->setLabel('Titre'),
        $form->editor('content')->setLabel('Content'),
        $form->image('image')->setLabel('Image')
    ])->setLabel('');
});

$bloc_equipe = tr_meta_box('equipe_about');
$bloc_equipe->setLabel('Notre equipe');
$bloc_equipe->addScreen('page');
$bloc_equipe->setCallback(function(){

    $form = tr_form();
    echo $form->text('title_equipe')->setLabel('Titre');
    echo $form->wpEditor('content_equipe')->setLabel('Contenue');

    echo $form->repeater('liste_equipe')->setFields([
        $form->text('name')->setLabel('Nom Complet'),
        $form->editor('post')->setLabel('Poste ou Role'),
        $form->image('image')->setLabel('Photo')
    ])->setLabel('Liste de l\'equipe');

});


add_action('admin_head', function () use ($bloc_about, $bloc_how, $bloc_equipe) {
    if (get_page_template_slug(get_the_ID()) === 'about.php') :
        remove_post_type_support('page', 'editor');
    else:
        remove_meta_box($bloc_about->getId(), 'page', 'normal');
        remove_meta_box($bloc_how->getId(), 'page', 'normal');
        remove_meta_box($bloc_equipe->getId(), 'page', 'normal');
    endif;
});
