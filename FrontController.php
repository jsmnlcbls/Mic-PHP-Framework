<?php
namespace Mic {

use Mic\Settings;
use Mic\Library\System;

//Load the configuration. Change path as necessary.
require './Settings.php';

$settings = new Settings();
$systemFile = $settings->getMicDirectory() . DIRECTORY_SEPARATOR 
			. 'library' . DIRECTORY_SEPARATOR . 'System.php';

require $systemFile;

$system = System::getInstance($settings);

$system->run();
}