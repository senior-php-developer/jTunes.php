$(home_init);
var ol = $("div.overlay").overlay({oneInstance: true, api: true, closeOnClick: false, closeOnEsc: false, left: 'center', top: '15%'}); 

function home_init() {
	$("#file-list").load('inc/music.php?do=showFiles');
	$("#artist-list").load('inc/music.php?do=showArtists');
	$("#popular").load('inc/music.php?do=loadPopular');
}

function changeCat() {
	var v = $("#cat").val();
	if (v == "All") {
		$("#file-list tr").each(function(){
			$(this).removeClass('hid');
		});
	} else
	$("#file-list tr").each(function(){
		if ($(this).find(".desc-w").next().text() != v)
			$(this).addClass('hid');
		else
			$(this).removeClass('hid');
	});
}

function changeAct() {
	var v = $("#act").val();
	if (v == 0) {
		$("#file-list tr").each(function(){
			$(this).removeClass('hid');
		});
	} else
	$("#file-list tr").each(function(){
		if ($(this).find(".desc-w").next().next().text() != v)
			$(this).addClass('hid');
		else
			$(this).removeClass('hid');
	});
}

function loadArtist(artist) {
	$("#file-list").load('inc/music.php?do=loadArtist',{artist:artist});
}

function applyFile(id) {
	$.post('inc/music.php?do=applyFile',{id: id},function(r){
		$(".overlay").html(r);	
		ol.load();
	});
}

function loadAll() {
	$("#file-list").load('inc/music.php?do=showFiles');
}

function confirmFile(id) {
	$.post('inc/music.php?do=confirmFile',{id: id},function(r){
		$(".overlay").html(r);
		$("#file-list").load('inc/music.php?do=showFiles');	
	});
}

function cancelFile(id) {
	$.post('inc/music.php?do=cancelFile',{id:id},function(){
		$("#file-list").load('inc/music.php?do=showFiles');
	});
}

function loadTune(id) {
	$("#file-list").load('inc/music.php?do=loadTune',{id:id});
}
