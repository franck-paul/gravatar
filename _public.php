<?php
# -- BEGIN LICENSE BLOCK ---------------------------------------
#
# This file is part of Dotclear 2.
#
# Copyright (c) 2003-2011 Franck Paul
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK -----------------------------------------
if (!defined('DC_RC_PATH')) { return; }

$core->addBehavior('templateAfterValue',array('behaviorGravatar','getGravatarURL'));
$core->addBehavior('publicHeadContent',array('behaviorGravatar','publicHeadContent'));

class behaviorGravatar
{
	// Behaviours

	public static function getGravatarURL($core,$v,$attr)
	{
		$ret = '';
		if ($core->blog->settings->gravatar->active) {
			if (($v == 'EntryAuthorLink') && ($core->blog->settings->gravatar->on_post)) {
				$ret = ' <img src="'.'<?php echo behaviorGravatar::gravatarHelper(true); ?>'.'" alt="" class="gravatar" />';
			} elseif (($v == 'CommentAuthorLink') && ($core->blog->settings->gravatar->on_comment)) {
				$ret = ' <img src="'.'<?php echo behaviorGravatar::gravatarHelper(false); ?>'.'" alt="" class="gravatar" />';
			}
		}
		return $ret;
	}
	
	public static function publicHeadContent($core)
	{
		if (($core->blog->settings->gravatar->active) && 
			(($core->blog->settings->gravatar->on_post) || ($core->blog->settings->gravatar->on_comment))) {
			echo '<style type="text/css">'."\n".self::gravatarStyle()."</style>\n";
		}
	}

	// Helpers
	
	public static function gravatarStyle()
	{
		$s = $GLOBALS['core']->blog->settings->gravatar->style;
		if ($s === null) {
			return;
		}
		return
			'.gravatar {'."\n".
			'	'.$s."\n".
			'}'."\n";
	}

	public static function gravatarHelper($from_post)
	{
		global $core, $_ctx;
		
		$email = $from_post ? $_ctx->posts->getAuthorEmail(false) : $_ctx->comments->getEmail(false);
		$email = trim($email);
		$email = ($email == '' ? '00000000000000000000000000000000' : md5(strtolower($email)));
		
		$url = 'http://www.gravatar.com/avatar/'.$email;

		$query = '';
		if (($from_post) && ($core->blog->settings->gravatar->size_on_post != 0)) {
			$query .= '&s='.$core->blog->settings->gravatar->size_on_post;
		}
		if ((!$from_post) && ($core->blog->settings->gravatar->size_on_comment != 0)) {
			$query .= '&s='.$core->blog->settings->gravatar->size_on_comment;
		}
		if ($core->blog->settings->gravatar->default != '') {
			$query .= '&d='.$core->blog->settings->gravatar->default;
		}
		if ($core->blog->settings->gravatar->rating != '') {
			$query .= '&r='.$core->blog->settings->gravatar->rating;
		}
		if ($query != '') {
			$query = '?'.substr($query,1);
		}

		return $url.$query;
	}
}
?>