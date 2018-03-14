/* map.js */

$(document).ready(function() {

	$('div.grid-cell').click(function() {
		
		$item = $(this);
		
		var is_block = parseInt($item.attr('im-a-block'));
		if(isNaN(is_block))is_block = 0;
		
		if(is_block == 0) {
			$item.attr('im-a-block',1);
		}
		else if(is_block == 1) {
			$item.attr('im-a-block',0);
		} 
		
		$.ajax({
			url:"script/interface.php"
			,data:{
					put:'set-block'
					,fk_warehouse:fk_warehouse
					,col:$item.attr('col')
					,row:$item.attr('row')
					,is_block:$item.attr('im-a-block')
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
		
		if(is_block == 0) {
			$item.css('background-color','#fff');
		}
		else {
			$item.css('background-color','#333');
		} 
		
		
	});
	
	
}