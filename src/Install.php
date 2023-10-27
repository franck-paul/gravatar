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

use Dotclear\App;
use Dotclear\Core\Process;
use Exception;

class Install extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        try {
            // Init
            $settings = My::settings();

            $settings->put('active', false, App::blogWorkspace()::NS_BOOL, 'Active', false, true);
            $settings->put('libravatar', false, App::blogWorkspace()::NS_BOOL, 'Use Libravatar.org service instead of Gravatar.com', false, true);
            $settings->put('on_post', false, App::blogWorkspace()::NS_BOOL, 'Show post author Gravatar', false, true);
            $settings->put('on_comment', true, App::blogWorkspace()::NS_BOOL, 'Show comment author Gravatar', false, true);
            $settings->put('size_on_post', 0, App::blogWorkspace()::NS_INT, 'Gravatar size for post author', false, true);
            $settings->put('size_on_comment', 0, App::blogWorkspace()::NS_INT, 'Gravatar size for comment author', false, true);
            $settings->put('default', '', App::blogWorkspace()::NS_STRING, 'Gravatar default imageset', false, true);
            $settings->put('rating', '', App::blogWorkspace()::NS_STRING, 'Gravatar minimum rating', false, true);
            $settings->put('style', '', App::blogWorkspace()::NS_STRING, 'Gravatar image style', false, true);
        } catch (Exception $exception) {
            App::error()->add($exception->getMessage());
        }

        return true;
    }
}
