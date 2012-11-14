<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Dotclear 2.
#
# Copyright (c) 2003-2012 Franck Paul
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
$gv_style = $core->blog->settings->gravatar->style;

if (!empty($_POST))
{
	try
	{
		$new_cache = false;
		if ($gv_active != (boolean) $_POST['gv_active']) {
			$new_cache = true;
		} elseif ($gv_on_post != (boolean) $_POST['gv_on_post']) {
			$new_cache = true;
		} elseif ($gv_on_comment = (boolean) $_POST['gv_on_comment']) {
			$new_cache = true;
		}
		
		$gv_active = (boolean) $_POST['gv_active'];
		$gv_on_post = (boolean) $_POST['gv_on_post'];
		$gv_on_comment = (boolean) $_POST['gv_on_comment'];
		$gv_size_on_post = (integer) $_POST['gv_size_on_post'];
		$gv_size_on_comment = (integer) $_POST['gv_size_on_comment'];
		$gv_default = $_POST['gv_default'];
		$gv_rating = $_POST['gv_rating'];
		$gv_style = trim($_POST['gv_style']);
		
		if (($gv_size_on_post < 0) || ($gv_size_on_post > 512)) {
			throw new Exception(__('The size must be between 1 and 512 pixels.'));
		}
		if (($gv_size_on_comment < 0) || ($gv_size_on_comment > 512)) {
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
		$core->blog->settings->gravatar->put('style',$gv_style);
		
		if ($new_cache) $core->emptyTemplatesCache();
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
	__('G') => 'g',
	__('PG') => 'pg',
	__('R') => 'r',
	__('X') => 'x'
);

$gv_url_test = 'http://www.gravatar.com/avatar/00000000000000000000000000000000?f=y';
if ($gv_default != '') {
	$gv_url_test .= '&d='.$gv_default;
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
	dcPage::message(__('Settings have been successfully updated.'));
}

echo
'<form action="'.$p_url.'" method="post">'.
'<fieldset><legend>'.__('Activation').'</legend>'.
'<p class="field"><label for="gv_active">'.__('Active:').'</label> '.
form::checkbox('gv_active',1,$gv_active).'</p>'.
'<p class="field"><label for="gv_on_post">'.__('Automatically insert Gravatars for posts:').'</label> '.
form::checkbox('gv_on_post',1,$gv_on_post).'</p>'.
'<p class="field"><label for="gv_on_comment">'.__('Automatically insert Gravatars for comments:').'</label> '.
form::checkbox('gv_on_comment',1,$gv_on_comment).'</p>'.
'</fieldset>'.

'<fieldset><legend>'.__('Options').'</legend>'.
'<p class="field"><label for="gv_size_on_post">'.__('Image size for post in pixels (1 to 512):').'</label> '.
form::field('gv_size_on_post',3,3,$gv_size_on_post).'</p>'.
'<p class="field"><label for="gv_size_on_comment">'.__('Image size for comment in pixels (1 to 512):').'</label> '.
form::field('gv_size_on_comment',3,3,$gv_size_on_comment).'</p>'.
'<p class="field"><label for="gv_default">'.__('Default Gravatar imageset:').'</label> '.
form::combo('gv_default',$gv_defaults,$gv_default).''.
'<img src="'.$gv_url_test.'" alt="'.__('Default Gravatar image').'" '.
//($gv_style != '' ? 'style="'.$gv_style.'"' : '').
'/>'.
'</p>'.
'<p class="field"><label for="gv_rating">'.__('Rating:').'</label> '.
form::combo('gv_rating',$gv_ratings,$gv_rating).'</p>'.
'<p class="area"><label for="gv_style">'.__('Gravatar images CSS style:').'</label> '.
form::textarea('gv_style',30,8,html::escapeHTML($gv_style)).
'</p>'.
'</fieldset>'.

'<p class="form-note">'.__('See <a href="http://en.gravatar.com/">Gravatar web site</a> for more information.').'</p>'.

'<p>'.$core->formNonce().'<input type="submit" value="'.__('Save').'" /></p>'.
'</form>';

?>
</body>
</html>