/* map.js */
$(document).ready(function() {

	$('div.grid-cell').click(function() {
		
		$item = $(this);
		
		var fk_wh = parseInt($item.attr('fk-warehouse'));
		if(isNaN(fk_wh))fk_wh = 0;
		
		if(fk_wh == 0) {
			$item.attr('fk-warehouse',1);
		}
		else {
			$item.attr('fk-warehouse',0);
		} 
		
		drawCells();
		
	});
	
	drawCells();
	
});

function drawCells() {
	
	
	$('div.grid-cell').each(function(i,item) {
		
		$item = $(item);
		
		var fk_wh = parseInt($item.attr('fk-warehouse'));
		if(isNaN(fk_wh))fk_wh = 0;
		
		if(fk_wh == 0) {
			$item.css('background-color','#fff');
		}
		else {
			$item.css('background-color','#333');
		} 
		
		
	});
	
	
}