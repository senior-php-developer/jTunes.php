$(init);

function init() {
	$.ajaxSetup({
		beforeSend: function(){$("#loader").show();},
    success: function(){$("#loader").hide();},
    complete: function(){$("#loader").hide();}
  });
}

function hideAjax() {
  setTimeout('$("#infoDiv").slideUp("slow");',2500);
}

function loadPage(page) {
	location.href='index.php?mod='+page;
}