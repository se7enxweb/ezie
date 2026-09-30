<?php
/**
 * File containing the pixelate tool handler
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */
$prepare_action = new eZIEImagePreAction();

// retrieve image dimensions
$failure = false;
try
{
    $analyzer = new eZIEImageAnalyzer( $prepare_action->getImagePath(), false );
    $width = (int)$analyzer->data->width;
    $height = (int)$analyzer->data->height;
}
catch ( Exception $e )
{
    eZDebug::writeError( get_class( $e ) . ': ' . $e->getMessage(), 'ezie/tool_pixelate' );
    $failure = true;
}
if ( $failure || $width < 1 || $height < 1 )
{
    eZIEImagePreAction::sendError( 500, 'The image could not be analyzed' );
}

$prepare_action->apply( eZIEImageToolPixelate::filter( $width, $height, $prepare_action->getRegion() ) );
?>
