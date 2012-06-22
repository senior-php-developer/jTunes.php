<? if (!$CURUSER) die; ?>

<div id="left-column">
	<div id="artists" class="section">
		<h5>Artists</h5>
		<div id="artist-list"></div>
	</div>
	<div id="tags" class="section">
		<h5>Tag Cloud</h5>
	</div>
</div>
<div id="central-column">

	<div id="download" class="section">
		<h5>Downloads</h5><br/>
		<? print($activation.' '.$categories); ?>
		
		<div id="file-list"></div>
	</div>
	<div class="section">
		<h5>Most Popular Downloads</h5><br/>
		<div id="popular"></div>
	</div>
</div>
<div class="clr"></div>

<div class="overlay"></div>

<link rel="stylesheet" type="text/css" href="css/music.css"/>
<script type="text/javascript" src="js/login.js"></script>
<script type="text/javascript" src="js/overlay.js"></script>
<script type="text/javascript" src="js/music.js"></script>