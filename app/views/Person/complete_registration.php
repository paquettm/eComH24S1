<html>
<head>
	<title><?= $name ?> view</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>TODO: add several items under the same name
	<div id='container'>
		<form method='post' action='/Person/complete_registration'>
			<div class="form-group">
				<label>First name:<input type="text" class="form-control" name="first_name" placeholder="Jon" value="<?= $data->first_name ?>" /></label>
			</div>
			<div class="form-group">
				<label>Last name:<input type="text" class="form-control" name="last_name" placeholder="Doe" value="<?= $data->last_name ?>" /></label>
			</div>
			<div class="form-group">
				<label>Email address:<input type="email" class="form-control" name="email" placeholder="jondoe@email.com" value="<?= $data->email ?>" /></label>
			</div>
			<div class="form-group">
				Do you want to be included on our mailing list?
				<label><input type="radio" name="mailing_list" value="1" <?= ($data->mailing_list==1?'checked':'') ?> />Yes</label>
				<label><input type="radio" name="mailing_list" value="-1" <?= ($data->mailing_list==-1?'checked':'') ?> />No</label>
			</div>


			<div class="form-group">
					<label><input type="checkbox" name="publications[]" value="mailing_list" <?= ($data->mailing_list==true?'checked':'') ?>>Include me on the mailing list</label>
			</div>
			<div class="form-group">
				<label><input type="checkbox" name="publications[]" value="weekly_flyer" <?= ($data->weekly_flyer==true?'checked':'') ?>>Send me the weekly flyer</label>
			</div>



			<div class="form-group">
				<input type="submit" name="action" value="Register" />
			</div>
		</form>
	</div>
</body>
</html>