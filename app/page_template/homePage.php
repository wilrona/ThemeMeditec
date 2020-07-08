<?php

//$home = (int) get_option('page_on_front');

$bloc_slider = tr_meta_box('slider_home');
$bloc_slider->setLabel('Slider');
$bloc_slider->addScreen('page');
$bloc_slider->setCallback(function (){

    $form = tr_form();

    echo $form->text('content_slider')->setLabel('Code du slider');

});
;

$bloc_service = tr_meta_box('service_home');
$bloc_service->setLabel('Nos Services');
$bloc_service->addScreen('page');
$bloc_service->setCallback(function (){

    $form = tr_form();

    echo $form->repeater('list_services')->setFields([

        $form->search('service')->setPostType('service')

    ])->setLabel('');

});


$bloc_catalogue = tr_meta_box('catalogue_home');
$bloc_catalogue->setLabel('Nos catalogues');
$bloc_catalogue->addScreen('page');
$bloc_catalogue->setCallback(function(){

    $form = tr_form();

    echo $form->text('titre_cat')->setLabel('Titre');
    echo $form->text('subtitre_cat')->setLabel('Sous titre');

    echo $form->repeater('list_catalogue')->setFields([

        $form->search('catalogue')->setTaxonomy('catalogue')

    ])->setLabel('');


});

$bloc_produit = tr_meta_box('produit_home');
$bloc_produit->setLabel('Nos produits');
$bloc_produit->addScreen('page');
$bloc_produit->setCallback(function(){
    $form = tr_form();
    echo $form->text('titre_pro')->setLabel('Titre');

    $options = [
        'Afficher 3 produits' => '3',
        'Afficher 6 produits' => '6'
    ];

    echo $form->select('nbre_produit')->setLabel('Nombre de produit a afficher')->setOptions($options);
});

add_action('admin_head', function () use ($bloc_slider, $bloc_service, $bloc_catalogue, $bloc_produit) {
    if (get_page_template_slug(get_the_ID()) === 'home.php') :
        remove_post_type_support('page', 'editor');
    else:
        remove_meta_box($bloc_slider->getId(), 'page', 'normal');
        remove_meta_box($bloc_service->getId(), 'page', 'normal');
        remove_meta_box($bloc_catalogue->getId(), 'page', 'normal');
        remove_meta_box($bloc_produit->getId(), 'page', 'normal');
    endif;
});
