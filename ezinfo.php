<?php
/**
 * File containing the ezieInfo class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package ezie
 */

class ezieInfo
{
    static function info()
    {
        return array( 'Name' => "eZ Image Editor LS",
                      'Version' => "6.0.7",
                      'Copyright' => "Copyright (C) eZ Systems AS. All rights reserved.",
                      'License' => "GNU General Public License v2.0 (or any later version)",
                      'Info_url' => "https://github.com/se7enxweb/ezie",
                      'Includes the following third-party software' => array( 'Name' => 'jQuery UI',
                                                                               'Version' => '1.8.9',
                                                                               'Copyright' => 'Copyright (c) 2009, PAUL BAKAUS AND THE JQUERY UI TEAM. All rights reserved.',
                                                                               'License' => 'MIT License' ),
                      'Includes the following third-party software (2)' => array( 'Name' => 'Jcrop',
                                                                                   'Version' => '0.9.8',
                                                                                   'Copyright' => 'Copyright (c) 2008-2009 Deep Liquid Group. All rights reserved.',
                                                                                   'License' => 'MIT License' ),
                      'Includes the following third-party software (3)' => array( 'Name' => 'jQuery Hotkeys',
                                                                                   'Version' => '0.7.9',
                                                                                   'Copyright' => 'Copyright (c) 2007 - 2008 Binny V A, Tzury Bar Yochay. All rights reserved.',
                                                                                   'License' => 'MIT License' ) );
    }
}

?>
