<?php
/**
 * File containing the ezie/prepare view
 * This view prepares an image for edition, and returns its information as JSON
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package ezie
 */

$Module = $Params['Module'];
$Params = $Module->getNamedParameters();
$objectId     = isset( $Params['object_id'] ) ? (int)$Params['object_id'] : 0;
$editLanguage = isset( $Params['edit_language'] ) ? (string)$Params['edit_language'] : '';
$attributeID  = isset( $Params['attribute_id'] ) ? (int)$Params['attribute_id'] : 0;
$version      = isset( $Params['version'] ) ? (int)$Params['version'] : 0;

// Checks the attribute is an image of the user's own draft that the user may
// edit, in the attribute's own language (ends the request if not)
$attribute = eZIEImagePreAction::fetchEditableAttribute( $attributeID, $version );
if ( (int)$attribute->attribute( 'contentobject_id' ) !== $objectId ||
     ( $editLanguage !== '' && $attribute->attribute( 'language_code' ) !== $editLanguage ) )
{
    eZIEImagePreAction::sendError( 400, 'The image does not belong to this object or language' );
}

// retrieve the original image path
$img = $attribute->attribute( 'content' );
$image_path = $img ? $img->attributeFromOriginal( 'url' ) : '';
$handler = eZClusterFileHandler::instance();
if ( $image_path == '' || !$handler->fileExists( $image_path ) )
{
    eZIEImagePreAction::sendError( 404, 'The attribute has no image file to edit' );
}

// Creation of the editing arborescence
// /{cache folder}/public/ezie/user_id/image_id-version_id
// A folder left behind by an earlier session is emptied first, so that no
// stale history version of it can be picked up.
$working_folder_path = eZIEImagePreAction::workingFolder( $attributeID, $version );
$working_folder_absolute_path = eZSys::rootDir() . "/{$working_folder_path}";

if ( is_dir( $working_folder_absolute_path ) )
{
    eZDir::recursiveDelete( $working_folder_absolute_path );
}
eZDir::mkdir( $working_folder_absolute_path, false, true );

// Copy the original file in the temp directory
// $work_folder/{history_id}-{file_name}
// (thumb: $working_folder/thumb_{history_id}-{file_name}
$file = "0-" . basename( $image_path );
$thumb = "thumb-{$file}";

$failure = false;
try
{
    $handler->fileCopy( $image_path, "{$working_folder_path}/{$file}" );
    if ( !$handler->fileExists( "{$working_folder_path}/{$file}" ) )
    {
        throw new Exception( "Copying $image_path to $working_folder_path failed" );
    }

    // Creation of a thumbnail
    eZIEImageToolResize::doThumb(
        "{$working_folder_path}/{$file}",
        "{$working_folder_path}/{$thumb}"
    );
    // retrieve image dimensions
    $ezcanalyzer = new eZIEImageAnalyzer( "{$working_folder_path}/{$file}", false );
    $width = (int)$ezcanalyzer->data->width;
    $height = (int)$ezcanalyzer->data->height;
}
catch ( Exception $e )
{
    eZDebug::writeError( get_class( $e ) . ': ' . $e->getMessage(), 'ezie/prepare' );
    $failure = true;
}
if ( $failure )
{
    eZIEImagePreAction::sendError( 500, 'The image could not be prepared for editing' );
}

$object = new stdClass();

$imageURI = "{$working_folder_path}/{$file}";
eZURI::transformURI( $imageURI, true );

$thumbnailURI = "{$working_folder_path}/{$thumb}";
eZURI::transformURI( $thumbnailURI, true );

$moduleURI = 'ezie';
eZURI::transformURI( $moduleURI, false );

$object->thumbnail_url = $thumbnailURI;
$object->image_url = $imageURI;

// the key is the folder where the working image is stored
$object->key = eZIEImagePreAction::workingKey( $attributeID, $version );
$object->image_id = (int)$attributeID;
$object->image_version = (int)$version;
$object->history_version = 0;
$object->module_url = $moduleURI;
$object->image_width = $width;
$object->image_height = $height;

eZIEImagePreAction::sendJSON( $object );

?>
