jQuery(document).ready(function() {
	var $ = jQuery; // use noConflict version of jQuery as the short $ within this block

	storeIdOrder();

  $("#sortable")
	.disableSelection()
	.sortable( {
		placeholder: 'ui-sortable-highlight',
		stop: function() { storeIdOrder(); }
	} );

	function storeIdOrder() {
		var itemOrder = new Array();
		$(".dragitems").each( function(index) {
			itemOrder.push($(this).data("id"));
		} );
		console.log(itemOrder);
		$("#reorderElementTextsOrder").val( JSON.stringify(itemOrder) );
	}

} );
