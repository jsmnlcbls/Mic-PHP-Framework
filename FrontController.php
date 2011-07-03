<?php
/**
 * The Front Controller. One Controller To Rule Them All.
 * PHP processing starts here.
 * @author enilehcim
 *
 */
namespace Mic {

use Mic\Settings;
use Mic\Library\System;

//Load the configuration. Change path as necessary.
require './Settings.php';

$settings = new Settings();
$systemFile = $settings->getSystemFilePath();
require $systemFile;

$system = System::getInstance($settings);

$system->run();
}