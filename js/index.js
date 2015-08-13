function init() {
	$(".edit-entry").click(function (e) {
		var entry_id = $(this).attr('data-entry-id');
		
		$("#entry-"+entry_id + " .body").tinymce({
			id: "tens",
			toolbar: false,
			statusbar: false,
 			menubar: false,
 			inline: true,
 			auto_focus: "entry-body-"+entry_id
 		})

 		$("#entry-save-"+entry_id).show();


		e.stopPropagation();
		return false;
	});
}