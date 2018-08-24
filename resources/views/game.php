<!DOCTYPE html>
<html>
<head>
  <title>Tic Tac Toe</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
  <!-- Third-party -->
  <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="57x57" href="dist/images/apple-icon-57x57.png">
  <link rel="apple-touch-icon" sizes="60x60" href="dist/images/apple-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="72x72" href="dist/images/apple-icon-72x72.png">
  <link rel="apple-touch-icon" sizes="76x76" href="dist/images/apple-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="114x114" href="dist/images/apple-icon-114x114.png">
  <link rel="apple-touch-icon" sizes="120x120" href="dist/images/apple-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="144x144" href="dist/images/apple-icon-144x144.png">
  <link rel="apple-touch-icon" sizes="152x152" href="dist/images/apple-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="dist/images/apple-icon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192"  href="dist/images/android-icon-192x192.png">
  <link rel="icon" type="image/png" sizes="32x32" href="dist/images/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="96x96" href="dist/images/favicon-96x96.png">
  <link rel="icon" type="image/png" sizes="16x16" href="dist/images/favicon-16x16.png">
  <link rel="manifest" href="dist/manifest.json">
  <meta name="msapplication-TileColor" content="#ffffff">
  <meta name="msapplication-TileImage" content="dist/images/ms-icon-144x144.png">
  <meta name="theme-color" content="#ffffff">
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="dist/all.css" media="all">
  <!-- JS Libraries-->
	<script src="https://unpkg.com/react@16/umd/react.production.min.js" crossorigin></script>
	<script src="https://unpkg.com/react-dom@16/umd/react-dom.production.min.js" crossorigin></script>
	<script src="https://unpkg.com/babel-standalone@6/babel.min.js"></script>
  <!-- JS Custom Settings -->
  <script type="text/javascript">
    const settings = {
      api_base: '<?php echo url('/'); ?>'
    };
  </script>
	<script type="text/babel" src="dist/app.js"></script>
</head>
<body>
  <div id="root"></div>
</body>
</html>
