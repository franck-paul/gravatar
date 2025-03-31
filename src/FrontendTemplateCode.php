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

class FrontendTemplateCode
{
    // Templates

    public static function EntryAuthorGravatar(
    ): void {
        echo (new \Dotclear\Helper\Html\Form\Img(\Dotclear\Plugin\gravatar\Helper::gravatarHelper(true)))
            ->alt('')
            ->class('gravatar')
            ->loading('lazy')
            ->extra(\Dotclear\Plugin\gravatar\Helper::gravatarSizeHelper(true))
        ->render();
    }

    public static function CommentAuthorGravatar(
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
