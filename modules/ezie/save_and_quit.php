<?php
/**
 * File containing the ezie save & quit menu item handler
 *
 * Stores the current history version of the edited image into the image
 * attribute of the draft, removes the working folder and answers with the
 * attribute's edit template, which the editor puts back into the edit form.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package ezie
 */

// The code is in extension/ezie/classes/runnable/views/ezie/save_and_quit.php (#207); this file is the entry point.
return \Exponential\View\Extension\Ezie\Ezie\SaveAndQuit::main( __FILE__, get_defined_vars() );
