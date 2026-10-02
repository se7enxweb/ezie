<?php
/**
 * The code of extension/ezie/modules/ezie/filter_bw.php, moved into a class (#207 stage 1). The file extension/ezie/modules/ezie/filter_bw.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of extension/ezie/modules/ezie/filter_bw.php:
 *
 *
 * File containing the black & white filter handler
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Extension\Ezie\Ezie
{

class FilterBw extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $prepare_action = new \eZIEImagePreAction();

        $prepare_action->apply( \eZIEImageFilterBW::filter( $prepare_action->getRegion() ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
