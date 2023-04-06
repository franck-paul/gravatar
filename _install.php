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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

if (!dcCore::app()->newVersion(basename(__DIR__), dcCore::app()->plugins->moduleInfo(basename(__DIR__), 'version'))) {
    return;
}

try {
    dcCore::app()->blog->settings->gravatar->put('active', false, 'boolean', 'Active', false, true);
    dcCore::app()->blog->settings->gravatar->put('libravatar', false, 'boolean', 'Use Libravatar.org service instead of Gravatar.com', false, true);
    dcCore::app()->blog->settings->gravatar->put('on_post', false, 'boolean', 'Show post author Gravatar', false, true);
    dcCore::app()->blog->settings->gravatar->put('on_comment', true, 'boolean', 'Show comment author Gravatar', false, true);
    dcCore::app()->blog->settings->gravatar->put('size_on_post', 0, 'integer', 'Gravatar size for post author', false, true);
    dcCore::app()->blog->settings->gravatar->put('size_on_comment', 0, 'integer', 'Gravatar size for comment author', false, true);
    dcCore::app()->blog->settings->gravatar->put('default', '', 'string', 'Gravatar default imageset', false, true);
    dcCore::app()->blog->settings->gravatar->put('rating', '', 'string', 'Gravatar minimum rating', false, true);
    dcCore::app()->blog->settings->gravatar->put('style', '', 'string', 'Gravatar image style', false, true);

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

return false;
