<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of Gravatar, a plugin for Dotclear 2.
#
# Copyright (c) Franck Paul and contributors
# carnet.franck.paul@gmail.com
#
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_RC_PATH')) {return;}

$this->registerModule(
    "Gravatar",                                                          // Name
    "Add Gravatar/Libravatar images to your posts and comments authors", // Description
    "Franck Paul",                                                       // Author
    '0.8',                                                               // Version
    array(
        'requires'    => array(array('core', '2.13')), // Dependencies
        'permissions' => 'admin',                      // Permissions
        'type'        => 'plugin',                     // Type
        'settings'    => array()                      // Settings
    )
);
