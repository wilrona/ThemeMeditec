<?php
/**
 * Created by IntelliJ IDEA.
 * User: user
 * Date: 01/05/2018
 * Time: 09:51
 */


$post_type = tr_post_type('FAQ');

$post_type->setIcon('ticket');
$post_type->setArgument('supports', ['title', 'editor']);
$post_type->setAdminOnly();

