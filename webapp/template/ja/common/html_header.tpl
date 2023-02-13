<!DOCTYPE html>
<html lang="ja" class="h-100">
	<!-- Head -->
	<head>
		<title>{{if $title}}{{$title}} | {{/if}}{{$config.app_name|lang}}</title>

		<!-- Meta -->
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta name="keywords" content="{{$config.site_keyword}}">
		<meta name="description" content="{{$config.site_description}}">
		<meta name="author" content="{{$config.app_name}}">


		<meta property="og:site_name" content="{{$config.app_name}}" />
		<meta property="og:url" content="{{$config.url}}" />
		<meta property="og:type" content="{{if $title}}article{{else}}website{{/if}}" /> 
		<meta property="og:title" content="{{if $title}}{{$title}}{{else}}{{$config.app_name}}{{/if}}" />
		<meta property="og:description" content="{{$config.site_description}}" />
		<meta property="og:site_name" content="{{$config.app_name}}" />
		<meta property="og:image" content="{{$config.url}}common/logo.png" />

		<link rel="shortcut icon" href="{{$config.base}}common/favicon.ico" type="image/x-icon">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Bakbak+One&family=Noto+Sans+JP:wght@100;400&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
		<script src="https://kit.fontawesome.com/123bf1af3a.js" crossorigin="anonymous"></script>
		
		<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
		<link rel="stylesheet" type="text/css" href="{{$config.base}}assets/vendors/slick-carousel/slick-theme.css">
		{{* <link rel="stylesheet" type="text/css" href="{{$config.base}}assets/vendors/slick-carousel/slick.css"> *}}
		<link rel="stylesheet" type="text/css" href="{{$config.base}}common/css/afw.css">
	</head>
	<!-- End Head -->

	<body class="d-flex flex-column h-100">
