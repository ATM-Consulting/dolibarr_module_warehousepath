/* map.js */
/* TODO to php for trans */

$(document).ready(function() {

	$('div.grid-cell').click(function() {
		
		$item = $(this);
		
		var fk_warehouse = $item.closest('div[fk_warehouse]').attr('fk_warehouse');
		
		var l_fk_product = (typeof fk_product != 'undefined' ) ? fk_product : 0;
		
		var is_block = parseInt($item.attr('im-a-block'));
		if(isNaN(is_block))is_block = 0;
		
		if(l_fk_product>0 && is_block == 0) {
			alert('Block not a container');
			return false;
		}
		if(l_fk_product == 0 && is_block > 1) {
			alert('Block contains products : '+$item.attr('products'));
			return false;
		}
		
		
		
		if(l_fk_product>0) {
			if(is_block == 1) {
				$item.attr('im-a-block',2);
			}
			else if(is_block == 2) {
				
				var products = $item.attr('products').split(',');
				
				if(products.indexOf(l_fk_product.toString())!=-1) {
					$item.attr('im-a-block',1);/* TODO ça marchera pas */
					l_fk_product = 0;	
				}
				
			} 

		} 
		else {
			if(is_block == 0) {
				$item.attr('im-a-block',1);
			}
			else if(is_block == 1) {
				$item.attr('im-a-block',0);
			} 
			
		}
		
		$.ajax({
			url:"script/interface.php"
			,data:{
					put:'set-block'
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
	
	for(x in path) {
		
		pair = path[x];
		
		$map.find('div.grid-cell[col='+pair[0]+'][row='+pair[1]+']').addClass('walk-here');
	}
	
	
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
			
			var products = $item.attr('products').split(',');
			
			if(products.indexOf(l_fk_product.toString())!=-1)$item.css('background-color','#66ff66');
			else $item.css('background-color','#6ffff6');
			
			
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