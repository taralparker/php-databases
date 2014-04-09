<header>
 <div style="margin: 8; margin-top: 0;">
  <nav style="float: left; margin: 0;">
   <a href="facultyProfessorEdit.php">Professor Editor</a>
   /
   <a href="facultyCourseEdit.php">Course Editor</a>
   /
   <a href="facultyCourseList.php">Course List</a>
  </nav>
  <nav style="float: right; margin: 0;">
   <a href="facultyHome.php">Home</a>
   /
   <a href="logout.php">Logout</a>
   <?php printf( "(R%s)\n" , $_SESSION[ "rNumber" ] ); ?>
  </nav>
  <div style="clear: both;"></div>
 </div>
 <hr style="margin: 0; border-color: #333333;" noshade>
</header>
