<!DOCTYPE html>
<html lang="en">

<head>
    <title>Kanakku</title>
	<link rel="stylesheet" href="/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="/assets/css/style.css">
	<base href="/">	
	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="/assets/plugins/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" href="/assets/plugins/fontawesome/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" href="/assets/img/favicon.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-config" content="/assets/img/favicons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div id="app" >
        <router-view></router-view>
    </div>
		<script src="assets/js/jquery-3.6.0.min.js"></script>
		
		<!-- Bootstrap Core JS -->
        <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="{{mix('/assets/js/app.js')}}"></script>
</body>

</html>
