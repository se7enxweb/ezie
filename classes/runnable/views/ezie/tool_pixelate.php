<?php
/**
 * The code of extension/ezie/modules/ezie/tool_pixelate.php, moved into a class (#207 stage 1). The file extension/ezie/modules/ezie/tool_pixelate.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of extension/ezie/modules/ezie/tool_pixelate.php:
 *
 *
 * File containing the pixelate tool handler
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Extension\Ezie\Ezie
{

class ToolPixelate extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $prepare_action = new \eZIEImagePreAction();

        // retrieve image dimensions
        $failure = false;
        try
        {
            $analyzer = new \eZIEImageAnalyzer( $prepare_action->getImagePath(), false );
            $width = (int)$analyzer->data->width;
            $height = (int)$analyzer->data->height;
        }
        catch ( \Exception $e )
        {
            \eZDebug::writeError( get_class( $e ) . ': ' . $e->getMessage(), 'ezie/tool_pixelate' );
            $failure = true;
        }
        if ( $failure || $width < 1 || $height < 1 )
        {
            \eZIEImagePreAction::sendError( 500, 'The image could not be analyzed' );
        }

        $prepare_action->apply( \eZIEImageToolPixelate::filter( $width, $height, $prepare_action->getRegion() ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
