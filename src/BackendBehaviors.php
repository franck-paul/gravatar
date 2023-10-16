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
declare(strict_types=1);

namespace Dotclear\Plugin\gravatar;

use ArrayObject;

class BackendBehaviors
{
    /**
     * @param      ArrayObject<string, string>   $csp    The content security policies
     *
     * @return     string
     */
    public static function adminPageHTTPHeaderCSP(ArrayObject $csp): string
    {
        if (!isset($csp['img-src'])) {
            $csp['img-src'] = '';
        }
        $csp['img-src'] .= ' ' . 'https://i0.wp.com https://secure.gravatar.com https://seccdn.libravatar.org';

        return '';
    }
}
