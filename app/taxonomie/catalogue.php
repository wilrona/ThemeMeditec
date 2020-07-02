<?php

$tax = tr_taxonomy('Catalogue', 'Catalogues');
$tax->setHierarchical();
$tax->addPostType('produit');
$tax->setMainForm(function(){
    $form = tr_form();
    echo $form->image('image')->setLabel('Image du catalogue');
});

