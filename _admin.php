<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of Gravatar, a plugin for Dotclear 2.
#
# Copyright (c) Franck Paul and contributors
# carnet.franck.paul@gmail.com
#
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_CONTEXT_ADMIN')) { return; }

// dead but useful code, in order to have translations
__('Gravatar').__('Add Gravatar images to your posts and comments authors');

$_menu['Blog']->addItem(__('Gravatar'),'plugin.php?p=gravatar','index.php?pf=gravatar/icon.png',
		preg_match('/plugin.php\?p=gravatar(&.*)?$/',$_SERVER['REQUEST_URI']),
		$core->auth->check('admin',$core->blog->id));
?>