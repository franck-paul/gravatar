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

use Dotclear\Helper\Html\Html;

class dcGravatar
{
    // Templates

    public static function EntryAuthorGravatar()
    {
        $ret = '';
        if (dcCore::app()->blog->settings->gravatar->active) {
            $ret = ' <img load="lazy" src="' . '<?php echo dcGravatar::gravatarHelper(true); ?>' . '" ' .
                '<?php echo dcGravatar::gravatarSizeHelper(true) ?> alt="" class="gravatar" />';
        }

        return $ret;
    }

    public static function CommentAuthorGravatar()
    {
        $ret = '';
        if (dcCore::app()->blog->settings->gravatar->active) {
            $ret = '<?php if (!dcCore::app()->ctx->comments->comment_trackback) : ?>' .
                ' <img load="lazy" src="' . '<?php echo dcGravatar::gravatarHelper(false); ?>' . '" ' .
                '<?php echo dcGravatar::gravatarSizeHelper(false) ?> alt="" class="gravatar" />' .
                '<?php endif; ?>';
        }

        return $ret;
    }

    // Behaviours

    public static function getGravatarURL($v)
    {
        $ret = '';
        if (dcCore::app()->blog->settings->gravatar->active) {
            if (($v == 'EntryAuthorLink') && (dcCore::app()->blog->settings->gravatar->on_post)) {
                $ret = ' <img load="lazy" src="' . '<?php echo dcGravatar::gravatarHelper(true); ?>' . '" ' .
                    '<?php echo dcGravatar::gravatarSizeHelper(true) ?> alt="" class="gravatar" />';
            } elseif (($v == 'CommentAuthorLink') && (dcCore::app()->blog->settings->gravatar->on_comment)) {
                $ret = '<?php if (!dcCore::app()->ctx->comments->comment_trackback) : ?>' .
                    ' <img load="lazy" src="' . '<?php echo dcGravatar::gravatarHelper(false); ?>' . '" ' .
                    '<?php echo dcGravatar::gravatarSizeHelper(false) ?> alt="" class="gravatar" />' .
                    '<?php endif; ?>';
            }
        }

        return $ret;
    }

    public static function publicHeadContent()
    {
        if (dcCore::app()->blog->settings->gravatar->active) {
            echo '<style type="text/css">' . "\n" . self::gravatarStyle() . "</style>\n";
        }
    }

    // Helpers

    public static function gravatarStyle()
    {
        $s = dcCore::app()->blog->settings->gravatar->style;
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
        $size = 80;
        if ($from_post && dcCore::app()->blog->settings->gravatar->size_on_post != 0) {
            $size = dcCore::app()->blog->settings->gravatar->size_on_post;
        } elseif (!$from_post && dcCore::app()->blog->settings->gravatar->size_on_comment != 0) {
            $size = dcCore::app()->blog->settings->gravatar->size_on_comment;
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
        if ($https) {
            $subdomain = '_avatars-sec._tcp.';
            $fallback  = 'seccdn.';
            $port      = 443;
        } else {
            $subdomain = '_avatars._tcp.';
            $fallback  = 'cdn.';
            $port      = 80;
        }
        if ($domain == null) {
            // No domain means invalid email address/openid
            return $fallback . 'libravatar.org';
        }
        // Lets try get us some records based on the choice of subdomain
        // and the domain we had passed in.
        $srv = @dns_get_record($subdomain . $domain, DNS_SRV);
        // Did we get anything? No?
        if (!$srv) {
            // Then let's try Libravatar.org.
            return $fallback . 'libravatar.org';
        }
        // Sort by the priority. We must get the lowest.
        usort($srv, fn ($a, $b) => $a['pri'] - $b['pri']);
        $top = $srv[0];
        $sum = 0;
        // Try to adhere to RFC2782's weighting algorithm, page 3
        // "arrange all SRV RRs (that have not been ordered yet) in any order,
        // except that all those with weight 0 are placed at the beginning of
        // the list."
        shuffle($srv);
        $srvs = [];
        foreach ($srv as $s) {
            if ($s['weight'] == 0) {
                array_unshift($srvs, $s);
            } else {
                array_push($srvs, $s);
            }
        }
        $pri = [];
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
        $random = random_int(0, $sum);
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
        // Nothing found, return fallback
        return $fallback . 'libravatar.org';
    }

    public static function gravatarHelper($from_post)
    {
        $email = $from_post ? dcCore::app()->ctx->posts->getAuthorEmail(false) : dcCore::app()->ctx->comments->getEmail(false);
        $email = trim((string) $email);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        if (dcCore::app()->blog->settings->gravatar->libravatar) {
            if ($email) {
                $parts  = explode('@', $email);
                $domain = $parts[1];
            } else {
                $domain = null;
            }
            $service = 'https://' . self::srvGet($domain, true);
        } else {
            $service = 'https://secure.gravatar.com';
        }

        $email = (!$email ? '00000000000000000000000000000000' : md5(strtolower($email)));

        $url = $service . '/avatar/' . $email;

        $query = '';
        if (($from_post) && (dcCore::app()->blog->settings->gravatar->size_on_post != 0)) {
            $query .= '&s=' . dcCore::app()->blog->settings->gravatar->size_on_post;
        }
        if ((!$from_post) && (dcCore::app()->blog->settings->gravatar->size_on_comment != 0)) {
            $query .= '&s=' . dcCore::app()->blog->settings->gravatar->size_on_comment;
        }
        if (dcCore::app()->blog->settings->gravatar->default != '') {
            $query .= '&d=' . dcCore::app()->blog->settings->gravatar->default;
        }
        if (dcCore::app()->blog->settings->gravatar->rating != '') {
            $query .= '&r=' . dcCore::app()->blog->settings->gravatar->rating;
        }
        if ($query != '') {
            $query = '?' . substr($query, 1);
        }

        return Html::escapeURL($url . $query);
    }
}

dcCore::app()->addBehaviors([
    'templateAfterValueV2' => [dcGravatar::class, 'getGravatarURL'],
    'publicHeadContent'    => [dcGravatar::class, 'publicHeadContent'],
]);

dcCore::app()->tpl->addValue('EntryAuthorGravatar', [dcGravatar::class, 'EntryAuthorGravatar']);
dcCore::app()->tpl->addValue('CommentAuthorGravatar', [dcGravatar::class, 'CommentAuthorGravatar']);
