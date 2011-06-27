<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>405 Method Not Allowed</title>
</head>
<body>
	<h1>Method Not Allowed</h1>
	<p>
		The request method <strong><?php echo $method; ?></strong> is not supported by this resource.
	</p>
	<p>	
		<?php 
		$methodCount = count($method);
		if ($methodCount == 1) {
			echo 'Allowed method: ';
		} elseif($methodCount > 1) {
			echo 'Allowed methods: ';
		}
		if (!empty($allowedMethod)) {
			$allowed = implode(', ', $allowedMethod);
			echo $allowed;
		}
		?>
	</p>
	<hr />
	<p>
		<em>Powered by: Mic Framework</em>
	</p>
</body>
</html>