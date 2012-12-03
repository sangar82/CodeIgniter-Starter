$(document).ready(function() {
  $(function() {

		$( "#tcategories tbody" ).sortable({

      start: function (event, ui) {
        ui.placeholder.html("<tr><td colspan='5'>&nbsp;</td></tr>");
      },
      forcePlaceholderSize:true,
			update: function(event, ui){
				saveOrderClick();
			}
		});

		$( "#tcategories tbody" ).disableSelection();
	});
});

function saveOrderClick() {

    // ----- Retrieve the li items inside our sortable list
    var items = $("#tcategories tbody tr");

    var linkIDs = [items.size()];
    var index = 0;

    // ----- Iterate through each li, extracting the ID embedded as an attribute
    items.each(
        function(intIndex) {
            linkIDs[index] = $(this).attr("id");
            index++;
        });

      var stringcat = linkIDs.join(",");

      $.ajax({

       type: "POST",
       url: "/admin/categories/update_order_categories",
       data: "categories="+stringcat,
       success: function(msg){
       }
      });
     

    //$get("<%=txtExampleItemsOrder.ClientID %>").value = linkIDs.join(",");
}
