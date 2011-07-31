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

if (!defined('DC_CONTEXT_ADMIN')){return;}

$new_version = $core->plugins->moduleInfo('gravatar','version');
$old_version = $core->getVersion('gravatar');

if (version_compare($old_version,$new_version,'>=')) return;

try
{
	$core->blog->settings->addNamespace('gravatar');
	$core->blog->settings->gravatar->put('active',false,'boolean','Active',false,true);
	$core->blog->settings->gravatar->put('on_post',false,'boolean','Show post author Gravatar',false,true);
	$core->blog->settings->gravatar->put('on_comment',true,'boolean','Show comment author Gravatar',false,true);
	$core->blog->settings->gravatar->put('size_on_post',0,'integer','Gravatar size for post author',false,true);
	$core->blog->settings->gravatar->put('size_on_comment',0,'integer','Gravatar size for comment author',false,true);
	$core->blog->settings->gravatar->put('default','','string','Gravatar default imageset',false,true);
	$core->blog->settings->gravatar->put('rating','','string','Gravatar minimum rating',false,true);
	$core->blog->settings->gravatar->put('style','','string','Gravatar image style',false,true);

	$core->setVersion('gravatar',$new_version);
	
	return true;
}
catch (Exception $e)
{
	$core->error->add($e->getMessage());
}
return false;

?>