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

use dcCore;
use dcNamespace;
use dcNsProcess;
use Exception;

class Install extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = My::checkContext(My::INSTALL);

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        try {
            // Init
            $settings = dcCore::app()->blog->settings->get(My::id());

            $settings->put('active', false, dcNamespace::NS_BOOL, 'Active', false, true);
            $settings->put('libravatar', false, dcNamespace::NS_BOOL, 'Use Libravatar.org service instead of Gravatar.com', false, true);
            $settings->put('on_post', false, dcNamespace::NS_BOOL, 'Show post author Gravatar', false, true);
            $settings->put('on_comment', true, dcNamespace::NS_BOOL, 'Show comment author Gravatar', false, true);
            $settings->put('size_on_post', 0, dcNamespace::NS_INT, 'Gravatar size for post author', false, true);
            $settings->put('size_on_comment', 0, dcNamespace::NS_INT, 'Gravatar size for comment author', false, true);
            $settings->put('default', '', dcNamespace::NS_STRING, 'Gravatar default imageset', false, true);
            $settings->put('rating', '', dcNamespace::NS_STRING, 'Gravatar minimum rating', false, true);
            $settings->put('style', '', dcNamespace::NS_STRING, 'Gravatar image style', false, true);
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }
}
