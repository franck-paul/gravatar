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
    '5.0',
    [
        'date'     => '2025-03-31T14:11:32+0200',
        'requires' => [
            ['core', '2.34'],
            ['TemplateHelper'],
        ],
        'permissions' => 'My',
        'type'        => 'plugin',
        'settings'    => [],

        'details'    => 'https://open-time.net/?q=gravatar',
        'support'    => 'https://github.com/franck-paul/gravatar',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/gravatar/main/dcstore.xml',
        'license'    => 'gpl2',
    ]
);
