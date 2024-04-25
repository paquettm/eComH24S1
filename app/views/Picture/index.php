<html>
<head>
	<title>Picture upload</title>
</head>
<body>
	<?php
		if($data['error'] != null){
			echo "<p>$data</p>";
		}

		foreach($pictures as $picture)
			echo "<img src='/uploads/$picture->filename'>"
	?>

	<h1>Upload a new picture</h1>
	<form method="post" enctype="multipart/form-data">
		Select an image file to upload:<input type="file" name="newPicture">
		<input type="submit" name="action">
	</form>

</body>
</html>
