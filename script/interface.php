<?php

    require '../config.php';

    dol_include_once('/warehousepath/class/warehousepath.class.php');


    $get = GETPOST('get');
    $put = GETPOST('put');

    if($put === 'set-block') {
        $fk_product=GETPOST('fk_product');
        $type = $fk_product>0 ? Warehousepath::PRODUCT : Warehousepath::BLOCK;

        echo Warehousepath::setBlock(GETPOST('fk_warehouse'), GETPOST('row'), GETPOST('col'), GETPOST('is_block') > 0, $type, $fk_product);

    }