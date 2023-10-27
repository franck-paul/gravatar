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

class FrontendTemplate
{
    // Templates

    public static function EntryAuthorGravatar(): string
    {
        $settings = My::settings();

        $ret = '';
        if ($settings->active) {
            $ret = ' <img load="lazy" src="<?php echo ' . Helper::class . '::gravatarHelper(true); ?>' . '" ' .
                '<?php echo ' . Helper::class . '::gravatarSizeHelper(true) ?> alt="" class="gravatar" />';
        }

        return $ret;
    }

    public static function CommentAuthorGravatar(): string
    {
        $settings = My::settings();

        $ret = '';
        if ($settings->active) {
            $ret = '<?php if (!App::frontend()->context()->comments->comment_trackback) : ?> <img load="lazy" src="' . '<?php echo ' . Helper::class . '::gravatarHelper(false); ?>' . '" ' .
                '<?php echo ' . Helper::class . '::gravatarSizeHelper(false) ?> alt="" class="gravatar" />' .
                '<?php endif; ?>';
        }

        return $ret;
    }
}
