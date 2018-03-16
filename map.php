<?php

    require 'config.php';

    dol_include_once('/warehousepath/lib/warehousepath.lib.php');
    dol_include_once('/warehousepath/class/warehousepath.class.php');
    dol_include_once('/product/stock/class/entrepot.class.php');
    dol_include_once('/core/lib/stock.lib.php');
    dol_include_once('/product/class/product.class.php');
    dol_include_once('/core/lib/product.lib.php');

    llxHeader('', $langs->trans('MapWareHouse'),'','',0,0,array('/warehousepath/js/map.js.php','/warehousepath/js/ga.js','/warehousepath/lib/pathfinding/pathfinding-browser.min.js'),array('/warehousepath/css/style.css') );

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

            card($fk_wh, $product->id, 0,false);

        }

    }
    else {
        card(GETPOST('fk_warehouse'),0,GETPOST('fk_shipping'));
    }


    llxFooter();

    function card($fk_wh, $fk_product = 0,$fk_shipping = 0, $dolbanner=true) {
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


            Warehousepath::showMap($wh, $fk_product);

            if($fk_shipping>0) {

                   echo '<div style="float:left"><ul id="products">';

                   dol_include_once('/expedition/class/expedition.class.php');

                   $e=new Expedition($db);
                   $e->fetch($fk_shipping);

                   $TProduct = array();

                   foreach($e->lines as $line) {
                        $TProduct[] = $line->fk_product;
                   }

                   echo '</ul></div>';


                   $position = Warehousepath::getPath($wh, $TProduct);

                   $nb = count($position);
                   var_dump($position,$nb);
            	echo '<script type="text/javascript">'."\n";
            	echo 'mapOptimizeRoute('.json_encode($position).');'."\n";

				/*for($i=1;$i<$nb;$i++) {
				        echo ' drawPath('.$wh->id.','.$position[$i-1][2].','.$position[$i-1][1].','.$position[$i][2].','.$position[$i][1].');'."\n";
				}*/

				echo '</script>';
            }



        }



    }