<?php
/**
 * File containing the watermark tool handler
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */
$prepare_action = new eZIEImagePreAction();

$http = eZHTTPTool::instance();

if ( !$prepare_action->hasRegion() || !$http->hasPostVariable( 'watermark_image' ) )
{
    eZIEImagePreAction::sendError( 400, 'Select a watermark and place it on the image first' );
}

// Only the watermarks offered by the editor ([eZIE] watermarks in image.ini)
// can be used: the name ends up in a file path.
$watermark = $http->postVariable( 'watermark_image' );
$imageINI = eZINI::instance( 'image.ini' );
$watermarks = $imageINI->hasVariable( 'eZIE', 'watermarks' ) ? (array)$imageINI->variable( 'eZIE', 'watermarks' ) : array();
if ( !is_string( $watermark ) || !in_array( $watermark, $watermarks, true ) || basename( $watermark ) !== $watermark ||
     !eZIEImageToolWatermark::imagePath( $watermark ) )
{
    eZIEImagePreAction::sendError( 400, 'Unknown watermark image' );
}

$failure = false;
try
{
    $filters = eZIEImageToolWatermark::filter( $prepare_action->getRegion(), $watermark );
}
catch ( Exception $e )
{
    eZDebug::writeError( get_class( $e ) . ': ' . $e->getMessage(), 'ezie/tool_watermark' );
    $failure = true;
}
if ( $failure )
{
    eZIEImagePreAction::sendError( 500, 'The watermark image could not be read' );
}

$prepare_action->apply( $filters );
?>
