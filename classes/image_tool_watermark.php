<?php
/**
 * File containing the eZIEImageToolWatermark class.
 *
 * @copyright Copyright (C) eZ Systems AS.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package ezie
 */
class eZIEImageToolWatermark extends eZIEImageAction
{
    /**
     * Watermark filter.
     * Adds the $image watermark at the $region location
     *
     * @param  array $region array with 'x' and 'y' keys
     * @param  string $image Image file name, will be searched in the ezie design folder
     * @return array(ezcImageFilter)s
     */
    public static function filter( $region, $image )
    {
        $img_path = self::imagePath( $image );
        if ( $img_path === false )
        {
            throw new ezcBaseFileNotFoundException( (string)$image, 'watermark' );
        }

        return array(
            new ezcImageFilter(
                'watermarkAbsolute',
                array(
                    'image'  => $img_path,
                    'posX'   => $region['x'],
                    'posY'   => $region['y'],
                    'width'  => intval( $region['w'] ),
                    'height' => intval( $region['h'] )
                )
            )
        );
    }

    /**
     * Absolute path of a watermark image, or false if there is none by that name
     *
     * The watermark images are in ezie/design/standard/images/watermarks.
     * Only a plain file name is accepted, so the path cannot leave that folder.
     *
     * @param string $image Image file name
     * @return string|false
     */
    public static function imagePath( $image )
    {
        $folder = realpath( dirname( __FILE__ ) . "/../design/standard/images/watermarks" );
        if ( $folder === false || !is_string( $image ) || $image === '' || basename( $image ) !== $image || $image[0] === '.' )
        {
            return false;
        }

        $path = $folder . "/" . $image;
        return is_file( $path ) ? $path : false;
    }
}
?>