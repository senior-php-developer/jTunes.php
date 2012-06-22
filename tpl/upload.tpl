<? if (!$CURUSER or $CURUSER['class'] >= 2) die; ?>

<div id="upload-area" class="section">
	<div id="browse-btn"><input id="uploadify" name="uploadify" type="file" /></div><img id="upload-btn" src="img/upload-btn.png" class="ptr hid" onclick="uploadFile(<?=$CURUSER[id]?>)"/>
	<div class="clr"></div>
	<div id="upload-queue"></div>
</div>

<div id="files-area" class="section">
	<h5>Uploaded Files</h5>
	<div id="file-list"></div>
</div>
<div class="clr"></div>

<div class="overlay"><div id="uploaded-files"></div><br/><center><button class="btn" onclick="saveUpload()">Save Changes</button></div>

<link rel="stylesheet" type="text/css" href="css/upload.css"/>
<link rel="stylesheet" type="text/css" href="mod/uploadify/uploadify.css"/>
<script type="text/javascript" src="mod/uploadify/swfobject.js"></script>
<script type="text/javascript" src="mod/uploadify/uploadify.js"></script>
<script type="text/javascript" src="js/overlay.js"></script>
<script type="text/javascript" src="js/ocupload.js"></script>
<script type="text/javascript" src="js/upload.js"></script>
