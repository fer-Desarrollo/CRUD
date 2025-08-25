<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>404 Page Not Found</title>
<style type="text/css">

::selection { background-color: #E13300; color: white; }
::-moz-selection { background-color: #E13300; color: white; }

body {
	background-color: #f8f9fa;
	margin: 40px;
	font: 14px/20px normal Helvetica, Arial, sans-serif;
	color: #333;
}

a {
	color: #007bff;
	text-decoration: none;
}

a:hover {
	text-decoration: underline;
}

h1 {
	color: #dc3545;
	background-color: transparent;
	border-bottom: 1px solid #dee2e6;
	font-size: 24px;
	font-weight: bold;
	margin: 0 0 20px 0;
	padding: 14px 15px 10px 15px;
}

code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f1f3f5;
	border: 1px solid #ced4da;
	color: #495057;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

#container {
	margin: 10px auto;
	border: 1px solid #dee2e6;
	box-shadow: 0 0 10px rgba(0,0,0,0.1);
	padding: 20px;
	max-width: 600px;
	background-color: #fff;
	border-radius: 8px;
	text-align: center;
}

p {
	margin: 12px 15px 20px 15px;
	font-size: 16px;
}

button {
	background-color: #007bff;
	color: #fff;
	border: none;
	padding: 10px 20px;
	border-radius: 5px;
	cursor: pointer;
	font-size: 16px;
}

button:hover {
	background-color: #0056b3;
}
</style>
</head>
<body>
	<div id="container">
		<h1><?php echo isset($heading) ? $heading : '404 Page Not Found'; ?></h1>
		<p><?php echo isset($message) ? $message : 'Lo sentimos, la página que buscas no existe.'; ?></p>
		<button onclick="history.back()">← Regresar</button>
	</div>
</body>
</html>
