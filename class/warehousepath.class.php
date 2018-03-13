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

	static function getMap() {
        global $conf,$db;

        $res = $db->query("SELECT rowid,grid_col,grid_row,fk_warehouse FROM ".MAIN_DB_PREFIX."warehousepath WHERE entity=".$conf->entity);

        if($res === false){
            var_dump($db);exit;
        }

        $TMap=array();
        while($obj = $db->fetch_object($res)) {

            @$TMap[$obj->grid_row][$obj->grid_col] = $obj;

        }

        return $TMap;

	}

}
