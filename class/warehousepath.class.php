<?php

if (!class_exists('TObjetStd'))
{
	/**
	 * Needed if $form->showLinkedObjectBlock() is call
	 */
	define('INC_FROM_DOLIBARR', true);
	require_once dirname(__FILE__).'/../config.php';
}


class warehousepath extends SeedObject
{

	public $table_element = 'warehousepath';

	public $element = 'warehousepath';

	public function __construct($db)
	{
		global $conf,$langs;

		$this->db = $db;

		$this->fields=array(
		    'grid_col'=>array('type'=>'integer','index'=>true)
		    ,'grid_row'=>array('type'=>'integer','index'=>true)
		    ,'fk_warehouse'=>array('type'=>'integer','index'=>true)
		    ,'entity'=>array('type'=>'integer','index'=>true)
		);

		$this->init();

		$this->entity = $conf->entity;
	}

	static function getMap($fk_wh) {
        global $conf,$db;

        $res = $db->query("SELECT rowid,grid_col,grid_row,fk_warehouse FROM ".MAIN_DB_PREFIX."warehousepath
            WHERE entity=".$conf->entity. " AND fk_warehouse=".(int)$fk_wh );

        if($res === false){
            var_dump($db);exit;
        }

        $TMap=array();
        while($obj = $db->fetch_object($res)) {

            @$TMap[$obj->grid_row][$obj->grid_col] = $obj;

        }

        return $TMap;

	}
	
	static function setBlock($fk_warehouse, $row,$col,$is_block = true) {
	    global $db,$conf;
	    
	    if($is_block) {
	       $res = $db->query("INSERT INTO ".MAIN_DB_PREFIX."warehousepath
                (grid_col,grid_row,fk_warehouse,entity) VALUES (".(int)$col.",".(int)$row.",".(int)$fk_warehouse.",".(int)$conf->entity.")
                 ");    
	       
	    }
	    else {
	       $res= $db->query("DELETE FROM ".MAIN_DB_PREFIX."warehousepath
                WHERE grid_col=".(int)$col." AND grid_row=".(int)$row." AND fk_warehouse=".$fk_warehouse." AND entity=".$conf->entity."
                 ");
	        
	    }
	    
	    if($res===false) {
	        var_dump($db);exit;
	    }
	    
	    return 1;
	    
	}
	
	static function showMap(&$wh) {
	    
	    if(empty($cols)) $cols = 20;
	    if(empty($row)) $row = 20;
	    
	    $TMap = warehousepath::getMap($wh->id);
	    
	    echo '<script type="text/javascript">
            var fk_warehouse = '.(int)$wh->id.';
        </script>';
	    
	    for($i = 0; $i<$row; $i++) {
	        echo '<div class="grid-row">';
	        for($j = 0; $j<$cols; $j++) {
	            echo '<div class="grid-cell" col="'.$j.'" row="'.$i.'" ';
	            if(isset($TMap[$i][$j])) {
	                if(!empty($TMap[$i][$j]->products)) {
	                    echo ' products="'.$TMap[$i][$j]->products.'" ';
	                    echo ' im-a-block="2" '; // non switchable block
	                }
	                else {
	                    echo ' im-a-block="1" ';
	                }
	            }
	            echo ' ></div>';
	            
	        }
	        echo '</div>';
	    }
	    
	}
	

}
