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

$new_version = $core->plugins->moduleInfo('gravatar', 'version');
$old_version = $core->getVersion('gravatar');

if (version_compare($old_version, $new_version, '>=')) {return;}

try
{
    $core->blog->settings->addNamespace('gravatar');
    $core->blog->settings->gravatar->put('active', false, 'boolean', 'Active', false, true);
    $core->blog->settings->gravatar->put('libravatar', false, 'boolean', 'Use Libravatar.org service instead of Gravatar.com', false, true);
    $core->blog->settings->gravatar->put('on_post', false, 'boolean', 'Show post author Gravatar', false, true);
    $core->blog->settings->gravatar->put('on_comment', true, 'boolean', 'Show comment author Gravatar', false, true);
    $core->blog->settings->gravatar->put('size_on_post', 0, 'integer', 'Gravatar size for post author', false, true);
    $core->blog->settings->gravatar->put('size_on_comment', 0, 'integer', 'Gravatar size for comment author', false, true);
    $core->blog->settings->gravatar->put('default', '', 'string', 'Gravatar default imageset', false, true);
    $core->blog->settings->gravatar->put('rating', '', 'string', 'Gravatar minimum rating', false, true);
    $core->blog->settings->gravatar->put('style', '', 'string', 'Gravatar image style', false, true);

    $core->setVersion('gravatar', $new_version);

    return true;
} catch (Exception $e) {
    $core->error->add($e->getMessage());
}
return false;
