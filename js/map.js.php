<?php

    require '../config.php';
    dol_include_once('/warehousepath/class/warehousepath.class.php');

    echo 'var crossoverRate = '.(empty($conf->global->WHPATH_CROSSOVERRATE) ? '0.5' : (double)$conf->global->WHPATH_CROSSOVERRATE).';';
    echo 'var mutationRate = '.(empty($conf->global->WHPATH_MUTATIONRATE) ? '0.1' : (double)$conf->global->WHPATH_MUTATIONRATE).';';
    echo 'var populationSize = '.(empty($conf->global->WHPATH_POPULATIONSIZE) ? '50' : (double)$conf->global->WHPATH_POPULATIONSIZE).';';
    echo 'var maxGeneration = '.(empty($conf->global->WHPATH_MAXGENERATION) ? '50' : (double)$conf->global->WHPATH_MAXGENERATION).';';


?>

$(document).ready(function() {

	$('div.grid-cell').click(function() {

		$item = $(this);

		if(typeof fk_shipping != 'undefined' ) return false;

		var fk_warehouse = $item.closest('div[fk_warehouse]').attr('fk_warehouse');

		var l_fk_product = (typeof fk_product != 'undefined' ) ? fk_product : 0;

		var is_block = parseInt($item.attr('im-a-block'));
		if(isNaN(is_block))is_block = 0;

		if(l_fk_product>0 && is_block == 0) {
			alert('<?php echo $langs->transnoentities('BlockIsntAContainer') ?>');
			return false;
		}

		if(l_fk_product == 0 && is_block > 1) {
			alert('<?php echo $langs->transnoentities('BlockContainsProducts') ?> : '+$item.attr('products'));
			return false;
		}

		var action = 'set-block';

		if(is_block == <?php echo Warehousepath::FREE ?>) {
			$item.attr('im-a-block', l_fk_product>0 ? <?php echo Warehousepath::PRODUCT ?> : <?php echo Warehousepath::BLOCK ?>);
		}
		else if(is_block == <?php echo Warehousepath::PRODUCT ?>) {
			$item.attr('im-a-block',<?php echo Warehousepath::BLOCK ?>);
			action = 'delete-block';
		}
		else if(is_block == <?php echo Warehousepath::BLOCK ?>) {

			if(l_fk_product>0) {
				$item.attr('im-a-block',<?php echo Warehousepath::PRODUCT ?>);
				$item.attr('products',l_fk_product);
			}
			else {
				$item.attr('im-a-block',<?php echo Warehousepath::FREE ?>);
				action = 'delete-block';
			}

		}

		$.ajax({
			url:"script/interface.php"
			,data:{
					put:action
					,fk_warehouse:fk_warehouse
					,col:$item.attr('col')
					,row:$item.attr('row')
					,is_block:$item.attr('im-a-block')
					,fk_product:l_fk_product
			}

		}).done(function(data) {

		});




		drawCells();

	});

	drawCells();

});

function drawPath(fk_warehouse, X1, Y1, X2, Y2, step ) {

	path = getPath( fk_warehouse, X1, Y1, X2, Y2 );

	var $map = $('div.map[fk_warehouse='+fk_warehouse+']');

	for(x in path) {

		pair = path[x];

		if($map.find('div.grid-cell[col='+pair[0]+'][row='+pair[1]+']').html()=='') {
    		if(step && x == path.length - 1 ) {
    			$map.find('div.grid-cell[col='+pair[0]+'][row='+pair[1]+']').addClass('walk-step').append(step);
    		}
    		else {
    			$map.find('div.grid-cell[col='+pair[0]+'][row='+pair[1]+']').addClass('walk-here');
    		}
		}
	}

}

function getPath( fk_warehouse, X1, Y1, X2, Y2 ) {

	var $map = $('div.map[fk_warehouse='+fk_warehouse+']');

	var matrix = [];
	$map.find('div.grid-row').each(function(i, row) {

		var cols = [];

		$(row).find('div.grid-cell').each(function(j, col) {
			var type = $(col).attr('im-a-block');
			cols[j] = type == 1 || type == 2 ? 1 : 0;
		});

		matrix.push(cols);
	});

	matrix[Y1][X1]=0;
	matrix[Y2][X2]=0;

	var grid = new PF.Grid(matrix);

	var finder = new PF.AStarFinder();

	var path = finder.findPath(X1, Y1, X2, Y2, grid);

	return path;
}

function drawCells() {


	$('div.grid-cell').each(function(i,item) {

		$item = $(item);

		var l_fk_product = (typeof fk_product != 'undefined' ) ? fk_product : 0;

		var is_block = parseInt($item.attr('im-a-block'));
		if(isNaN(is_block))is_block = 0;

		if(is_block == 1) {
			$item.css('background-color','#333');
		}
		else if(is_block == 2) {

			if(l_fk_product>0) {
    			var products = $item.attr('products').split(',');

    			if(products.indexOf(l_fk_product.toString())!=-1)$item.css('background-color','#66ff66');
    			else $item.css('background-color','#6ffff6');
			}
			else if(typeof $item.attr('products') != 'undefined') {

				var count = $item.attr('products').split(',').length;
				if(count == 1) $item.css('background-color','#6ffff6');
				else if(count == 2) $item.css('background-color','#6dffd6');
				else if(count == 3) $item.css('background-color','#6bffb6');
				else if(count == 4) $item.css('background-color','#69ff96');
				else if(count == 5) $item.css('background-color','#67ff76');
				else $item.css('background-color','#66ff66');

			}

		}
		else if(is_block == 666) {
			$item.css('background-color','#ffff66');
		}
		else if(is_block == 999) {
			$item.css('background-color','#ff6666');
		}
		else {
			$item.css('background-color','#fff');
		}


	});

}

function mapOptimizeRoute(fk_warehouse, nodes) {
console.log('mapOptimizeRoute', nodes, nodes.length);

	for(origin in nodes) {
		durations[origin]=[];
		for(destination in nodes) {
			path = getPath(fk_warehouse, nodes[origin][2], nodes[origin][1], nodes[destination][2], nodes[destination][1]);
			distance = path.length - 1;
			durations[origin][destination] = distance;

		}

	}

	ga.getConfig();
    var pop = new ga.population();
    pop.initialize(nodes.length);
    var route = pop.getFittest().chromosome;

	var nb = route.length;

	var $map = $('div.map[fk_warehouse='+fk_warehouse+']');
	origin = 0;
	$map.find('div.grid-cell[col='+nodes[origin][2]+'][row='+nodes[origin][1]+']').addClass('walk-step').append('E');
	destination = nodes.length - 1;
	$map.find('div.grid-cell[col='+nodes[destination][2]+'][row='+nodes[destination][1]+']').addClass('walk-step').append('S');

    for(var i=0;i<nb-1; i++) {

		origin = route[i];
		destination = route[i+1];

    	drawPath(fk_warehouse, nodes[origin][2], nodes[origin][1], nodes[destination][2], nodes[destination][1] , i + 1);

    }


}

