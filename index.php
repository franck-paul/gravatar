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

if (!defined('DC_CONTEXT_ADMIN')) {return;}

$core->blog->settings->addNamespace('gravatar');
if (is_null($core->blog->settings->gravatar->active)) {
    try {
        // Add default settings values if necessary
        $core->blog->settings->gravatar->put('active', false, 'boolean', 'Active', false);
        $core->blog->settings->gravatar->put('libravatar', false, 'boolean', 'Use Libravatar.org service instead of Gravatar.com', false);
        $core->blog->settings->gravatar->put('on_post', false, 'boolean', 'Show post author Gravatar', false);
        $core->blog->settings->gravatar->put('on_comment', true, 'boolean', 'Show comment author Gravatar', false);
        $core->blog->settings->gravatar->put('size_on_post', 0, 'integer', 'Gravatar size for post author', false);
        $core->blog->settings->gravatar->put('size_on_comment', 0, 'integer', 'Gravatar size for comment author', false);
        $core->blog->settings->gravatar->put('default', '', 'string', 'Gravatar default imageset', false);
        $core->blog->settings->gravatar->put('rating', '', 'string', 'Gravatar minimum rating', false);
        $core->blog->settings->gravatar->put('style', '', 'string', 'Gravatar image style', false);

        $core->blog->triggerBlog();
        http::redirect($p_url);
    } catch (Exception $e) {
        $core->error->add($e->getMessage());
    }
}

$gv_active          = (boolean) $core->blog->settings->gravatar->active;
$gv_libravatar      = (boolean) $core->blog->settings->gravatar->libravatar;
$gv_on_post         = (boolean) $core->blog->settings->gravatar->on_post;
$gv_on_comment      = (boolean) $core->blog->settings->gravatar->on_comment;
$gv_size_on_post    = (integer) $core->blog->settings->gravatar->size_on_post;
$gv_size_on_comment = (integer) $core->blog->settings->gravatar->size_on_comment;
$gv_default         = $core->blog->settings->gravatar->default;
$gv_rating          = $core->blog->settings->gravatar->rating;
$gv_style           = $core->blog->settings->gravatar->style;

if (!empty($_POST)) {
    try
    {
        $new_cache = false;
        if ((isset($_POST['gv_active'])) && ($gv_active != (boolean) $_POST['gv_active'])) {
            $new_cache = true;
        } elseif ((isset($_POST['gv_on_post'])) && ($gv_on_post != (boolean) $_POST['gv_on_post'])) {
            $new_cache = true;
        } elseif ((isset($_POST['gv_on_comment'])) && ($gv_on_comment = (boolean) $_POST['gv_on_comment'])) {
            $new_cache = true;
        }

        $gv_active          = !empty($_POST['gv_active']);
        $gv_libravatar      = !empty($_POST['gv_libravatar']);
        $gv_on_post         = !empty($_POST['gv_on_post']);
        $gv_on_comment      = !empty($_POST['gv_on_comment']);
        $gv_size_on_post    = (integer) $_POST['gv_size_on_post'];
        $gv_size_on_comment = (integer) $_POST['gv_size_on_comment'];
        $gv_default         = $_POST['gv_default'];
        $gv_rating          = $_POST['gv_rating'];
        $gv_style           = trim($_POST['gv_style']);

        if (($gv_size_on_post < 0) || ($gv_size_on_post > 512)) {
            throw new Exception(__('The size must be between 1 and 512 pixels.'));
        }
        if (($gv_size_on_comment < 0) || ($gv_size_on_comment > 512)) {
            throw new Exception(__('The size must be between 1 and 512 pixels.'));
        }

        # Everything's fine, save options
        $core->blog->settings->addNamespace('gravatar');
        $core->blog->settings->gravatar->put('active', $gv_active);
        $core->blog->settings->gravatar->put('libravatar', $gv_libravatar);
        $core->blog->settings->gravatar->put('on_post', $gv_on_post);
        $core->blog->settings->gravatar->put('on_comment', $gv_on_comment);
        $core->blog->settings->gravatar->put('size_on_post', $gv_size_on_post);
        $core->blog->settings->gravatar->put('size_on_comment', $gv_size_on_comment);
        $core->blog->settings->gravatar->put('default', $gv_default);
        $core->blog->settings->gravatar->put('rating', $gv_rating);
        $core->blog->settings->gravatar->put('style', $gv_style);

        if ($new_cache) {
            $core->emptyTemplatesCache();
        }

        $core->blog->triggerBlog();

        dcPage::addSuccessNotice(__('Settings have been successfully updated.'));
        http::redirect($p_url);
    } catch (Exception $e) {
        $core->error->add($e->getMessage());
    }
}

$gv_defaults = [
    __('Default')   => '',
    __('mm')        => 'mm',
    __('identicon') => 'identicon',
    __('monsterid') => 'monsterid',
    __('wavatar')   => 'wavatar',
    __('retro')     => 'retro'
];

$gv_ratings = [
    __('Default') => '',
    __('G')       => 'g',
    __('PG')      => 'pg',
    __('R')       => 'r',
    __('X')       => 'x'
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

?>
<html>
<head>
	<title><?php echo __('Gravatar'); ?></title>
</head>

<body>
<?php
echo dcPage::breadcrumb(
    [
        html::escapeHTML($core->blog->name) => '',
        __('Gravatar')                      => ''
    ]);
echo dcPage::notices();

echo
'<form action="' . $p_url . '" method="post">' .
'<p>' . form::checkbox('gv_active', 1, $gv_active) . ' ' .
'<label for="gv_active" class="classic">' . __('Active Gravatars') . '</label></p>' .

'<h3>' . __('Options') . '</h3>' .

'<p>' . form::checkbox('gv_libravatar', 1, $gv_libravatar) . ' ' .
'<label for="gv_libravatar" class="classic">' . __('Use Libravatar.org service instead of Gravatar.com') . '</label></p>' .

'<p>' . form::checkbox('gv_on_post', 1, $gv_on_post) . ' ' .
'<label for="gv_on_post" class="classic">' . __('Automatically insert Gravatars for posts') . '</label></p>' .
'<p>' . form::checkbox('gv_on_comment', 1, $gv_on_comment) . ' ' .
'<label for="gv_on_comment" class="classic">' . __('Automatically insert Gravatars for comments') . '</label></p>' .

'<h3>' . __('Advanced options') . '</h3>' .

'<p><label for="gv_size_on_post" class="classic">' . __('Image size for post in pixels (1 to 512):') . '</label> ' .
form::field('gv_size_on_post', 3, 3, $gv_size_on_post) . '</p>' .
'<p><label for="gv_size_on_comment" class="classic">' . __('Image size for comment in pixels (1 to 512):') . '</label> ' .
form::field('gv_size_on_comment', 3, 3, $gv_size_on_comment) . '</p>' .
'<p><label for="gv_default" class="classic">' . __('Default Gravatar imageset:') . '</label> ' .
form::combo('gv_default', $gv_defaults, $gv_default) . '' .
    '</p>';

echo '<p><img src="' . $gv_url_test . '" alt="' . __('Default Gravatar image') . '" ' . '/></p>';

echo
'<p><label for="gv_rating" class="classic">' . __('Rating:') . '</label> ' .
form::combo('gv_rating', $gv_ratings, $gv_rating) . '</p>' .
'<p class="area"><label for="gv_style">' . __('Gravatar images CSS style:') . '</label> ' .
form::textarea('gv_style', 30, 8, html::escapeHTML($gv_style)) .
'</p>' .

'<p class="form-note">' . __('See <a href="https://en.gravatar.com/">Gravatar</a> or <a href="https://www.libravatar.org/">Libravatar</a> web sites for more information.') . '</p>' .

'<p>' . $core->formNonce() . '<input type="submit" value="' . __('Save') . '" /></p>' .
    '</form>';

?>
</body>
</html>
