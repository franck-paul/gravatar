<?php

/**
 * @brief gravatar, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\gravatar;

use ArrayObject;

class BackendBehaviors
{
    /**
     * @param      ArrayObject<string, string>   $arrayObject    The content security policies
     */
    public static function adminPageHTTPHeaderCSP(ArrayObject $arrayObject): string
    {
        $arrayObject['img-src'] ??= '';
        $arrayObject['img-src'] .= ' https://i0.wp.com https://secure.gravatar.com https://seccdn.libravatar.org';

        return '';
    }
}
