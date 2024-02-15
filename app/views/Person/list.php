<html>
<head>
	<title><?= $name ?> view</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>TODO: add several items under the same name
	<div id='container'>
		<table>
			<tr><th>first_name</th><th>last_name</th><th>email</th><th>weekly_flyer</th><th>mailing_list</th></tr>

		<?php
		foreach($data as $person){
			echo "<tr><td>$person->first_name</td><td>$person->last_name</td><td>$person->email</td><td>$person->weekly_flyer</td><td>$person->mailing_list</td></tr>";
		}
		?>
		</table>

	</div>
</body>
</html>