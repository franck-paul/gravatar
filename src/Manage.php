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
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Number;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Select;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Form\Textarea;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Process\TraitProcess;
use Exception;

class Manage
{
    use TraitProcess;

    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        return self::status(My::checkContext(My::MANAGE));
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        $settings = My::settings();

        if (is_null($settings->active)) {
            try {
                // Add default settings values if necessary
                $settings->put('active', false, App::blogWorkspace()::NS_BOOL, 'Active', false);
                $settings->put('libravatar', false, App::blogWorkspace()::NS_BOOL, 'Use Libravatar.org service instead of Gravatar.com', false);
                $settings->put('on_post', false, App::blogWorkspace()::NS_BOOL, 'Show post author Gravatar', false);
                $settings->put('on_comment', true, App::blogWorkspace()::NS_BOOL, 'Show comment author Gravatar', false);
                $settings->put('size_on_post', 0, App::blogWorkspace()::NS_INT, 'Gravatar size for post author', false);
                $settings->put('size_on_comment', 0, App::blogWorkspace()::NS_INT, 'Gravatar size for comment author', false);
                $settings->put('default', '', App::blogWorkspace()::NS_STRING, 'Gravatar default imageset', false);
                $settings->put('rating', '', App::blogWorkspace()::NS_STRING, 'Gravatar minimum rating', false);
                $settings->put('style', '', App::blogWorkspace()::NS_STRING, 'Gravatar image style', false);

                App::blog()->triggerBlog();
                My::redirect();
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        if ($_POST !== []) {
            // Post data helpers
            $_Bool = fn (string $name): bool => !empty($_POST[$name]);
            $_Int  = fn (string $name, int $default = 0): int => isset($_POST[$name]) && is_numeric($val = $_POST[$name]) ? (int) $val : $default;
            $_Str  = fn (string $name, string $default = ''): string => isset($_POST[$name]) && is_string($val = $_POST[$name]) ? $val : $default;

            try {
                $active     = (bool) $settings->active;
                $on_post    = (bool) $settings->on_post;
                $on_comment = (bool) $settings->on_comment;

                $new_cache = ($active !== $_Bool('gv_active') || $on_post !== $_Bool('gv_on_post') || $on_comment !== $_Bool('gv_on_comment'));

                $active          = $_Bool('gv_active');
                $libravatar      = $_Bool('gv_libravatar');
                $on_post         = $_Bool('gv_on_post');
                $on_comment      = $_Bool('gv_on_comment');
                $size_on_post    = $_Int('gv_size_on_post');
                $size_on_comment = $_Int('gv_size_on_comment');
                $default         = $_Str('gv_default');
                $rating          = $_Str('gv_rating');
                $style           = trim($_Str('gv_style'));

                if (($size_on_post < 0) || ($size_on_post > 512)) {
                    throw new Exception(__('The size must be between 1 and 512 pixels.'));
                }

                if (($size_on_comment < 0) || ($size_on_comment > 512)) {
                    throw new Exception(__('The size must be between 1 and 512 pixels.'));
                }

                # Everything's fine, save options
                $settings->put('active', $active, App::blogWorkspace()::NS_BOOL);
                $settings->put('libravatar', $libravatar, App::blogWorkspace()::NS_BOOL);
                $settings->put('on_post', $on_post, App::blogWorkspace()::NS_BOOL);
                $settings->put('on_comment', $on_comment, App::blogWorkspace()::NS_BOOL);
                $settings->put('size_on_post', $size_on_post, App::blogWorkspace()::NS_INT);
                $settings->put('size_on_comment', $size_on_comment, App::blogWorkspace()::NS_INT);
                $settings->put('default', $default, App::blogWorkspace()::NS_STRING);
                $settings->put('rating', $rating, App::blogWorkspace()::NS_STRING);
                $settings->put('style', $style, App::blogWorkspace()::NS_STRING);

                if ($new_cache) {
                    App::cache()->emptyTemplatesCache();
                }

                App::blog()->triggerBlog();

                App::backend()->notices()->addSuccessNotice(__('Settings have been successfully updated.'));
                My::redirect();
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        // Variable data helpers
        $_Bool = fn (mixed $var): bool => (bool) $var;
        $_Int  = fn (mixed $var, int $default = 0): int => $var !== null && is_numeric($val = $var) ? (int) $val : $default;
        $_Str  = fn (mixed $var, string $default = ''): string => $var !== null && is_string($val = $var) ? $val : $default;

        $settings = My::settings();

        $active          = $_Bool($settings->active);
        $libravatar      = $_Bool($settings->libravatar);
        $on_post         = $_Bool($settings->on_post);
        $on_comment      = $_Bool($settings->on_comment);
        $size_on_post    = $_Int($settings->size_on_post);
        $size_on_comment = $_Int($settings->size_on_comment);
        $default         = $_Str($settings->default);
        $rating          = $_Str($settings->rating);
        $style           = $_Str($settings->style);

        $defaults = [
            __('Default')   => '',
            __('mm')        => 'mm',
            __('identicon') => 'identicon',
            __('monsterid') => 'monsterid',
            __('wavatar')   => 'wavatar',
            __('retro')     => 'retro',
        ];

        $ratings = [
            __('Default') => '',
            __('G')       => 'g',
            __('PG')      => 'pg',
            __('R')       => 'r',
            __('X')       => 'x',
        ];

        $url_test = ($libravatar ?
            'https://seccdn.libravatar.org/avatar/%s' :
            'https://secure.gravatar.com/avatar/%s?f=y');
        $hash_test = ($libravatar ?
            '40f8d096a3777232204cb3f796c577b7' :
            '00000000000000000000000000000000');

        $url_test = sprintf($url_test, $hash_test);
        if ($default !== '') {
            $url_test .= ($libravatar ? '?' : '&') . 'd=' . $default;
        }

        App::backend()->page()->openModule(My::name());

        echo App::backend()->page()->breadcrumb(
            [
                Html::escapeHTML(App::blog()->name()) => '',
                __('Gravatar')                        => '',
            ]
        );
        echo App::backend()->notices()->getNotices();

        // Form

        echo
        (new Form('gv_params'))
            ->action(App::backend()->getPageURL())
            ->method('post')
            ->fields([
                (new Para())->items([
                    (new Checkbox('gv_active', $active))
                        ->value(1)
                        ->label((new Label(__('Active Gravatars'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Text('h3', __('Options'))),
                (new Para())->items([
                    (new Checkbox('gv_libravatar', $libravatar))
                        ->value(1)
                        ->label((new Label(__('Use Libravatar.org service instead of Gravatar.com'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Para())->items([
                    (new Checkbox('gv_on_post', $on_post))
                        ->value(1)
                        ->label((new Label(__('Automatically insert Gravatars for posts'), Label::INSIDE_TEXT_AFTER))),
                    (new Checkbox('gv_on_comment', $on_comment))
                        ->value(1)
                        ->label((new Label(__('Automatically insert Gravatars for comments'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Text('h3', __('Advanced options'))),
                (new Para())->items([
                    (new Number('gv_size_on_post', 1, 512, $size_on_post))
                        ->default(48)
                        ->label((new Label(__('Image size for post in pixels (1 to 512):'), Label::INSIDE_TEXT_BEFORE))),
                ]),
                (new Para())->items([
                    (new Number('gv_size_on_comment', 1, 512, $size_on_comment))
                        ->default(48)
                        ->label((new Label(__('Image size for comment in pixels (1 to 512):'), Label::INSIDE_TEXT_BEFORE))),
                ]),
                (new Para())->items([
                    (new Select('gv_default'))
                    ->items($defaults)
                    ->default($default)
                    ->label((new Label(__('Default Gravatar imageset:'), Label::INSIDE_TEXT_BEFORE))),
                ]),
                (new Para())->items([
                    (new Text(null, '<img src="' . $url_test . '" alt="' . __('Default Gravatar image') . '" ' . '>')),
                ]),
                (new Para())->items([
                    (new Select('gv_rating'))
                    ->items($ratings)
                    ->default($rating)
                    ->label((new Label(__('Rating:'), Label::INSIDE_TEXT_BEFORE))),
                ]),
                (new Para())->items([
                    (new Textarea('gv_style'))
                        ->cols(72)
                        ->rows(25)
                        ->value(Html::escapeHTML($style))
                        ->class('maximal')
                        ->label((new Label(__('Gravatar images CSS style:'), Label::OUTSIDE_LABEL_BEFORE))),
                ]),
                (new Para())->class('form-note')->items([
                    (new Text(null, __('See <a href="https://en.gravatar.com/">Gravatar</a> or <a href="https://www.libravatar.org/">Libravatar</a> web sites for more information.'))),
                ]),
                // Submit
                (new Para())->items([
                    (new Submit(['frmsubmit']))
                        ->value(__('Save')),
                    ...My::hiddenFields(),
                ]),
            ])
        ->render();

        App::backend()->page()->closeModule();
    }
}
