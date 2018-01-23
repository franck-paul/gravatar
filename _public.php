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

if (!defined('DC_RC_PATH')) {return;}

$core->addBehavior('templateAfterValue', array('dcGravatar', 'getGravatarURL'));
$core->addBehavior('publicHeadContent', array('dcGravatar', 'publicHeadContent'));

$core->tpl->addValue('EntryAuthorGravatar', array('dcGravatar', 'EntryAuthorGravatar'));
$core->tpl->addValue('CommentAuthorGravatar', array('dcGravatar', 'CommentAuthorGravatar'));

class dcGravatar
{
    // Templates

    public static function EntryAuthorGravatar($attr)
    {
        global $core;

        $ret = '';
        if ($core->blog->settings->gravatar->active) {
            $ret = ' <img src="' . '<?php echo dcGravatar::gravatarHelper(true); ?>' . '" ' .
                '<?php echo dcGravatar::gravatarSizeHelper(true) ?> alt="" class="gravatar" />';
        }
        return $ret;
    }

    public static function CommentAuthorGravatar($attr)
    {
        global $core;

        $ret = '';
        if ($core->blog->settings->gravatar->active) {
            $ret = '<?php if (!$_ctx->comments->comment_trackback) : ?>' .
                ' <img src="' . '<?php echo dcGravatar::gravatarHelper(false); ?>' . '" ' .
                '<?php echo dcGravatar::gravatarSizeHelper(false) ?> alt="" class="gravatar" />' .
                '<?php endif; ?>';
        }
        return $ret;
    }

    // Behaviours

    public static function getGravatarURL($core, $v, $attr)
    {
        $ret = '';
        if ($core->blog->settings->gravatar->active) {
            if (($v == 'EntryAuthorLink') && ($core->blog->settings->gravatar->on_post)) {
                $ret = ' <img src="' . '<?php echo dcGravatar::gravatarHelper(true); ?>' . '" ' .
                    '<?php echo dcGravatar::gravatarSizeHelper(true) ?> alt="" class="gravatar" />';
            } elseif (($v == 'CommentAuthorLink') && ($core->blog->settings->gravatar->on_comment)) {
                $ret = '<?php if (!$_ctx->comments->comment_trackback) : ?>' .
                    ' <img src="' . '<?php echo dcGravatar::gravatarHelper(false); ?>' . '" ' .
                    '<?php echo dcGravatar::gravatarSizeHelper(false) ?> alt="" class="gravatar" />' .
                    '<?php endif; ?>';
            }
        }
        return $ret;
    }

    public static function publicHeadContent($core)
    {
        if ($core->blog->settings->gravatar->active) {
            echo '<style type="text/css">' . "\n" . self::gravatarStyle() . "</style>\n";
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
            '.gravatar {' . "\n" .
            '	' . $s . "\n" .
            '}' . "\n";
    }

    public static function gravatarSizeHelper($from_post)
    {
        global $core;

        $size = 80;
        if ($from_post && $core->blog->settings->gravatar->size_on_post != 0) {
            $size = $core->blog->settings->gravatar->size_on_post;
        } elseif (!$from_post && $core->blog->settings->gravatar->size_on_comment != 0) {
            $size = $core->blog->settings->gravatar->size_on_comment;
        }

        return sprintf('width="%1$s" height="%1$s"', $size);
    }

    /**
     * Get the target to use. (from https://github.com/pear/Services_Libravatar/blob/master/Services/Libravatar.php)
     *
     * Get the SRV record, filtered by priority and weight. If our domain
     * has no SRV records, fall back to Libravatar.org
     *
     * @param string  $domain A string of the domain we extracted from the provided identifier with domainGet()
     * @param boolean $https  Whether or not to look for https records
     *
     * @return string The target URL.
     */
    protected static function srvGet($domain, $https = false)
    {
        // Are we going secure? Set up a fallback too.
        if (isset($https) && $https === true) {
            $subdomain = '_avatars-sec._tcp.';
            $fallback  = 'seccdn.';
            $port      = 443;
        } else {
            $subdomain = '_avatars._tcp.';
            $fallback  = 'cdn.';
            $port      = 80;
        }
        if ($domain === null) {
            // No domain means invalid email address/openid
            return $fallback . 'libravatar.org';
        }
        // Lets try get us some records based on the choice of subdomain
        // and the domain we had passed in.
        $srv = dns_get_record($subdomain . $domain, DNS_SRV);
        // Did we get anything? No?
        if (count($srv) == 0) {
            // Then let's try Libravatar.org.
            return $fallback . 'libravatar.org';
        }
        // Sort by the priority. We must get the lowest.
        usort($srv, function ($a, $b) {return $a['pri'] - $b['pri'];});
        $top = $srv[0];
        $sum = 0;
        // Try to adhere to RFC2782's weighting algorithm, page 3
        // "arrange all SRV RRs (that have not been ordered yet) in any order,
        // except that all those with weight 0 are placed at the beginning of
        // the list."
        shuffle($srv);
        $srvs = array();
        foreach ($srv as $s) {
            if ($s['weight'] == 0) {
                array_unshift($srvs, $s);
            } else {
                array_push($srvs, $s);
            }
        }
        foreach ($srvs as $s) {
            if ($s['pri'] == $top['pri']) {
                // "Compute the sum of the weights of those RRs"
                $sum += (int) $s['weight'];
                // "and with each RR associate the running sum in the selected
                // order."
                $pri[$sum] = $s;
            }
        }
        // "Then choose a uniform random number between 0 and the sum computed
        // (inclusive)"
        $random = rand(0, $sum);
        // "and select the RR whose running sum value is the first in the selected
        // order which is greater than or equal to the random number selected"
        foreach ($pri as $k => $v) {
            if ($k >= $random) {
                $target = $v['target'];
                if ($v['port'] !== $port) {
                    $target .= ':' . $v['port'];
                }
                return $target;
            }
        }
    }

    public static function gravatarHelper($from_post)
    {
        global $core, $_ctx;

        $email = $from_post ? $_ctx->posts->getAuthorEmail(false) : $_ctx->comments->getEmail(false);
        $email = trim($email);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        if ($core->blog->settings->gravatar->libravatar) {
            if ($email) {
                $parts  = explode('@', $email);
                $domain = $parts[1];
            } else {
                $domain = null;
            }
            $service = 'https://' . self::srvGet($domain, true);
        } else {
            $service = 'https://secure.gravatar.com/';
        }

        $email = (!$email ? '00000000000000000000000000000000' : md5(strtolower($email)));

        $url = $service . '/avatar/' . $email;

        $query = '';
        if (($from_post) && ($core->blog->settings->gravatar->size_on_post != 0)) {
            $query .= '&s=' . $core->blog->settings->gravatar->size_on_post;
        }
        if ((!$from_post) && ($core->blog->settings->gravatar->size_on_comment != 0)) {
            $query .= '&s=' . $core->blog->settings->gravatar->size_on_comment;
        }
        if ($core->blog->settings->gravatar->default != '') {
            $query .= '&d=' . $core->blog->settings->gravatar->default;
        }
        if ($core->blog->settings->gravatar->rating != '') {
            $query .= '&r=' . $core->blog->settings->gravatar->rating;
        }
        if ($query != '') {
            $query = '?' . substr($query, 1);
        }

        return html::escapeURL($url . $query);
    }
}
