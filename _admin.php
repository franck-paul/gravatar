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

if (!defined('DC_CONTEXT_ADMIN')) {return;}

// dead but useful code, in order to have translations
__('Gravatar') . __('Add Gravatar/Libravatar images to your posts and comments authors');

$core->addBehavior('adminPageHTTPHeaderCSP', array('gravatarBehaviors', 'adminPageHTTPHeaderCSP'));

$_menu['Blog']->addItem(__('Gravatar'),
    'plugin.php?p=gravatar',
    urldecode(dcPage::getPF('gravatar/icon.png')),
    preg_match('/plugin.php\?p=gravatar(&.*)?$/', $_SERVER['REQUEST_URI']),
    $core->auth->check('admin', $core->blog->id));

class gravatarBehaviors
{
    public static function adminPageHTTPHeaderCSP($csp)
    {
        if (!isset($csp['img-src'])) {
            $csp['img-src'] = '';
        }
        $csp['img-src'] .= ' ' . 'https://i0.wp.com https://secure.gravatar.com https://seccdn.libravatar.org';
    }
}
