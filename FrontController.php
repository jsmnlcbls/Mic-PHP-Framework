<?php
/**
 * Copyright 2010, Jose Manuel A. Cabiles III
 * 
 * This file is part of Mic Framework.
 * 
 * Mic Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Mic Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Mic Framework.  If not, see <http://www.gnu.org/licenses/>.
 */

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