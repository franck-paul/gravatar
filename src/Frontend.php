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
use dcNsProcess;

class Frontend extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = My::checkContext(My::FRONTEND);

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        dcCore::app()->addBehaviors([
            'templateAfterValueV2' => [FrontendBehaviors::class, 'getGravatarURL'],
            'publicHeadContent'    => [FrontendBehaviors::class, 'publicHeadContent'],
        ]);

        dcCore::app()->tpl->addValue('EntryAuthorGravatar', [FrontendTemplate::class, 'EntryAuthorGravatar']);
        dcCore::app()->tpl->addValue('CommentAuthorGravatar', [FrontendTemplate::class, 'CommentAuthorGravatar']);

        return true;
    }
}