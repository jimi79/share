<html>
<head>
<script>


function uploadFile(file) {
	var xhr = new XMLHttpRequest(); 
	xhr.upload.onprogress = function(e) {
		var percentComplete = (e.loaded / e.total) * 100;
		if (percentComplete < 99) {
			document.getElementById("pasteOutput").innerHTML = "Uploaded " + percentComplete + "%";
		} else {
			document.getElementById("pasteOutput").innerHTML = "Almost done";
		}
	}; 

  var fd = new FormData();
	fd.append("data", file);

	if (pastePassword.value != '') {
		fd.append("password", pastePassword.value);
	}
	if (pastePassword.duration != '') {
		fd.append("duration", pasteDuration.value);
	} 
	xhr.onload = function() {
		if (xhr.status == 200) {
			document.getElementById("pasteOutput").innerHTML = "<a href='" + xhr.responseText + "' target='_blank'>" + xhr.responseText + "</a>";
		} else {
			alert("Error! Upload failed");
			document.getElementById("pasteOutput").innerHTML = 'Upload failed';
		}
	}; 
	xhr.onerror = function() {
		alert("Error! Upload failed. Can not connect to server.");
	};

	xhr.open("POST", "ajax_gui_upload.php", true);
	xhr.send(fd);
}

function handlePaste(e) {
	document.getElementById("pasteOutput").innerHTML = "Starting";
	ok = false;
	for (var i = 0 ; i < e.clipboardData.items.length ; i++) {
		var item = e.clipboardData.items[i];
		if (item.type.indexOf("image") -1) {
			if (item.getAsFile() != null) {
				uploadFile(item.getAsFile());
				ok = true;
			}
		}
	}
	if (!ok) {
		document.getElementById("pasteOutput").innerHTML = "Error";
		alert("Error, the thing you pasted isn't a file. It works if you copy paste a file or a picture.");
	}
}

</script>
</head>


<body>
	<?php
		require_once('lib/lib.php');
		alert_default_password_html();
	?>

	<fieldset>
		<legend>Usual upload button (you can drag n' drop)</legend>
		<div>
			<form class="box" method="post" action="gui_upload.php" enctype="multipart/form-data">
				Duration in minutes (link will work only in that duration):<input type="numeric" min="1" />	
				<br>Password:<input type="password" name="password" />
				<input type="file" name="data" required />
				<br/>
				<button type="submit">Upload</button>
			</form>
		</div>
	</fieldset>
	<fieldset>
		<legend>Copy paste</legend>
		 Start with filling the password etc before, as soon as you paste the link is created

		<br>Duration in minutes (link will work only in that duration):<input id="pasteDuration" type="numeric" min="1" />	
		<br>Password:<input type="password" id="pastePassword" name="password" />
		<div id="pasteTarget">
			<fieldset style="width: 200px; height:100px">
				<legend>click and past here</legend>
			</fieldset> 
		</div>
		<div id="pasteOutput">
		Link will appear here
		</div>
	</fieldset>
	<fieldset>
		<legend>Url shortener</legend>
		Enter the url
			<form class="box" method="post" action="gui_shortener.php" enctype="multipart/form-data">
			<input type="text" name="url" required />
			<br/>
			<button type="submit">Process</button>
		<form>
	</fieldset>
</body>

<script>
window.onload = function()  {
	document.getElementById("pasteTarget").addEventListener("paste", handlePaste);
}
</script> 

</html>
