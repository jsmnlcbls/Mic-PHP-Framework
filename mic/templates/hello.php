<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Hello World</title>
</head>
<body>
	<h1>Hello World</h1>
	
	<strong>Welcome to Mic Framework!</strong>
	
	<p>
		As a quick introduction and tutorial to the framework, you are seeing this page because:
		<ul>
			<li>You requested a representation for a resource identified by <strong><?php echo $path;?> </strong></li>
			<li>Your request was routed to a resource located at <strong><?php echo $resourceFile;?></strong> </li>
			<li>That resource returned a representation, a template file at <strong><?php echo __FILE__;?></strong>
		</ul>
	</p>
	<p>
		For more information, please read the documentation.
	</p>
	<hr />
	<p>
		Powered by: <em>Mic Framework</em>
	</p>
</body>
</html>