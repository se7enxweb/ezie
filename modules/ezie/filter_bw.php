<?php
/**
 * File containing the black & white filter handler
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */
$prepare_action = new eZIEImagePreAction();

$prepare_action->apply( eZIEImageFilterBW::filter( $prepare_action->getRegion() ) );
?>
