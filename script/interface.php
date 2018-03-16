<?php

    require '../config.php';

    dol_include_once('/warehousepath/class/warehousepath.class.php');


    $get = GETPOST('get');
    $put = GETPOST('put');

    if($put === 'set-block') {
        $fk_product=GETPOST('fk_product');
        $type = $fk_product>0 ? Warehousepath::PRODUCT : Warehousepath::BLOCK;

        echo Warehousepath::setBlock(GETPOST('fk_warehouse'), GETPOST('row'), GETPOST('col'), $type, $fk_product);

    }
    else if($put == 'delete-block') {
        $fk_product=GETPOST('fk_product');
        $type = $fk_product>0 ? Warehousepath::PRODUCT : Warehousepath::BLOCK;

        echo Warehousepath::deleteBlock(GETPOST('fk_warehouse'), GETPOST('row'), GETPOST('col'), $type, $fk_product);
    }