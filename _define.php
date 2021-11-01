<?php
/**
 * @brief gravatar, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'Gravatar',                                                          // Name
    'Add Gravatar/Libravatar images to your posts and comments authors', // Description
    'Franck Paul',                                                       // Author
    '0.9',                                                               // Version
    [
        'requires'    => [['core', '2.13']], // Dependencies
        'permissions' => 'admin',            // Permissions
        'type'        => 'plugin',           // Type
        'settings'    => [],                 // Settings

        'details'    => 'https://open-time.net/?q=gravatar',       // Details URL
        'support'    => 'https://github.com/franck-paul/gravatar', // Support URL
        'repository' => 'https://raw.githubusercontent.com/franck-paul/gravatar/master/dcstore.xml'
    ]
);
