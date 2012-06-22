
function showReg() {
	$("#loginDlg").load('inc/login.php?do=showReg',function(){
		Recaptcha.create("6LeeUwkAAAAAAA6dYtHFbu3YnpJIaT6ZUn3yKw5Z", "captchaDiv", {theme: "blue", callback: Recaptcha.focus_response_field});	
		var w = ($(window).width()/2)-100;
		var h = ($(window).height()/2)-200;
		$("#loginDlg").css('left',w+'px').css('top',h+'px').show();
	});
}

function showReset() {
	$("#loginDlg").load('inc/login.php?do=showReset',function(){
	});
}

function getCode() {
	var email = $("#loginDlg input[name='email']").val();
	$.post('inc/login.php?do=getCode',{email:email},function(reply){
		if (reply == 'SENT_OK') {
			$("#infoDiv").text('confirmation code sent').slideDown("slow",hideAjax);
			$("#doResetBtn").show();	
		} else {
			$("#infoDiv").text(reply).slideDown("slow",hideAjax);
			$("#getCodeBtn").show();
		}
	});
	$("#getCodeBtn").hide();
}

function doLogin() {
	if (($("#loginArea input[name='login']").val() == '') || ($("#loginArea input[name='pass']").val() == '')) {
		$("#infoDiv").text("login information is missing").slideDown("slow",hideAjax); return;
	}
	var data = {};
	$("#loginArea :input").each(function(){
		data[$(this).attr('name')]=$(this).val();
	});
	$.post('inc/login.php?do=doLogin',data,function(reply) {
		if (reply == 'LOGIN_OK')
			window.location.reload(true);
		else
			$("#infoDiv").text(reply).slideDown("slow",hideAjax);
	});
}

function doReg() {
	if (($("#loginDlg input[name='email']").val() == '') || ($("#loginDlg input[name='pass']").val() == '')) {
		$("#infoDiv").text("fill all neccessary fields").slideDown("slow",hideAjax); return;
	}
	if ($("#recaptcha_response_field").val() == '') {
		$("#infoDiv").text("provide captcha solution").slideDown("slow",hideAjax); return;
	}
	var data = {};
	$("#loginDlg :input").each(function(){
		data[$(this).attr('name')]=$(this).val();
	});
	$.post('inc/login.php?do=doReg',data,function(reply) {
		if (reply == 'REG_OK')
			window.location.reload(true);
		else
			$("#infoDiv").text(reply).slideDown("slow",hideAjax);
	});
}

function doLogout() {
	$.post('inc/login.php?do=doLogout',function(reply) {
		window.location.reload(true);  
	});
}

function doReset() {
	var data = {};
	$("#loginDlg :input").each(function(){
		data[$(this).attr('name')]=$(this).val();
	});
	$.post('inc/login.php?do=doReset',data,function(reply){
		if (reply == 'RESET_OK')
			window.location.reload(true);
		else
			$("#infoDiv").text(reply).slideDown("slow",hideAjax);
	});	
}
