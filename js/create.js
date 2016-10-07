function init() {
	autocomplete("#tags", tags, ",");
	
	$("#guess").click(function () {
		getData("plugins/journaler/Journaler.php", "body");
	});

	$("#show").click(function () {
		getData("plugins/journaler/show.php","what-i-did");
	});
}

function getData (url, id) {
	var data = {"date": $("#date").val()};

	$.ajax({
		"url": url,
		"data": data,
		"type": "POST",
		"dataType": 'html',
		"success": function(data){
			//loaded(); 
			if(id == "body") {
				// Make sure that if data is already there, it doesn't get overwritten.
				if($("#body").val()) if(!confirm("Entry already has some content. Replace with auto-generated content?")) return;
				$("#body").val(data);
			}
			else $("#" + id).html(data);
			showMessage(data);
		},
		"error": function(data){loaded(); showMessage(data);},
	});
}