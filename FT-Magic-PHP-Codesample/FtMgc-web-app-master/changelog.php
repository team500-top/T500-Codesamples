<?php
include('includes/inc.php');


spl_autoload_register(function($class){
	require preg_replace('{\\\\|_(?!.*\\\\)}', DIRECTORY_SEPARATOR, ltrim($class, '\\')).'.php';
});

# Get Markdown class
use \ftmgcMC\Markdown;

# Read file and pass content through the Markdown parser
$text = file_get_contents('CHANGELOG.md');
$html = Markdown::defaultTransform($text);

$jsArray = array();

require_once("includes/header.php");
?>
       
<!-- ### Below is the Settings page which contains common/site-wide preferences
      
-->
         
      <div id="help-wrap" class="container sub-nav full-content">
        <div class="markdown-body">
           <div id="readme" class="panel panel-default panel-no-grid">
             <h1>Update History</h1>
             <div class="panel-heading">
                 <h2 class="panel-title"><i class="icon icon-document"></i> CHANGELOG.md</h2>
              </div>
              <div class="panel-body panel-body-markdown">
              <?php
                 # Put rendered README markdown in the document
                 echo $html;
              ?>
              </div>
           </div>
        </div>
      </div>
      <!-- /container -->

      <?php require_once("includes/footer.php"); ?>
      </div>
      <!-- /page-container -->
      
      <?php require_once("includes/scripts.php"); ?>
   </body>
</html>