function init() {
	$("#guess").click(function (e) {
		var data = {"date": $("#date").val()};

		$.ajax({
			"url": "plugins/journaler/Journaler.php",
			"data": data,
			"type": "POST",
			"dataType": 'html',
			"success": function(data){
				//loaded(); 
				$("#body").val(data);
				showMessage(data);
			},
			"error": function(data){loaded(); showMessage(data);},
		});
	});
}