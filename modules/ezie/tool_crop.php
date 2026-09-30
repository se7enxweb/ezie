<?php
/**
 * File containing the crop tool handler
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */
$prepare_action = new eZIEImagePreAction();

if ( !$prepare_action->hasRegion() )
{
    eZIEImagePreAction::sendError( 400, 'Select the area to crop first' );
}

$prepare_action->apply( eZIEImageToolCrop::filter( $prepare_action->getRegion() ) );
?>
