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
function runUSSD($command)
{
  echo "USSD request: ".$command.PHP_EOL;
  
  $pdu = str2pdu($command);
  $command = "AT+CUSD=1,{$pdu},15";
  runAT($command);
  
  $fp = fopen(ADDITIONAL_DEVICE, 'r');
  $answer = "";
  $ending="\n";
  $starting = "+CUSD:";
  do
  {
    $char = fgetc($fp);
    if(strlen($starting) == 0 && $ending[0] == $char)
    {
      $ending = substr($ending, 1);
    }
    elseif(strlen($starting) && $starting[0] == $char)
    {
      $starting = substr($starting, 1);
    }
    $answer .= $char;
  }
  while(strlen($ending));
  fclose($fp);
  
  $lines = preg_split("/\r\n|\r|\n/", $answer);
  foreach($lines as $answer)
  {
    if(preg_match('/\+CUSD: 0,"(.*)",1/', $answer, $m))
    {
      $pduanswer = $m[1];
      $answer = pdu2str($pduanswer);
      echo "Answer: ".$answer.PHP_EOL; 
    }
  }
}
