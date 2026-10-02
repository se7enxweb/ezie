<?php
/**
 * The code of extension/ezie/modules/ezie/filter_contrast.php, moved into a class (#207 stage 1). The file extension/ezie/modules/ezie/filter_contrast.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of extension/ezie/modules/ezie/filter_contrast.php:
 *
 *
 * File containing the contrast filter handler
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace Exponential\View\Extension\Ezie\Ezie
{

class FilterContrast extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $prepare_action = new \eZIEImagePreAction();

        $http = \eZHTTPTool::instance();
        $value = $http->hasPostVariable( 'value' ) ? $http->postVariable( 'value' ) : 0;
        if ( !is_numeric( $value ) )
        {
            \eZIEImagePreAction::sendError( 400, 'The contrast value must be a number' );
        }
        // valid range of the handlers: -100 to 100
        $value = max( -100, min( 100, (int)round( $value ) ) );

        $prepare_action->apply( \eZIEImageFilterContrast::filter( $value, $prepare_action->getRegion() ) );

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
