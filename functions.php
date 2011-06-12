<?

   require_once('mime-mail-parser/MimeMailParser.class.php');
   
   function getFiles ($root, &$files) {
      $paths = scandir ($root);
      foreach ($paths as $path) {
         if ($path == '.' || $path == '..') continue;
         $path = $root . '/' . $path;
         if (is_dir($path)) getFiles ($path, $files);
         else $files[] = $path;
      }
   }
   
   function writeValues ($path, $values) {
      $file = fopen($path, 'w');
      if ($file) {
         foreach ($values as $value)
            fwrite ($file, $value . "\n");
         fclose ($file);
      }
   }
   
   function getValues ($path) {
      $file = fopen($path, 'r');
      if ($file) {
         $data = array();
         while ($line = fgets($file))
            $data[] = trim ($line);
         fclose ($file);
         return $data;
      } else {
         return null;
      }   
   }
   
   function getEmails ($files) {
      $emails = array();
      foreach ($files as $file) {
         $parser = new MimeMailParser();
         $parser->setPath($file);
         $to = mb_decode_mimeheader($parser->getHeader('to'));
         $from = mb_decode_mimeheader($parser->getHeader('from'));
         extractEmails ($to, &$emails);
         extractEmails ($from, &$emails);
      }
      return $emails; 
   }

   global $count;
   $count = 0;

   function extractEmails ($header, &$final) {
      global $count;
      $invalid = array ('', 'postmaster', 'MAILER-DAEMON', 'comprovante.real', 'sem resposta"',
                        'Citibank.Brazil', 'InternetBanking', 'noreply-orkut', 'picasaweb-noreply', 'noreply');
      $eliminate = "<> \t\n\r\0\"'?=";

      $emails = explode (',', $header);
      foreach ($emails as $email) {
         $index = strpos ($email, '<');
         if ($index !== false) {
            $address = substr ($email, $index);
            $name = substr  ($email, 0, $index);
         } else {
            $name = '-';
            $address = $email;
         }
         $name = trim ($name, $eliminate);
         $address = trim ($address, $eliminate);
         $user = strstr ($address, '@', true);
         if (strpos ($name, '?') !== false || strpos ($user, '?') !== false) {
            echo "- codification error: ${name}\n";
            $count++;
         } elseif (!in_array($name, $invalid) && !in_array($user, $invalid)) {
            if ($name == '-') $name = '';
            if (isset($final[$address])) {
               $final[$address]['count']++;
            } else {
               $final[$address] = array ('name' => $name, 'user' => $user, 'count' => 1);
            }
            
         }
      }
   }
   
   function prepareEmails ($emails) {
      $prefix = '   $emails[' . "'";
      $midfix = "'] = array ('name' => \"";
      $midfix2 = "\", 'user' => \"";
      $midfix3 = "\", 'count' => ";
      $posfix = ");";

      $data = array();
      $data[] = '<?';
      $data[] = '   $emails = array();';       
      foreach ($emails as $address => $info) {
         $name = $info['name'];
         $user = $info['user'];
         $count = $info['count'];
         $data[] = $prefix . $address
                   . $midfix . $name
                   . $midfix2 . $user 
                   . $midfix3 . $count . $posfix;
      }
      $data[] = '?>';
      
      return $data;
   }
   
?>
