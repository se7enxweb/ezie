<?php
/**
 * File containing the contrast filter handler
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
    eZIEImagePreAction::sendError( 400, 'The contrast value must be a number' );
}
// valid range of the handlers: -100 to 100
$value = max( -100, min( 100, (int)round( $value ) ) );

$prepare_action->apply( eZIEImageFilterContrast::filter( $value, $prepare_action->getRegion() ) );
?>
