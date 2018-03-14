<?php

    require 'config.php';

    dol_include_once('/warehousepath/lib/warehousepath.lib.php');
    dol_include_once('/warehousepath/class/warehousepath.class.php');
    dol_include_once('/product/stock/class/entrepot.class.php');
    dol_include_once('/core/lib/stock.lib.php');
    
    llxHeader('', $langs->trans('MapWareHouse'),'','',0,0,array('/warehousepath/js/map.js'),array('/warehousepath/css/style.css') );

    $object = new Entrepot($db);
    $object->fetch(GETPOST('fk_warehouse'));
    
    $head = stock_prepare_head($object);
    dol_fiche_head($head, 'map', $langs->trans("Map"), -1, 'stock');
    
    $morehtmlref='<div class="refidno">';
    $morehtmlref.=$langs->trans("LocationSummary").' : '.$object->lieu;
    $morehtmlref.='</div>';
    
    
    
    dol_banner_tab($object, 'ref', '', 0, 'ref', 'ref', $morehtmlref);
    
    if($object->id>0) {
        warehousepath::showMap($object);
    }
    
    llxFooter();

