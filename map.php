<?php

    require 'config.php';

    dol_include_once('/warehousepath/lib/warehousepath.lib.php');
    dol_include_once('/warehousepath/class/warehousepath.class.php');

    llxHeader('', $langs->trans('MapWareHouse'),'','',0,0,array('/warehousepath/js/map.js'),array('/warehousepath/css/style.css') );

    showMap();

    llxFooter();

