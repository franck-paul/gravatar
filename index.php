<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Dotclear 2.
#
# Copyright (c) 2003-2008 Olivier Meunier and contributors
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------

$core->blog->settings->addNamespace('gravatar');
$gv_active = (boolean) $core->blog->settings->gravatar->active;
$gv_on_post = (boolean) $core->blog->settings->gravatar->on_post;
$gv_on_comment = (boolean) $core->blog->settings->gravatar->on_comment;
$gv_size_on_post = (integer) $core->blog->settings->gravatar->size_on_post;
$gv_size_on_comment = (integer) $core->blog->settings->gravatar->size_on_comment;
$gv_default = $core->blog->settings->gravatar->default;
$gv_rating = $core->blog->settings->gravatar->rating;

if (isset($_POST['gv_active']))
{
	try
	{
		$gv_active = (boolean) $_POST['gv_active'];
		$gv_on_post = (boolean) $_POST['gv_on_post'];
		$gv_on_comment = (boolean) $_POST['on_comment'];
		$gv_size_on_post = (integer) $_POST['size_on_post'];
		$gv_size_on_comment = (integer) $_POST['size_on_comment'];
		$gv_default = $_POST['gv_default'];
		$gv_rating = $_POST['rating'];
		
		if (($gv_size_on_post < 0) || ($gv_size_on_post > 512) {
			throw new Exception(__('The size must be between 1 and 512 pixels.'));
		}
		if (($gv_size_on_comment < 0) || ($gv_size_on_comment > 512) {
			throw new Exception(__('The size must be between 1 and 512 pixels.'));
		}

		# Everything's fine, save options
		$core->blog->settings->addNamespace('gravatar');
		$core->blog->settings->gravatar->put('active',$gv_active);
		$core->blog->settings->gravatar->put('on_post',$gv_on_post);
		$core->blog->settings->gravatar->put('on_comment',$gv_on_comment);
		$core->blog->settings->gravatar->put('size_on_post',$gv_size_on_post);
		$core->blog->settings->gravatar->put('size_on_comment',$gv_size_on_comment);
		$core->blog->settings->gravatar->put('default',$gv_default);
		$core->blog->settings->gravatar->put('rating',$gv_rating);
		
		$core->blog->triggerBlog();
		http::redirect($p_url.'&upd=1');
	}
	catch (Exception $e)
	{
		$core->error->add($e->getMessage());
	}
}

$gv_defaults = array(
	__('Default') => '',
	__('mm') => 'mm',
	__('identicon') => 'identicon',
	__('monsterid') => 'monsterid',
	__('wavatar') => 'wavatar',
	__('retro') => 'retro'
);

$gv_ratings = array(
	__('Default') => '',
	__('g') => 'g',
	__('pg') => 'pg',
	__('r') => 'r',
	__('x') => 'x'
);

$gv_url_test = 'http://www.gravatar.com/avatar/00000000000000000000000000000000?f=y';
if ($gv_default != '') {
	$gv_url_test .= '?d='.$gv_default;
}

?>
<html>
<head>
	<title><?php echo __('Gravatar'); ?></title>
</head>

<body>
<?php
echo '<h2>'.html::escapeHTML($core->blog->name).' &rsaquo; '.__('Gravatar').'</h2>';

if (!empty($_GET['upd'])) {
	echo '<p class="message">'.__('Setting have been successfully updated.').'</p>';
}

echo
'<form action="'.$p_url.'" method="post">'.
'<fieldset><legend>'.__('Activation').'</legend>'.
'<p class="field"><label for="gv_active">'.__('Active:').' '.
form::checkbox('gv_active',1,$gv_active).'</label>'.'</p>'.
'<p class="field"><label for="gv_on_post">'.__('Display gravatar for posts:').' '.
form::checkbox('gv_on_post',1,$gv_on_post).'</label>'.'</p>'.
'<p class="field"><label for="gv_on_comment">'.__('Display gravatar for comments:').' '.
form::checkbox('gv_on_comment',1,$gv_on_comment).'</label>'.'</p>'.
'</fieldset>'.

'<fieldset><legend>'.__('Options').'</legend>'.
'<p class="field"><label for="gv_size_on_post">'.__('Image size for post in pixels (1 to 512):').'</label> '.
form::field('gv_size_on_post',3,3,$gv_size_on_post).'</p>'.
'<p class="field"><label for="gv_size_on_comment">'.__('Image size for comment in pixels (1 to 512):').'</label> '.
form::field('gv_size_on_comment',3,3,$gv_size_on_comment).'</p>'.
'<p class="field"><label for="gv_default">'.__('Main:').' '.
form::combo('gv_default',$gv_defaults,$gv_default).'</label>'.
'<img src="'.$gv_url_test.'" alt="'.__('Default Gravatar image').'" />'.
'</p>'.
'<p class="field"><label for="gv_rating">'.__('Main:').' '.
form::combo('gv_rating',$gv_ratings,$gv_rating).'</label>'.'</p>'.
'</fieldset>'.

'<p>'.$core->formNonce().'<input type="submit" value="'.__('save').'" /></p>'.
'</form>';

?>
</body>
</html>