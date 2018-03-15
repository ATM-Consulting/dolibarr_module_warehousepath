<?php

if (!class_exists('TObjetStd'))
{
	/**
	 * Needed if $form->showLinkedObjectBlock() is call
	 */
	define('INC_FROM_DOLIBARR', true);
	require_once dirname(__FILE__).'/../config.php';
}


class Warehousepath extends SeedObject
{

	public $table_element = 'warehousepath';

	public $element = 'warehousepath';

	const BLOCK = 1;
	const PRODUCT = 2;
	const START = 999;
	const END = 666;

	public function __construct($db)
	{
		global $conf,$langs;

		$this->db = $db;

		$this->fields=array(
		    'grid_col'=>array('type'=>'integer','index'=>true)
		    ,'grid_row'=>array('type'=>'integer','index'=>true)
		    ,'fk_warehouse'=>array('type'=>'integer','index'=>true)
		    ,'fk_product'=>array('type'=>'integer','index'=>true)
		    ,'type'=>array('type'=>'integer','index'=>true)
		    ,'entity'=>array('type'=>'integer','index'=>true)
		);

		$this->init();

		$this->entity = $conf->entity;

		$this->type = self::BLOCK;

	}

	static function getMap($fk_wh) {
        global $conf,$db;

        $res = $db->query("SELECT rowid,grid_col,grid_row,fk_warehouse,fk_product,type FROM ".MAIN_DB_PREFIX."warehousepath
            WHERE entity=".$conf->entity. " AND fk_warehouse=".(int)$fk_wh );

        if($res === false){
            var_dump($db);exit;
        }

        $TMap=array();
        while($obj = $db->fetch_object($res)) {

            if(empty($TMap[$obj->grid_row][$obj->grid_col])) @$TMap[$obj->grid_row][$obj->grid_col] = $obj;

            if($obj->type == Warehousepath::PRODUCT) {

                if(empty($TMap[$obj->grid_row][$obj->grid_col]->products)) $TMap[$obj->grid_row][$obj->grid_col]->products = $obj->fk_product;
                else $TMap[$obj->grid_row][$obj->grid_col]->products .=','. $obj->fk_product;

            }

        }

        return $TMap;

	}

	static function setBlock($fk_warehouse, $row,$col,$is_block = true, $type = self::BLOCK, $fk_product = 0) {
	    global $db,$conf,$user;

	    if($is_block) {

	        if($type == self::START || $type == self::END) {

	            $res= $db->query( "DELETE FROM ".MAIN_DB_PREFIX."warehousepath
                WHERE type = ".(int)$type." AND fk_warehouse=".$fk_warehouse." AND entity=".$conf->entity);

	        }

	        $wp = new Warehousepath($db);
	        $wp->grid_col = $col;
	        $wp->grid_row = $row;
	        $wp->fk_warehouse = $fk_warehouse;
            $wp->entity = $conf->entity;
            $wp->fk_product = $fk_product;
            $wp->type = $type;

	        $wp->create($user);

	    }
	    else {
	        $sql = "DELETE FROM ".MAIN_DB_PREFIX."warehousepath
                WHERE grid_col=".(int)$col." AND grid_row=".(int)$row." AND type = ".(int)$type."
                        AND fk_warehouse=".$fk_warehouse." AND entity=".$conf->entity;

	        if($fk_product>0) $sql.=" AND fk_product = ".$fk_product;

	       $res= $db->query($sql);

	    }

	    if($res===false) {
	        var_dump($db);exit;
	    }

	    return 1;

	}

	static function showMap(&$wh) {
        global $db;

	    $res = $db->query("SELECT cols,rows,start_point,end_point FROM ".MAIN_DB_PREFIX."entrepot WHERE rowid=".$wh->id);
        $obj = $db->fetch_object($res);
        $cols = $obj->cols;
        $rows = $obj->rows;
        if($obj->start_point) $start_point = explode(',',$obj->start_point);
        if($obj->end_point) $end_point = explode(',',$obj->end_point);

	    if(empty($cols)) $cols = 5;
	    if(empty($rows)) $rows = 5;
	    if(empty($start_point)) $start_point = array(0,0);
	    if(empty($end_point)) $end_point = array(0,0);

	    $TMap = Warehousepath::getMap($wh->id);

	    if(empty($TMap[$start_point[0]][$start_point[1]])) $TMap[$start_point[0]][$start_point[1]] = new stdClass();
	    $TMap[$start_point[0]][$start_point[1]]->start = true;

	    if(empty($TMap[$end_point[0]][$end_point[1]])) $TMap[$end_point[0]][$end_point[1]] = new stdClass();
	    $TMap[$end_point[0]][$end_point[1]]->end = true;

	    for($i = 0; $i<$rows; $i++) {
	        echo '<div class="map" fk_warehouse='.(int)$wh->id.'>';
	        echo '<div class="grid-row">';
	        for($j = 0; $j<$cols; $j++) {
	            echo '<div class="grid-cell" col="'.$j.'" row="'.$i.'" title="('.$i.','.$j.')" ';
	            if(isset($TMap[$i][$j])) {

	                if(!empty($TMap[$i][$j]->end)) {

	                    echo ' im-a-block="'.self::END.'" '; // non switchable block
	                }
	                else if(!empty($TMap[$i][$j]->start)) {
	                    echo ' im-a-block="'.self::START.'" '; // non switchable block
	                }
	                else if(!empty($TMap[$i][$j]->products)) {
	                    echo ' products="'.$TMap[$i][$j]->products.'" ';
	                    echo ' im-a-block="'.self::PRODUCT.'" '; // non switchable block
	                }
	                else {

	                    echo ' im-a-block="'.self::BLOCK.'" ';
	                }
	            }
	            echo ' ></div>';

	        }
	        echo '</div>';
	        echo '</div>';
	    }

	}


}
