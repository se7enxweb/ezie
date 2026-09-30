<?php
/**
 * File containing the ezie no save & quit menu item handler
 *
 * Throws the working copies of the edited image away. The image attribute
 * itself is left as it was.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package ezie
 */

// closing must work even when the working copy is already gone
$prepare_action = new eZIEImagePreAction( false );

// deletes the working folder recursively; its path is built from the current
// user and the image, never from the request
eZDir::recursiveDelete( eZSys::rootDir() . '/' . $prepare_action->getWorkingFolder() );

eZIEImagePreAction::sendJSON( new stdClass() );
?>
