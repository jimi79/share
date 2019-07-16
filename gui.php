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
	// These extra params aren't necessary but show that you can include other data.
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
	//xhr.setRequestHeader("Content-Type", file.type);
	console.log(file);
	//xhr.send(file);
	xhr.send(fd);
}

function handlePaste(e) {
	for (var i = 0 ; i < e.clipboardData.items.length ; i++) {
		var item = e.clipboardData.items[i];
		if (item.type.indexOf("image") -1) {
			uploadFile(item.getAsFile());
		} else {
			console.log("Discardingimage paste data");
		}
	}
}

</script>
</head>


<body>
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
</body>

<script>
window.onload = function()  {
	document.getElementById("pasteTarget").addEventListener("paste", handlePaste);
}
</script> 

</html>
