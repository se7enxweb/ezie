<?php
/**
 * File containing the rotation tool handler
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */
$prepare_action = new eZIEImagePreAction();

$http = eZHTTPTool::instance();

$angle = $http->hasPostVariable( 'angle' ) ? $http->postVariable( 'angle' ) : 0;
$color = $http->hasPostVariable( 'color' ) ? $http->postVariable( 'color' ) : 'FFFFFF';

if ( !is_numeric( $angle ) )
{
    eZIEImagePreAction::sendError( 400, 'The rotation angle must be a number' );
}
// the handlers take 0 to 360 degrees
$angle = ( (int)round( $angle ) % 360 + 360 ) % 360;

// the background color goes to the handlers as a hexadecimal RGB code
$color = ltrim( is_string( $color ) ? trim( $color ) : '', '#' );
if ( !preg_match( '/^[0-9a-fA-F]{6}$/', $color ) )
{
    $color = 'FFFFFF';
}

if ( $http->hasPostVariable( 'clockwise' ) && $http->postVariable( 'clockwise' ) == 'yes' )
{
    $angle = ( 360 - $angle ) % 360;
}

$prepare_action->apply( eZIEImageToolRotation::filter( $angle, $color ) );
?>
