/* map.js */
/* TODO to php for trans */
$(document).ready(function() {

	$('div.grid-cell').click(function() {
		
		$item = $(this);
		
		var fk_warehouse = $item.closest('div[fk_warehouse]').attr('fk_warehouse');
		
		var l_fk_product = (fk_product) ? fk_product : 0;
		
		var is_block = parseInt($item.attr('im-a-block'));
		if(isNaN(is_block))is_block = 0;
		
		if(l_fk_product>0 && is_block == 0) {
			alert('Block not a container');
			return false;
		}
		
		if(l_fk_product>0) {
			if(is_block == 1) {
				$item.attr('im-a-block',2);
			}
			else if(is_block == 2) {
				$item.attr('im-a-block',1);
				l_fk_product = 0;
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

function drawCells() {
	
	
	$('div.grid-cell').each(function(i,item) {
		
		$item = $(item);
		
		var is_block = parseInt($item.attr('im-a-block'));
		if(isNaN(is_block))is_block = 0;
		
		if(is_block == 1) {
			$item.css('background-color','#333');
		}
		else if(is_block == 2) {
			$item.css('background-color','#66ff66');
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