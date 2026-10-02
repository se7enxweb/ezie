<?php
/**
 * The code of extension/ezie/modules/ezie/no_save_and_quit.php, moved into a class (#207 stage 1). The file extension/ezie/modules/ezie/no_save_and_quit.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of extension/ezie/modules/ezie/no_save_and_quit.php:
 *
 *
 * File containing the ezie no save & quit menu item handler
 *
 * Throws the working copies of the edited image away. The image attribute
 * itself is left as it was.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package ezie
 *
 */

namespace Exponential\View\Extension\Ezie\Ezie
{

class NoSaveAndQuit extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $prepare_action = new \eZIEImagePreAction( false );

        // deletes the working folder recursively; its path is built from the current
        // user and the image, never from the request
        \eZDir::recursiveDelete( \eZSys::rootDir() . '/' . $prepare_action->getWorkingFolder() );

        \eZIEImagePreAction::sendJSON( new \stdClass() );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
