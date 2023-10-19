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
$this->registerModule(
    'Gravatar',
    'Add Gravatar/Libravatar images to your posts and comments authors',
    'Franck Paul',
    '3.0',
    [
        'requires'    => [['core', '2.28']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_ADMIN,
        ]),
        'type'     => 'plugin',
        'settings' => [],

        'details'    => 'https://open-time.net/?q=gravatar',
        'support'    => 'https://github.com/franck-paul/gravatar',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/gravatar/master/dcstore.xml',
    ]
);
