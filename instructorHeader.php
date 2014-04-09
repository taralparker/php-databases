<header>
 <div style="margin: 8; margin-top: 0;">
  <nav style="float: left; margin: 0;">
   <a href="instructorPreferenceEdit.php">Preference Editor</a>
   /
   <a href="instructorLoadEdit.php">Load Editor</a>
   /
   <a href="instructorRequestEdit.php">Request Editor</a>
   /
   <a href="instructorTextbookEdit.php">Textbook Editor</a>
   /
   <a href="instructorNextView.php">Future Classes</a>
  </nav>
  <nav style="float: right; margin: 0;">
   <a href="instructorHome.php">Home</a>
   /
   <a href="logout.php">Logout</a>
   <?php printf( "(R%s)\n" , $_SESSION[ "rNumber" ] ); ?>
  </nav>
  <div style="clear: both;"></div>
 </div>
 <hr style="margin: 0; border-color: #333333;" noshade>
</header>
