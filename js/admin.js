$(init);

function init() {
	initBtns();
	initContent();
}

function initBtns() {
	$("#itemSortSel").change(showItemList);
}

function initContent() {
	showUserList();
	showCatList();
	showItemList();
}

/* users */

function showUserList() {
	$("#userEdit").load('inc/admin.php?do=showUserEdit');
	$("#userList").load('inc/admin.php?do=showUserList');
}

function showUserEdit(id) {
	$("#userEdit").load('inc/admin.php?do=showUserEdit&id='+id);
}

function doSaveUser() {
	var data = {};
	$("#userEdit input").each(function(){
		data[$(this).attr('name')] = $(this).val();
	});
	$.post('inc/admin.php?do=doSaveUser',data,function(reply){
		$("#infoDiv").text(reply).slideDown("slow",hideAjax);
		showUserList();
	});
}

function doAddUser() {
	var stop = false;
	var data = {};
	$("#userEdit input").each(function(){
		if ($(this).val().length == 0) stop = true;
		data[$(this).attr('name')] = $(this).val();
	});
	if (stop) {
		$("#infoDiv").text("fill all the fields").slideDown("slow",hideAjax); return;
	}
	$.post('inc/admin.php?do=doAddUser',data,function(reply){
		$("#infoDiv").text(reply).slideDown("slow",hideAjax);
		$("#userEdit input").val('');
		$("#userList").load('inc/admin.php?do=showUserList');
	});
}

function delUser(id) {
	if (confirm("Are you sure want to delete this user?")) {
		$.post('inc/admin.php?do=delUser',{id:id},function(reply){
			$("#infoDiv").text(reply).slideDown("slow",hideAjax);
			showUserList();
		});
	}
}

/* categories */

function showCatList() {
	$("#catEdit").load('inc/admin.php?do=showCatEdit');
	$("#catList").load('inc/admin.php?do=showCatList');
}

function showCatEdit(id) {
	$("#catEdit").load('inc/admin.php?do=showCatEdit&id='+id);
}

function doSaveCat() {
	if ($("#catEdit input[name='name']").val().length == 0) {
		$("#infoDiv").text("provide category name").slideDown("slow",hideAjax); return;
	}
	var data = {};
	$("#catEdit input").each(function(){
		data[$(this).attr('name')] = $(this).val();
	});
	$.post('inc/admin.php?do=doSaveCat',data,function(reply){
		$("#infoDiv").text(reply).slideDown("slow",hideAjax);
		showCatList();
		$("#itemEdit").load('inc/admin.php?do=showItemEdit');
	});
}

function doAddCat() {
	if ($("#catEdit input[name='name']").val().length == 0) {
		$("#infoDiv").text("provide category name").slideDown("slow",hideAjax); return;
	}
	var data = {};
	$("#catEdit input").each(function(){
		data[$(this).attr('name')] = $(this).val();
	});
	$.post('inc/admin.php?do=doAddCat',data,function(reply){
		$("#infoDiv").text(reply).slideDown("slow",hideAjax);
		$("#catEdit input").val('');
		$("#catList").load('inc/admin.php?do=showCatList');
		$("#itemEdit").load('inc/admin.php?do=showItemEdit');
	});
}

function delCat(id) {
	if (confirm("Are you sure want to delete this category?")) {
		$.post('inc/admin.php?do=delCat',{id:id},function(reply){
			$("#infoDiv").text(reply).slideDown("slow",hideAjax);
			showCatList();
			$("#itemEdit").load('inc/admin.php?do=showItemEdit');
		});
	}
}

function moveCatUp(id) {
	$.post('inc/admin.php?do=moveCatUp&id='+id,function(){
		showCatList();
	});
}

function moveCatDown(id) {
	$.post('inc/admin.php?do=moveCatDown&id='+id,function(){
		showCatList();
	});
}

/* items */

function showItemList() {
	var cat = $("#itemSortSel").val();
	$("#itemEdit").load('inc/admin.php?do=showItemEdit');
	$("#itemList").load('inc/admin.php?do=showItemList&cat='+cat);
}

function doAddItem() {
	if ($("#itemEdit input[name='name']").val().length == 0) {
		$("#infoDiv").text("provide item name").slideDown("slow",hideAjax); return;
	}
	var data = {};
	$("#itemEdit input").each(function(){
		data[$(this).attr('name')] = $(this).val();
	});
	data['cat'] = $("#itemEdit select").val();
	$.post('inc/admin.php?do=doAddItem',data,function(reply){
		$("#infoDiv").text(reply).slideDown("slow",hideAjax);
		$("#itemEdit input").val('');
		var cat = $("#itemSortSel").val();
		$("#itemList").load('inc/admin.php?do=showItemList&cat='+cat);
	});
}

function showItemEdit(id) {
	$("#itemEdit").load('inc/admin.php?do=showItemEdit&id='+id);
}
