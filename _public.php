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

$core->addBehavior('templateBeforeValue',array('behaviorDiscreteCat','templateBeforeValue'));

class behaviorGravatar
{
	public static function templateBeforeValue($core,$v,$attr)
	{
		$ret = '';
		if ($core->blog->settings->gravatar->active) {
			if (($v == 'EntryAuthorLink') && ($core->blog->settings->gravatar->on_post)) {
				$ret = '<img src="'.'<?php echo behaviorGravatar::gravatarHelper(true); ?>'.'" alt="" class="gravatar" />';
			} elseif (($v == 'CommentAuthorLink') && ($core->blog->settings->gravatar->on_comment)) {
				$ret = '<img src="'.'<?php echo behaviorGravatar::gravatarHelper(false); ?>'.'" alt="" class="gravatar" />';
			}
		}
		return $ret;
	}
	
	public static function gravatarHelper($from_post)
	{
		global $core, $_ctx;
		
		$email = trim($from_post ? $_ctx->posts->getAuthorEmail(false) : $_ctx->comments->getAuthorEmail(false));
		$email = ($email == '' ? 'example@example.com' : $email);
		$email = md5(strtolower($email));
		
		$url = 'http://www.gravatar.com/avatar/'.$email;

		$query = '';
		if (($from_post) && ($core->blog->settings->gravatar->size_on_post != 0)) {
			$query .= '&s='.$core->blog->settings->gravatar->size_on_post;
		}
		if ((!$from_post) && ($core->blog->settings->gravatar->size_on_comment != 0)) {
			$query .= '&s='.$core->blog->settings->gravatar->size_on_comment;
		}
		if ($core->blog->settings->gravatar->default != 0) {
			$query .= '&d='.$core->blog->settings->gravatar->default;
		}
		if ($core->blog->settings->gravatar->rating != 0) {
			$query .= '&r='.$core->blog->settings->gravatar->rating;
		}
		if ($query != '') {
			$query = '?'.substr($query,1);
		}

		return $url.$query;
	}
}
?>