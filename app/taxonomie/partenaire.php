<?php

$part = tr_taxonomy('Partenaire', 'Partenaires');
$part->setHierarchical();
$part->addPostType('produit');
$part->setMainForm(function(){
    $form = tr_form();
    echo $form->image('image')->setLabel('Image du partenaire');
    echo $form->checkbox('show_onliste')->setLabel('Afficher dans la liste des partenaires ?');
    echo $form->text('website')->setLabel('Url du site du partenaire');
});

