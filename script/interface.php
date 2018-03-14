<?php 

    require '../config.php';
    
    dol_include_once('/warehousepath/class/warehousepath.class.php');
    
    
    $get = GETPOST('get');
    $put = GETPOST('put');
    
    if($put === 'set-block') {
        
        echo Warehousepath::setBlock(GETPOST('fk_warehouse'), GETPOST('row'), GETPOST('col'), GETPOST('is_block') == 1);
        
    }