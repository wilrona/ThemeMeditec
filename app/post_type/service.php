<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 01/05/2018
 * Time: 09:51
 */


$post_type = tr_post_type('Service', 'Services');

$post_type->setIcon('loop');
$post_type->setArgument('supports', ['title', 'editor']);

$box1 = tr_meta_box('information')->setLabel('Informations complementaires');
$box1->addPostType( $post_type->getId() );
$box1->setCallback(function(){

    $form = tr_form();

    echo $form->text('sous_title')->setLabel('Sous titre');
    echo $form->image('left_image')->setLabel('Image de gauche');
    echo $form->editor('right_content')->setLabel('Contenu de droite');
    echo $form->text('demande_devis')->setLabel('Formulaire de demande de devis');

});

