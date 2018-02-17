<!-- author = waploaj -->
#!/usr/bin/php
<?php
define("CR", "\r");
define("TERMINAL_DEVICE", "/dev/ttyUSB0");
define("ADDITIONAL_DEVICE", "/dev/ttyUSB2");
$args = $_SERVER['argc'];
if($args < 2)
  help();
else
{
  $command = $_SERVER['argv'][1];
  
  if(preg_match("/^\\*[0-9*]+#$/", $command))
  {
      runUSSD($command);
  }
  elseif(preg_match("/^AT.*$/", $command))
  {
      runAT($command);
  }
  else
      error("Incorrect format of command.");
}
exit;
//  help function