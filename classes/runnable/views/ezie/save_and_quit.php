<?php
/**
 * The code of extension/ezie/modules/ezie/save_and_quit.php, moved into a class (#207 stage 1). The file extension/ezie/modules/ezie/save_and_quit.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 */
/*
 * The original header of extension/ezie/modules/ezie/save_and_quit.php:
 *
 *
 * File containing the ezie save & quit menu item handler
 *
 * Stores the current history version of the edited image into the image
 * attribute of the draft, removes the working folder and answers with the
 * attribute's edit template, which the editor puts back into the edit form.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package ezie
 *
 */

namespace Exponential\View\Extension\Ezie\Ezie
{

class SaveAndQuit extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $prepare_action = new \eZIEImagePreAction();
        $imageId = $prepare_action->getImageId();
        $imageVersion = $prepare_action->getImageVersion();

        $imageAttribute = \eZContentObjectAttribute::fetch( $imageId, $imageVersion );

        // the working copy must be local to be read by the image alias handler
        $clusterFile = \eZClusterFileHandler::instance( $prepare_action->getImagePath() );
        $clusterFile->fetch();

        // Save the class attribute
        $imageHandler = $prepare_action->getImageHandler();
        $stored = $imageHandler->initializeFromFile(
            $prepare_action->getImagePath(),
            $imageHandler->attribute( 'alternative_text' ),
            $imageHandler->attribute( 'original_filename' )
        );
        if ( $stored === false )
        {
            \eZIEImagePreAction::sendError( 500, 'The edited image could not be stored' );
        }

        $imageHandler->store( $imageAttribute );

        // remove view cache if needed
        \eZContentCacheManager::clearObjectViewCacheIfNeeded( $imageAttribute->attribute( 'contentobject_id' ) );

        // deletes the working folder recursively
        \eZDir::recursiveDelete( \eZSys::rootDir() . '/' . $prepare_action->getWorkingFolder() );

        // new attribute
        $imageAttribute = \eZContentObjectAttribute::fetch( $imageId, $imageVersion );

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'ezie_ajax_response', true );
        $tpl->setVariable( 'attribute', $imageAttribute );
        header( 'Content-Type: text/html; charset=utf-8' );
        echo $tpl->fetch( "design:content/datatype/edit/ezimage.tpl" );
        \eZExecution::cleanExit();

        return $this->viewResult( isset( $Result ) ? $Result : null, null );
    }
}

}
