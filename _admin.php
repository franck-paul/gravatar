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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

// dead but useful code, in order to have translations
__('Gravatar') . __('Add Gravatar/Libravatar images to your posts and comments authors');

dcCore::app()->menu[dcAdmin::MENU_BLOG]->addItem(
    __('Gravatar'),
    'plugin.php?p=gravatar',
    urldecode(dcPage::getPF('gravatar/icon.svg')),
    preg_match('/plugin.php\?p=gravatar(&.*)?$/', $_SERVER['REQUEST_URI']),
    dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([
        dcAuth::PERMISSION_ADMIN,
    ]), dcCore::app()->blog->id)
);

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

dcCore::app()->addBehavior('adminPageHTTPHeaderCSP', [gravatarBehaviors::class, 'adminPageHTTPHeaderCSP']);
