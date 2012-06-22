$(upload_init);
var ol = $("div.overlay").overlay({oneInstance: true, api: true, closeOnClick: false, closeOnEsc: false, left: 'center', top: '15%'}); 

function upload_init() {
	$("#uploadify").uploadify({
		'uploader': 'mod/uploadify/uploadify.swf', 'script': 'mod/uploadify/uploadify.php', 'folder': 'uploads', 'multi': true, 'buttonText': 'Browse', 'cancelImg': 'mod/uploadify/cancel.png', 'queueID': 'upload-queue', 
		'onSelectOnce': function(e,d){ $("#upload-btn").show(); $("#uploaded-files").html(''); },
		'onComplete'	: function(e,q,f,r,d) { $("#uploaded-files").append(r);},
		'onAllComplete': function(e,d) { ol.load(); $("#upload-btn").hide(); }
	});	
	$("#file-list").load('inc/upload.php?do=showUploaded');
	$("#canvas").show();
}

function uploadFile(userid) {
	$("#uploadify").uploadifySettings('scriptData', {'userid':userid});
	$('#uploadify').uploadifyUpload();
}

function uploadCover(id) {
	var upload = $("button[gid='"+id+"']").upload({
		name: 'cover',
		action: 'inc/upload.php',
		enctype: 'multipart/form-data',
		params: {id:id},
		autoSubmit: true,
		onSubmit: function() {
			
		},
		onComplete: function(resp) {
			if (resp == 'ERR_EXT')
				alert('Only .jpg and .png file types allowed!');
			else {
				$("div.cover[gid='"+id+"']").html('');
				$("div.cover[gid='"+id+"']").html('<img src="'+resp+'"/>');	
				$("div.cover[gid='"+id+"']").prev().prev().hide();
			}
			
		}
	});
	$("button[gid='"+id+"']").text('Browse..');
}

function saveUpload() {
	$(".file-edit").each(function(){
		var data = {};
		$(this).find("input, select, textarea").each(function(){
			data[$(this).attr('name')] = $(this).val();
		});
		$.post('inc/upload.php?do=saveUpload',data);
	});
	ol.close();
	$("#file-list").load('inc/upload.php?do=showUploaded');
}

function editFile(id) {
	$("#files-area tr[gid='"+id+"']").load('inc/upload.php?do=editFile&id='+id,function(){
		var upload = $("#files-area tr[gid='"+id+"'] button").upload({
			name: 'cover',
			action: 'inc/upload.php',
			enctype: 'multipart/form-data',
			params: {id:id},
			autoSubmit: true,
			onComplete: function(resp) {
				if (resp == 'ERR_EXT')
					alert('Only .jpg and .png file types allowed!');
				else {
					$("#files-area tr[gid='"+id+"'] .img-upload").html('<img src="'+resp+'"/>');	
				}
		}
	});
	});
}

function delFile(id) {
	var s = $("#files-area tr[gid='"+id+"'] td:first-child").next().text();
	if (confirm("Delete "+s+" ?")) {
		$.post('inc/upload.php?do=delFile',{id:id},function(){
			$("#files-area tr[gid='"+id+"']").fadeOut('slow',function(){
				$("#files-area tr[gid='"+id+"']").remove();
			});
		});
	};
}

function saveFile(id) {
	var data = {};
	$("#files-area tr[gid='"+id+"'] td :input, #files-area tr[gid='"+id+"'] td select").each(function(){
		data[$(this).attr('name')] = $(this).val();
	});
	data['id'] = id;
	$.post('inc/upload.php?do=saveFile',data,function(){
		$("#files-area tr[gid='"+id+"']").load('inc/upload.php?do=showFile',{id:id});
	});
}

function cancelSave(id) {
	$("#files-area tr[gid='"+id+"']").load('inc/upload.php?do=showFile',{id:id});
}
