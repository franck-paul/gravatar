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

class FrontendBehaviors
{
    public static function getGravatarURL($v)
    {
        $settings = My::settings();

        $ret = '';
        if ($settings->active) {
            if (($v == 'EntryAuthorLink') && ($settings->on_post)) {
                $ret = ' <img load="lazy" src="' . '<?php echo ' . Helper::class . '::gravatarHelper(true); ?>' . '" ' .
                    '<?php echo ' . Helper::class . '::gravatarSizeHelper(true) ?> alt="" class="gravatar" />';
            } elseif (($v == 'CommentAuthorLink') && ($settings->on_comment)) {
                $ret = '<?php if (!dcCore::app()->ctx->comments->comment_trackback) : ?>' .
                    ' <img load="lazy" src="' . '<?php echo ' . Helper::class . '::gravatarHelper(false); ?>' . '" ' .
                    '<?php echo ' . Helper::class . '::gravatarSizeHelper(false) ?> alt="" class="gravatar" />' .
                    '<?php endif; ?>';
            }
        }

        return $ret;
    }

    public static function publicHeadContent()
    {
        $settings = My::settings();

        if ($settings->active) {
            echo '<style type="text/css">' . "\n" . Helper::gravatarStyle() . "</style>\n";
        }
    }
}
