<html lang="en">
<head>
	<title>Import File</title>
	<link rel="stylesheet" href="css/bootstrap.min.css" >
</head>
<body>
	<div class="container">
		<form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ URL::to('importExcel') }}" 
		class="form-horizontal" method="post" enctype="multipart/form-data">
		 {{ csrf_field() }}
			<input type="file" name="import_file" />
			<button class="btn btn-primary">Import File</button>
		</form>
	</div>
</body>
</html>