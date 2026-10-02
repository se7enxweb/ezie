<?php
/**
 * The code of extension/ezie/modules/ezie/tool_flip_ver.php, moved into a class (#207 stage 1). The file extension/ezie/modules/ezie/tool_flip_ver.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of extension/ezie/modules/ezie/tool_flip_ver.php:
 *
 *
 * File containing the ezie vertical flip handler
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package ezie
 *
 */

namespace Exponential\View\Extension\Ezie\Ezie
{

class ToolFlipVer extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $prepare_action = new \eZIEImagePreAction();

        $prepare_action->apply( \eZIEImageToolFlipVertically::filter() );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
