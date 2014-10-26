<?php

/**
 * @author Lambert Portier
 * @copyright 2014
 */
include_once $_SERVER['DOCUMENT_ROOT'] . '/model/Vakantiehuis.php';

$code = 'CM12';
$vk = new Vakantiehuis();
$vk->setCode($code); 
$huis_id = $vk->getHuisID();
$vk->getDataHuis();
$vk->getAlikes();
$fotos = $vk->getAlikeFoto();
print_r($fotos);
?>