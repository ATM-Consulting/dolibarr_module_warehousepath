<?php

    require 'config.php';

    dol_include_once('/warehousepath/lib/warehousepath.lib.php');
    dol_include_once('/warehousepath/class/warehousepath.class.php');
    dol_include_once('/product/stock/class/entrepot.class.php');
    dol_include_once('/core/lib/stock.lib.php');
    dol_include_once('/product/class/product.class.php');
    dol_include_once('/core/lib/product.lib.php');

    llxHeader('', $langs->trans('MapWareHouse'),'','',0,0,array('/warehousepath/js/map.js'),array('/warehousepath/css/style.css') );


    if(GETPOST('fk_product')) {

        $product = new Product($db);
        $product->fetch(GETPOST('fk_product'));
        $product->load_stock();

        $head = product_prepare_head($product);
        dol_fiche_head($head, 'map', $langs->trans("Map"), -1, 'product');
        dol_banner_tab($product, 'ref');

        ?>
        <script type="text/javascript">
			var fk_product = <?php echo $product->id ?>;
        </script>
        <?php

        foreach($product->stock_warehouse as $fk_wh=>$data) {

            card($fk_wh, false);

        }

    }
    else {
        card(GETPOST('fk_warehouse'));
    }


    llxFooter();

    function card($fk_wh, $dolbanner=true) {
        global $db,$conf,$user,$langs;


        $wh = new Entrepot($db);
        $wh->fetch($fk_wh);
        if($wh->id>0) {
            if($dolbanner) {

                $head = stock_prepare_head($wh);
                dol_fiche_head($head, 'map', $langs->trans("Map"), -1, 'stock');

                $morehtmlref='<div class="refidno">';
                $morehtmlref.=$langs->trans("LocationSummary").' : '.$wh->lieu;
                $morehtmlref.='</div>';

                dol_banner_tab($wh, 'ref', '', 0, 'ref', 'ref', $morehtmlref);

            }
            else {
                echo '<br />'.$wh->getNomUrl(1).'<br />';
            }


            Warehousepath::showMap($wh);
        }



    }