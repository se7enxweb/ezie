<?php
/**
 * File containing the eZAutoloadGenerator class.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 * @author eZIE Team
 */
class eZIEezcImageConverter
{
    /**
     * @var ezcImageConverter
     */
    private $converter;

    /**
    * Instanciantes the image converter with a set of filters
    *
    * @param array(ezcImageFilter) $filter Filters to add to the image converter
    * @return void
    * @throws ezcBaseSettingValueException Error adding the transformation
    */
    public function __construct( $filter )
    {
        $imageINI = eZINI::instance( 'image.ini' );

        // we get an array of handlers, where order of entries in array gives priority
        // for each entry, we need to check if the matching handler is enabled, and this has to be manual
        $imageHandlers = $imageINI->variable( 'ImageConverterSettings', 'ImageConverters' );
        $hasImageMagick = false;
        $hasGD2 = false;
        foreach( $imageHandlers as $imageHandler )
        {
            switch( $imageHandler )
            {
                case 'ImageMagick':
                {
                    $hasImageMagick = ( $imageINI->variable( 'ImageMagick', 'IsEnabled' ) == 'true' );
                    if ( $hasImageMagick )
                        break 2;
                } break;

                // GD2 is required for the image editor
                // @todo Make the image editor degrade as nicely as possible if GD is not bundled
                case 'GD':
                {
                    $hasGD2 =
                        $imageINI->variable( 'GD', 'IsEnabled' ) == 'true' &&
                        $imageINI->variable( 'GDSettings', 'HasGD2' ) == 'true';
                    if ( $hasGD2 )
                        break 2;
                } break;
            }
        }

        if ( $hasImageMagick )
        {
            // we need to use the ImageMagick path configured in the image.ini file
            $executable = $imageINI->variable( 'ImageMagick', 'Executable' );

            if ( eZSys::osType() == 'win32' && $imageINI->hasVariable( 'ImageMagick', 'ExecutableWin32' ) )
                $executable = $imageINI->variable( 'ImageMagick', 'ExecutableWin32' );
            else if ( eZSys::osType() == 'mac'  && $imageINI->hasVariable( 'ImageMagick', 'ExecutableMac' ) )
                $executable = $imageINI->variable( 'ImageMagick', 'ExecutableMac' );
            else if ( eZSys::osType() == 'unix' && $imageINI->hasVariable( 'ImageMagick', 'ExecutableUnix' ) )
                $executable = $imageINI->variable( 'ImageMagick', 'ExecutableUnix' );
            if ( $imageINI->hasVariable( 'ImageMagick', 'ExecutablePath' ) && $imageINI->variable( 'ImageMagick', 'ExecutablePath' ) )
                $executable = $imageINI->variable( 'ImageMagick', 'ExecutablePath' ) . eZSys::fileSeparator() . $executable;
            // @todo Remove if ezc indeed do it automatically
            // if ( eZSys::osType() == 'win32' )
            //    $executable = "\"$executable\"";

            $imageHandlerSettings = new ezcImageHandlerSettings(
                'ImageMagick', 'eZIEEzcImageMagickHandler',
                array( 'binary' => $executable )
            );
            $settings = new ezcImageConverterSettings( array( $imageHandlerSettings ) );
        }
        else
        {
            $settings = new ezcImageConverterSettings( array( new ezcImageHandlerSettings( 'GD', 'eZIEEzcGDHandler' ) ) );
        }

        $this->converter = new ezcImageConverter( $settings );

        // The site may allow output formats the editor's handler cannot write
        // (image/webp with the ImageMagick and GD handlers of the image
        // conversion component). A transformation that names one of them
        // cannot be created at all, so only the formats the converter supports
        // are passed on.
        $mimeTypes = array();
        foreach ( (array)$imageINI->variable( 'OutputSettings', 'AllowedOutputFormat' ) as $mimeType )
        {
            if ( $mimeType != '' && $this->converter->allowsOutput( $mimeType ) )
            {
                $mimeTypes[] = $mimeType;
            }
        }
        if ( empty( $mimeTypes ) )
        {
            $mimeTypes = array( 'image/jpeg', 'image/png', 'image/gif' );
        }

        $this->converter->createTransformation( 'transformation', $filter, $mimeTypes );
    }

    /**
    * Performs the ezcImageConverter transformation
    *
    * @param  string $src Source image
    * @param  string $dst Destination image
    * @return void
    */
    public function perform( $src, $dst )
    {
        // fetch the input file locally
        $inClusterHandler = eZClusterFileHandler::instance( $src );
        $inClusterHandler->fetch();

        try {
            $this->converter->transform( 'transformation', $src, $dst );
        }
        catch ( Exception $e )
        {
            $inClusterHandler->deleteLocal();
            throw $e;
        }

        // store the output file to the cluster
        $outClusterHandler = eZClusterFileHandler::instance();

        // @todo Check if the local output file can be deleted at that stage. Theorically yes.
        $outClusterHandler->fileStore( $dst, 'image' );
        
        // fixing the file permissions
        eZImageHandler::changeFilePermissions( $dst );
    }

    /**
    * Active ezcImageConverter
    * @return ezcImageConverter
    */
    public function getConverter()
    {
        return $this->converter;
    }
}

?>
