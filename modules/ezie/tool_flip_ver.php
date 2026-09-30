<?php
/**
 * File containing the ezie vertical flip handler
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package ezie
 */
$prepare_action = new eZIEImagePreAction();

$prepare_action->apply( eZIEImageToolFlipVertically::filter() );
?>
