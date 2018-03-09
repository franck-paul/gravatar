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
