<html>
<head>
	<title><?= $name ?> view</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>
	<div id='container'>
		Hello <?= $data['person_name'] ?>! <!-- using the data sent to the view method in the $data local variable -->
		Here are some values:
		<?php
		foreach ($data['numbers'] as $key => $value) {
			echo "$key => $value<br>";//'' surround literals "" surround 'fStrings'
		}

		?>
		<dl>
			<dt>First name</dt>
			<dd><?=$data['profile']['first_name'] ?></dd>
			<dt>Last name</dt>
			<dd><?=$data['profile']['last_name'] ?></dd>
		</dl>
	</div>
</body>
</html>