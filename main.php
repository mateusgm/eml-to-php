<?
   require_once('functions.php');
   
   $root = $argv[1];
   $filesPath = $root . '/files.txt';
   $emailsPath = $root . '/emails.php';
   
   $files_tmp = array ();
   getFiles ($root, &$files_tmp);
   writeValues ($filesPath, $files_tmp);

   $files = getValues ($filesPath);
   $emails = getEmails ($files);
   $emails = prepareEmails ($emails);

   writeValues ($emailsPath, $emails);

   echo $count . " with encoding errors\n";
  
?>
