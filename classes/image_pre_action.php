<?php
/**
 * File containing the eZIEImagePreAction class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package ezie
 */
class eZIEImagePreAction
{
    /**
     * @var string
     */
    private $image_path;

    /**
     * @var int
     */
    private $image_id;

    /**
     * @var int
     */
    private $image_version;

    /**
      * @var int
      */
    private $history_version;

    /**
     * @var string
     */
    private $key;

    /**
     * Original image object
     * @var eZImageAliasHandler
     */
    private $original_image;

    /**
     * Image editor work folder
     * @var string
     */
    private $working_folder;

    /**
     * Region affected by the operation
     * @var array(int) Array of 4 integers, with w/h & x/y keys
     */
    private $region;

    /**
     * Constructor
     *
     * Reads the image the editor works on from the POST variables sent with
     * every action, checks that the current user may edit it and that the
     * working copy exists. Answers the request with a JSON error and ends it
     * when one of these does not hold.
     */
    public function __construct( $requireWorkingCopy = true )
    {
        $http = eZHTTPTool::instance();
        foreach ( array( 'image_id', 'image_version', 'history_version' ) as $name )
        {
            if ( !$http->hasPostVariable( $name ) || !self::isUnsignedInt( $http->postVariable( $name ) ) )
            {
                self::sendError( 400, "Missing or invalid parameter '$name'" );
            }
        }
        $this->image_id = (int)$http->postVariable( 'image_id' );
        $this->image_version = (int)$http->postVariable( 'image_version' );
        $this->history_version = (int)$http->postVariable( 'history_version' );

        $attribute = self::fetchEditableAttribute( $this->image_id, $this->image_version );
        $this->original_image = $attribute->attribute( 'content' );

        // The working folder is derived from the current user and the image,
        // never taken from the request: a folder named by the client could
        // point anywhere, and no_save_and_quit deletes it.
        $this->key = self::workingKey( $this->image_id, $this->image_version );
        $this->working_folder = self::workingFolder( $this->image_id, $this->image_version );

        $this->image_path =
            $this->working_folder . "/" .
            $this->history_version . "-" .
            $this->original_image->attributeFromOriginal( 'filename' );

        $handler = eZClusterFileHandler::instance();
        if ( $requireWorkingCopy && !$handler->fileExists( $this->image_path ) )
        {
            self::sendError( 404, 'The working copy of the image does not exist, reopen the image editor' );
        }

        $this->prepare_region();
    }

    /**
     * Key identifying the working folder of an image for the current user
     *
     * @param int $attributeId
     * @param int $version
     * @return string
     */
    public static function workingKey( $attributeId, $version )
    {
        return eZUser::currentUserID() . '/' . (int)$attributeId . '-' . (int)$version;
    }

    /**
     * Working folder of an image for the current user, relative to the root
     *
     * @param int $attributeId
     * @param int $version
     * @return string
     */
    public static function workingFolder( $attributeId, $version )
    {
        return eZSys::cacheDirectory() . '/public/ezie/' . self::workingKey( $attributeId, $version );
    }

    /**
     * Fetches an image attribute and checks that the current user may edit it
     *
     * The attribute must be an image of a draft that belongs to the current
     * user (as content/edit requires: draft, internal draft or repeat), and
     * the user must be allowed to edit the object in the attribute's language.
     * The anonymous user is refused: all anonymous visitors share one user ID,
     * so they would share each other's drafts and working folders. Answers
     * with a JSON error and ends the request otherwise.
     *
     * @param int $attributeId
     * @param int $version
     * @return eZContentObjectAttribute
     */
    public static function fetchEditableAttribute( $attributeId, $version )
    {
        if ( eZUser::currentUser()->isAnonymous() )
        {
            self::sendError( 403, 'Log in to edit images' );
        }

        $attribute = eZContentObjectAttribute::fetch( (int)$attributeId, (int)$version );
        if ( !$attribute instanceof eZContentObjectAttribute || $attribute->attribute( 'data_type_string' ) != 'ezimage' )
        {
            self::sendError( 404, 'The image does not exist' );
        }

        $object = eZContentObject::fetch( $attribute->attribute( 'contentobject_id' ) );
        $objectVersion = eZContentObjectVersion::fetchVersion( $attribute->attribute( 'version' ), $attribute->attribute( 'contentobject_id' ) );
        if ( !$object instanceof eZContentObject || !$objectVersion instanceof eZContentObjectVersion )
        {
            self::sendError( 404, 'The image does not exist' );
        }

        if ( !$object->canEdit( false, false, false, $attribute->attribute( 'language_code' ) ) )
        {
            self::sendError( 403, 'You are not allowed to edit this image' );
        }

        $status = (int)$objectVersion->attribute( 'status' );
        $editableStates = array( eZContentObjectVersion::STATUS_DRAFT,
                                 eZContentObjectVersion::STATUS_INTERNAL_DRAFT,
                                 eZContentObjectVersion::STATUS_REPEAT );
        if ( !in_array( $status, $editableStates, true ) ||
             (int)$objectVersion->attribute( 'creator_id' ) !== (int)eZUser::currentUserID() )
        {
            self::sendError( 403, 'Only an image in your own draft can be edited' );
        }

        return $attribute;
    }

    /**
     * Ends the request with a JSON error
     *
     * @param int $status HTTP status code
     * @param string $message
     * @return void (does not return)
     */
    public static function sendError( $status, $message )
    {
        $texts = array(
            400 => 'Bad Request',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error'
        );
        $status = isset( $texts[$status] ) ? (int)$status : 500;
        eZDebug::writeWarning( "ezie: $status $message", __METHOD__ );

        $protocol = isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
        header( "$protocol $status {$texts[$status]}" );
        self::sendJSON( array( 'error' => $message ) );
    }

    /**
     * Ends the request with a JSON document
     *
     * @param mixed $data
     * @return void (does not return)
     */
    public static function sendJSON( $data )
    {
        header( 'Content-Type: application/json; charset=utf-8' );
        echo is_string( $data ) ? $data : json_encode( $data );
        eZExecution::cleanExit();
    }

    /**
     * Checks that a request value is a non negative integer
     *
     * @param mixed $value
     * @return bool
     */
    public static function isUnsignedInt( $value )
    {
        return ( is_int( $value ) && $value >= 0 ) || ( is_string( $value ) && ctype_digit( $value ) );
    }

    /**
     * Applies filters to the current image, creates the new history version
     * and its thumbnail, and answers the request with the new version.
     *
     * @param array(ezcImageFilter) $filters
     * @return void (does not return)
     */
    public function apply( array $filters )
    {
        $failure = false;
        try
        {
            $imageconverter = new eZIEezcImageConverter( $filters );
            $imageconverter->perform( $this->getImagePath(), $this->getNewImagePath() );

            eZIEImageToolResize::doThumb( $this->getNewImagePath(), $this->getNewThumbnailPath() );
        }
        catch ( Exception $e )
        {
            eZDebug::writeError( get_class( $e ) . ': ' . $e->getMessage(), __METHOD__ );
            $failure = true;
        }

        // Answered outside the try block: ending the request throws on some
        // engines, and that must not be caught here.
        if ( $failure )
        {
            self::sendError( 500, 'The image could not be processed' );
        }
        self::sendJSON( (string)$this );
    }

    /**
     * Reads the selection sent by the editor, if any
     *
     * @return void
     */
    private function prepare_region()
    {
        $region = null;

        $http = eZHTTPTool::instance();
        if ( $http->hasPostVariable( 'selection' ) )
        {
            $selection = $http->postVariable( 'selection' );
            if ( is_array( $selection ) &&
                 isset( $selection['x'], $selection['y'], $selection['w'], $selection['h'] ) &&
                 is_numeric( $selection['x'] ) && is_numeric( $selection['y'] ) &&
                 is_numeric( $selection['w'] ) && is_numeric( $selection['h'] ) &&
                 $selection['x'] >= 0 && $selection['y'] >= 0 && $selection['w'] > 0 && $selection['h'] > 0 )
            {
                $region = array(
                    'x' => intval( $selection['x'] ),
                    'y' => intval( $selection['y'] ),
                    'w' => intval( $selection['w'] ),
                    'h' => intval( $selection['h'] )
                );
                if ( $region['w'] < 1 || $region['h'] < 1 )
                {
                    $region = null;
                }
            }
        }

        $this->region = $region;
    }

    /**
     * Checks if a region has been defined
     * @return bool
     */
    public function hasRegion()
    {
        return $this->region !== null;
    }

    /**
     * Region affected by the operations
     * @return array(int) Array with 4 keys: x/y & w/h
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Absolute path to the image file
     * @return string
     */
    public function getAbsoluteImagePath()
    {
        return eZSys::rootDir() . "/" . $this->getImagePath();
    }

    /**
     * Image file path
     * @note var/ezie/{user_id}/{image_id}-{image_version}/{history_version}-
     * @return string
     */
    public function getImagePath()
    {
        return $this->image_path;
    }

    /**
     * Path to the thumbnail
     * @return string
     */
    public function getThumbnailPath()
    {
        return $this->working_folder .
               "/thumb-" .
               $this->getHistoryVersion() .
               "-" .
               $this->original_image->attributeFromOriginal( 'filename' );
    }

    /**
     * Absolute path to the thumbnail
     * @return string
     */
    public function getAbsoluteThumbnailPath()
    {
        return eZSys::rootDir() . "/" . $this->getThumbnailPath();
    }

    /**
     * Absolute path to the new thumbnail version
     *
     * @return string
     */
    public function getAbsoluteNewThumbnailPath()
    {
        return eZSys::rootDir() . "/" . $this->getNewThumbnailPath();
    }

    /**
     * Path to the new thumbnail version
     * @return string
     */
    public function getNewThumbnailPath()
    {
        return $this->working_folder .
               "/thumb-" .
               $this->getNewHistoryVersion() .
               "-" .
               $this->original_image->attributeFromOriginal( 'filename' );
    }

    /**
     * Absolute path for the new image, based on the new history version
     *
     * @return string
     */
    public function getAbsoluteNewImagePath()
    {
        return eZSys::rootDir() . "/" . $this->getNewImagePath();
    }

    /**
     * Path for the new image, based on the new history version
     * @return string
     */
    public function getNewImagePath()
    {
        return $this->working_folder . "/"
         . $this->getNewHistoryVersion() . "-"
         . $this->original_image->attributeFromOriginal( 'filename' );
    }

    /**
     * Current version number
     * @return int
     */
    public function getVersion()
    {
        return $this->image_version;
    }

    /**
     * Current history version
     * @return int
     */
    public function getHistoryVersion()
    {
        return $this->history_version;
    }

    /**
     * Moves the version to the next one, and return its number
     *
     * @return int
     */
    public function getNewHistoryVersion()
    {
        return $this->history_version + 1;
    }

    /**
     * Formats the current response as a JSON string
     *
     * @return string The JSON encoded object
     */
    public function __toString(): string
    {
        $stringObject = new stdClass();

        $stringObject->image_url       = $this->getNewImagePath();
        $stringObject->thumbnail_url   = $this->getNewThumbnailPath();
        $stringObject->history_version = $this->getNewHistoryVersion();

        eZURI::transformURI( $stringObject->image_url, true );
        eZURI::transformURI( $stringObject->thumbnail_url, true );

        return json_encode( $stringObject );
    }

    /**
     * Current image editor work folder
     *
     * @return string
     */
    public function getWorkingFolder()
    {
        return $this->working_folder;
    }

    /**
     * Absolute path to the current image editor work folder
     *
     * @return string
     */
    public function getAbsoluteWorkingFolder()
    {
        return eZSys::rootDir() . "/" . $this->working_folder;
    }

    /**
     * Original image object
     * @return eZImageAliasHandler
     */
    public function getImageHandler()
    {
        return $this->original_image;
    }

    /**
     * Image identifier
     * @return int
     */
    public function getImageId()
    {
        return $this->image_id;
    }

    /**
     * Current image version
     * @return int
     */
    public function getImageVersion()
    {
        return $this->image_version;
    }

    /**
     * Key of the working folder
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}
?>
