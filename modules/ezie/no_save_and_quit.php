<?php
/**
 * File containing the ezie no save & quit menu item handler
 *
 * Throws the working copies of the edited image away. The image attribute
 * itself is left as it was.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package ezie
 */

// closing must work even when the working copy is already gone

// The code is in extension/ezie/classes/runnable/views/ezie/no_save_and_quit.php (#207); this file is the entry point.
return \Exponential\View\Extension\Ezie\Ezie\NoSaveAndQuit::main( __FILE__, get_defined_vars() );
