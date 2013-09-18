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
if (!defined('DC_CONTEXT_ADMIN')) { return; }

$_menu['Blog']->addItem(__('Gravatar'),'plugin.php?p=gravatar','index.php?pf=gravatar/icon.png',
		preg_match('/plugin.php\?p=gravatar(&.*)?$/',$_SERVER['REQUEST_URI']),
		$core->auth->check('admin',$core->blog->id));
?>