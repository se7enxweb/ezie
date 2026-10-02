<?php
/**
 * The code of extension/ezie/modules/ezie/tool_watermark.php, moved into a class (#207 stage 1). The file extension/ezie/modules/ezie/tool_watermark.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of extension/ezie/modules/ezie/tool_watermark.php:
 *
 *
 * File containing the watermark tool handler
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Extension\Ezie\Ezie
{

class ToolWatermark extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $prepare_action = new \eZIEImagePreAction();

        $http = \eZHTTPTool::instance();

        if ( !$prepare_action->hasRegion() || !$http->hasPostVariable( 'watermark_image' ) )
        {
            \eZIEImagePreAction::sendError( 400, 'Select a watermark and place it on the image first' );
        }

        // Only the watermarks offered by the editor ([eZIE] watermarks in image.ini)
        // can be used: the name ends up in a file path.
        $watermark = $http->postVariable( 'watermark_image' );
        $imageINI = \eZINI::instance( 'image.ini' );
        $watermarks = $imageINI->hasVariable( 'eZIE', 'watermarks' ) ? (array)$imageINI->variable( 'eZIE', 'watermarks' ) : array();
        if ( !is_string( $watermark ) || !in_array( $watermark, $watermarks, true ) || basename( $watermark ) !== $watermark ||
             !\eZIEImageToolWatermark::imagePath( $watermark ) )
        {
            \eZIEImagePreAction::sendError( 400, 'Unknown watermark image' );
        }

        $failure = false;
        try
        {
            $filters = \eZIEImageToolWatermark::filter( $prepare_action->getRegion(), $watermark );
        }
        catch ( \Exception $e )
        {
            \eZDebug::writeError( get_class( $e ) . ': ' . $e->getMessage(), 'ezie/tool_watermark' );
            $failure = true;
        }
        if ( $failure )
        {
            \eZIEImagePreAction::sendError( 500, 'The watermark image could not be read' );
        }

        $prepare_action->apply( $filters );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
