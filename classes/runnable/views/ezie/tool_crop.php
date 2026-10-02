<?php
/**
 * The code of extension/ezie/modules/ezie/tool_crop.php, moved into a class (#207 stage 1). The file extension/ezie/modules/ezie/tool_crop.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of extension/ezie/modules/ezie/tool_crop.php:
 *
 *
 * File containing the crop tool handler
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Extension\Ezie\Ezie
{

class ToolCrop extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $prepare_action = new \eZIEImagePreAction();

        if ( !$prepare_action->hasRegion() )
        {
            \eZIEImagePreAction::sendError( 400, 'Select the area to crop first' );
        }

        $prepare_action->apply( \eZIEImageToolCrop::filter( $prepare_action->getRegion() ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
