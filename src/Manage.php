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
use dcPage;
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
use Dotclear\Helper\Network\Http;
use Exception;

class Manage extends dcNsProcess
{
    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        static::$init = My::checkContext(My::MANAGE);

        return static::$init;
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        $settings = dcCore::app()->blog->settings->get(My::id());

        if (is_null($settings->active)) {
            try {
                // Add default settings values if necessary
                $settings->put('active', false, dcNamespace::NS_BOOL, 'Active', false);
                $settings->put('libravatar', false, dcNamespace::NS_BOOL, 'Use Libravatar.org service instead of Gravatar.com', false);
                $settings->put('on_post', false, dcNamespace::NS_BOOL, 'Show post author Gravatar', false);
                $settings->put('on_comment', true, dcNamespace::NS_BOOL, 'Show comment author Gravatar', false);
                $settings->put('size_on_post', 0, dcNamespace::NS_INT, 'Gravatar size for post author', false);
                $settings->put('size_on_comment', 0, dcNamespace::NS_INT, 'Gravatar size for comment author', false);
                $settings->put('default', '', dcNamespace::NS_STRING, 'Gravatar default imageset', false);
                $settings->put('rating', '', dcNamespace::NS_STRING, 'Gravatar minimum rating', false);
                $settings->put('style', '', dcNamespace::NS_STRING, 'Gravatar image style', false);

                dcCore::app()->blog->triggerBlog();
                Http::redirect(dcCore::app()->admin->getPageURL());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        if (!empty($_POST)) {
            try {
                $gv_active     = (bool) $settings->active;
                $gv_on_post    = (bool) $settings->on_post;
                $gv_on_comment = (bool) $settings->on_comment;

                $new_cache = false;
                if ((isset($_POST['gv_active'])) && ($gv_active != (bool) $_POST['gv_active'])) {
                    $new_cache = true;
                } elseif ((isset($_POST['gv_on_post'])) && ($gv_on_post != (bool) $_POST['gv_on_post'])) {
                    $new_cache = true;
                } elseif ((isset($_POST['gv_on_comment'])) && ($gv_on_comment = (bool) $_POST['gv_on_comment'])) {
                    $new_cache = true;
                }

                $gv_active          = !empty($_POST['gv_active']);
                $gv_libravatar      = !empty($_POST['gv_libravatar']);
                $gv_on_post         = !empty($_POST['gv_on_post']);
                $gv_on_comment      = !empty($_POST['gv_on_comment']);
                $gv_size_on_post    = (int) $_POST['gv_size_on_post'];
                $gv_size_on_comment = (int) $_POST['gv_size_on_comment'];
                $gv_default         = $_POST['gv_default'];
                $gv_rating          = $_POST['gv_rating'];
                $gv_style           = trim((string) $_POST['gv_style']);

                if (($gv_size_on_post < 0) || ($gv_size_on_post > 512)) {
                    throw new Exception(__('The size must be between 1 and 512 pixels.'));
                }
                if (($gv_size_on_comment < 0) || ($gv_size_on_comment > 512)) {
                    throw new Exception(__('The size must be between 1 and 512 pixels.'));
                }

                # Everything's fine, save options
                $settings->put('active', $gv_active, dcNamespace::NS_BOOL);
                $settings->put('libravatar', $gv_libravatar, dcNamespace::NS_BOOL);
                $settings->put('on_post', $gv_on_post, dcNamespace::NS_BOOL);
                $settings->put('on_comment', $gv_on_comment, dcNamespace::NS_BOOL);
                $settings->put('size_on_post', $gv_size_on_post, dcNamespace::NS_INT);
                $settings->put('size_on_comment', $gv_size_on_comment, dcNamespace::NS_INT);
                $settings->put('default', $gv_default, dcNamespace::NS_STRING);
                $settings->put('rating', $gv_rating, dcNamespace::NS_STRING);
                $settings->put('style', $gv_style, dcNamespace::NS_STRING);

                if ($new_cache) {
                    dcCore::app()->emptyTemplatesCache();
                }

                dcCore::app()->blog->triggerBlog();

                dcPage::addSuccessNotice(__('Settings have been successfully updated.'));
                Http::redirect(dcCore::app()->admin->getPageURL());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!static::$init) {
            return;
        }

        $settings = dcCore::app()->blog->settings->get(My::id());

        $gv_active          = (bool) $settings->active;
        $gv_libravatar      = (bool) $settings->libravatar;
        $gv_on_post         = (bool) $settings->on_post;
        $gv_on_comment      = (bool) $settings->on_comment;
        $gv_size_on_post    = (int) $settings->size_on_post;
        $gv_size_on_comment = (int) $settings->size_on_comment;
        $gv_default         = $settings->default;
        $gv_rating          = $settings->rating;
        $gv_style           = $settings->style;

        $gv_defaults = [
            __('Default')   => '',
            __('mm')        => 'mm',
            __('identicon') => 'identicon',
            __('monsterid') => 'monsterid',
            __('wavatar')   => 'wavatar',
            __('retro')     => 'retro',
        ];

        $gv_ratings = [
            __('Default') => '',
            __('G')       => 'g',
            __('PG')      => 'pg',
            __('R')       => 'r',
            __('X')       => 'x',
        ];

        $gv_url_test = ($gv_libravatar ?
            'https://seccdn.libravatar.org/avatar/%s' :
            'https://secure.gravatar.com/avatar/%s?f=y');
        $gv_hash_test = ($gv_libravatar ?
            '40f8d096a3777232204cb3f796c577b7' :
            '00000000000000000000000000000000');

        $gv_url_test = sprintf($gv_url_test, $gv_hash_test);
        if ($gv_default != '') {
            $gv_url_test .= ($gv_libravatar ? '?' : '&') . 'd=' . $gv_default;
        }

        dcPage::openModule(__('Gravatar'));

        echo dcPage::breadcrumb(
            [
                Html::escapeHTML(dcCore::app()->blog->name) => '',
                __('Gravatar')                              => '',
            ]
        );
        echo dcPage::notices();

        // Form

        echo
        (new Form('a11y_params'))
            ->action(dcCore::app()->admin->getPageURL())
            ->method('post')
            ->fields([
                (new Para())->items([
                    (new Checkbox('gv_active', $gv_active))
                        ->value(1)
                        ->label((new Label(__('Active Gravatars'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Text('h3', __('Options'))),
                (new Para())->items([
                    (new Checkbox('gv_libravatar', $gv_libravatar))
                        ->value(1)
                        ->label((new Label(__('Use Libravatar.org service instead of Gravatar.com'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Para())->items([
                    (new Checkbox('gv_on_post', $gv_on_post))
                        ->value(1)
                        ->label((new Label(__('Automatically insert Gravatars for posts'), Label::INSIDE_TEXT_AFTER))),
                    (new Checkbox('gv_on_comment', $gv_on_comment))
                        ->value(1)
                        ->label((new Label(__('Automatically insert Gravatars for comments'), Label::INSIDE_TEXT_AFTER))),
                ]),
                (new Text('h3', __('Advanced options'))),
                (new Para())->items([
                    (new Number('gv_size_on_post', 1, 512, $gv_size_on_post))
                        ->default(48)
                        ->label((new Label(__('Image size for post in pixels (1 to 512):'), Label::INSIDE_TEXT_BEFORE))),
                ]),
                (new Para())->items([
                    (new Number('gv_size_on_comment', 1, 512, $gv_size_on_comment))
                        ->default(48)
                        ->label((new Label(__('Image size for comment in pixels (1 to 512):'), Label::INSIDE_TEXT_BEFORE))),
                ]),
                (new Para())->items([
                    (new Select('gv_default'))
                    ->items($gv_defaults)
                    ->default($gv_default)
                    ->label((new Label(__('Default Gravatar imageset:'), Label::INSIDE_TEXT_BEFORE))),
                ]),
                (new Para())->items([
                    (new Text(null, '<img src="' . $gv_url_test . '" alt="' . __('Default Gravatar image') . '" ' . '/>')),
                ]),
                (new Para())->items([
                    (new Select('gv_rating'))
                    ->items($gv_ratings)
                    ->default($gv_rating)
                    ->label((new Label(__('Rating:'), Label::INSIDE_TEXT_BEFORE))),
                ]),
                (new Para())->items([
                    (new Textarea('gv_style'))
                        ->cols(72)
                        ->rows(25)
                        ->value(Html::escapeHTML($gv_style))
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
                    dcCore::app()->formNonce(false),
                ]),
            ])
        ->render();

        dcPage::closeModule();
    }
}
