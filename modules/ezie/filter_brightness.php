<?php
/**
 * File containing the brightness filter handler
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */
$prepare_action = new eZIEImagePreAction();

$http = eZHTTPTool::instance();
$value = $http->hasPostVariable( 'value' ) ? $http->postVariable( 'value' ) : 0;
if ( !is_numeric( $value ) )
{
    eZIEImagePreAction::sendError( 400, 'The brightness value must be a number' );
}
// valid range of the handlers: -255 to 255
$value = max( -255, min( 255, (int)round( $value ) ) );

$prepare_action->apply( eZIEImageFilterBrightness::filter( $value, $prepare_action->getRegion() ) );
?>
