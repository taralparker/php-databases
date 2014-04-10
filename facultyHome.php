<?php
	$pageSessionType = "faculty";
	include "sessionValidator.php";
?>

<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Instructor Home</title>
  <link rel="stylesheet" href="css/style.css" type="text/css" media="screen">
 </head>

 <body>
  <div id="container">
   <div id="masthead">
    <div id="logo"></div>
    <div id="title"></div>
   </div>
  
<?php echo file_get_contents( $pageSessionType."Header.php" ); ?>
  
  </div>
 </body>
</html>
