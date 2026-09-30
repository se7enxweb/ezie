<?php
/**
 * File containing the sepia filter handler
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */
$prepare_action = new eZIEImagePreAction();

$prepare_action->apply( eZIEImageFilterSepia::filter( $prepare_action->getRegion() ) );
?>
