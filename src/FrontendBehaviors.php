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
use Dotclear\Plugin\TemplateHelper\Code;

class FrontendBehaviors
{
    public static function publicHeadContent(): string
    {
        $settings = My::settings();

        if ($settings->active) {
            echo '<style type="text/css">' . "\n" . Helper::gravatarStyle() . "</style>\n";
        }

        return '';
    }

    public static function getGravatarURL(string $v): string
    {
        $settings = My::settings();

        if (!$settings->active) {
            return '';
        }

        if (($v === 'EntryAuthorLink') && ($settings->on_post)) {
            return Code::getPHPCode(
                self::getGravatarURLPostCode(...)
            );
        } elseif (($v === 'CommentAuthorLink') && ($settings->on_comment)) {
            return Code::getPHPCode(
                self::getGravatarURLCommentCode(...)
            );
        }

        return '';
    }

    // TemplateHelper code

    protected static function getGravatarURLPostCode(
    ): void {
        echo (new \Dotclear\Helper\Html\Form\Img(\Dotclear\Plugin\gravatar\Helper::gravatarHelper(true)))
            ->alt('')
            ->class('gravatar')
            ->loading('lazy')
            ->extra(\Dotclear\Plugin\gravatar\Helper::gravatarSizeHelper(true))
        ->render();
    }

    protected static function getGravatarURLCommentCode(
    ): void {
        if (!App::frontend()->context()->comments->comment_trackback) {
            echo (new \Dotclear\Helper\Html\Form\Img(\Dotclear\Plugin\gravatar\Helper::gravatarHelper(false)))
                ->alt('')
                ->class('gravatar')
                ->loading('lazy')
                ->extra(\Dotclear\Plugin\gravatar\Helper::gravatarSizeHelper(false))
            ->render();
        }
    }
}
