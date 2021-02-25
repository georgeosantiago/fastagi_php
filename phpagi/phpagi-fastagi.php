#!/usr/local/bin/php -q
<?php
 /**
  * phpagi-fastagi.php : PHP FastAGI bootstrap
  * Website: http://phpagi.sourceforge.net
  */

  require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'phpagi.php');

  //$fastagi = new AGI();  
  //$fastagi = new AGI("/etc/asterisk/phpagi.conf", $myconfig);  
  //$fastagi = new AGI(null, array("debug" => true, "tempdir" => "/tmp" ));
  $fastagi = new AGI("/etc/asterisk/phpagi.conf");

  //$fastagi->verbose(print_r($fastagi, true));  
  $fastagi->conlog(print_r($fastagi, true));

  if(!isset($fastagi->config['fastagi']['basedir']))
    $fastagi->config['fastagi']['basedir'] = dirname(__FILE__);

  // perform some security checks
  $script = $fastagi->config['fastagi']['basedir'] . DIRECTORY_SEPARATOR . $fastagi->request['agi_network_script'];
  
  // in the same directory (or subdirectory)
  $mydir = dirname($fastagi->config['fastagi']['basedir']) . DIRECTORY_SEPARATOR;
  $dir = dirname($script) . DIRECTORY_SEPARATOR;
  
  $fastagi->conlog("script => ". $script);
  
  if(substr($dir, 0, strlen($mydir)) != $mydir)
  {
    $fastagi->conlog("$script is not allowed to execute.");
    exit;
  }

  // make sure it exists
  if(!file_exists($script))
  {
    $fastagi->conlog("$script does not exist.");
    exit;
  }

  // drop privileges
  if(isset($fastagi->config['fastagi']['setuid']) && $fastagi->config['fastagi']['setuid'])
  {
    $owner = fileowner($script);
    $group = filegroup($script);
    if(!posix_setgid($group) || !posix_setegid($group) || !posix_setuid($owner) || !posix_seteuid($owner))
    {
      $fastagi->conlog("failed to lower privileges.");
      exit;      
    }
  }

  // make sure script is still readable
  if(!is_readable($script))
  {
    $fastagi->conlog("$script is not readable.");
    exit;
  }

  require_once($script);
?>
