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

use Dotclear\Plugin\TemplateHelper\Code;

class FrontendTemplate
{
    // Templates

    public static function EntryAuthorGravatar(): string
    {
        $settings = My::settings();

        if (!$settings->active) {
            return '';
        }

        return Code::getPHPCode(
            FrontendTemplateCode::EntryAuthorGravatar(...)
        );
    }

    public static function CommentAuthorGravatar(): string
    {
        $settings = My::settings();

        if (!$settings->active) {
            return '';
        }

        return Code::getPHPCode(
            FrontendTemplateCode::CommentAuthorGravatar(...)
        );
    }
}
