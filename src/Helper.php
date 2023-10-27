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
declare(strict_types=1);

namespace Dotclear\Plugin\gravatar;

use Dotclear\App;
use Dotclear\Helper\Html\Html;

class Helper
{
    // Helpers

    public static function gravatarStyle(): string
    {
        $style = My::settings()->style;
        if ($style === null) {
            return '';
        }

        return
            '.gravatar {' . "\n" .
            '   ' . $style . "\n" .
            '}' . "\n";
    }

    public static function gravatarSizeHelper(bool $from_post): string
    {
        $settings = My::settings();
        $size     = 80;
        if ($from_post && $settings->size_on_post != 0) {
            $size = $settings->size_on_post;
        } elseif (!$from_post && $settings->size_on_comment != 0) {
            $size = $settings->size_on_comment;
        }

        return sprintf('width="%1$s" height="%1$s"', $size);
    }

    /**
     * Get the target to use. (from https://github.com/pear/Services_Libravatar/blob/master/Services/Libravatar.php)
     *
     * Get the SRV record, filtered by priority and weight. If our domain
     * has no SRV records, fall back to Libravatar.org
     *
     * @param null|string   $domain     A string of the domain we extracted from the provided identifier with domainGet()
     * @param boolean       $https      Whether or not to look for https records
     *
     * @return string The target URL.
     */
    protected static function srvGet(?string $domain, bool $https = false)
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
        usort($srv, static fn($a, $b) => $a['pri'] - $b['pri']);  // @phpstan-ignore-line
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
                $srvs[] = $s;
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

    public static function gravatarHelper(bool $from_post): string
    {
        $settings = My::settings();

        $email = $from_post ? App::frontend()->context()->posts->getAuthorEmail(false) : App::frontend()->context()->comments->getEmail(false);
        $email = trim((string) $email);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);

        if ($settings->libravatar) {
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

        $email = ($email ? md5(strtolower($email)) : '00000000000000000000000000000000');

        $url = $service . '/avatar/' . $email;

        $query = '';
        if (($from_post) && ($settings->size_on_post != 0)) {
            $query .= '&s=' . $settings->size_on_post;
        }

        if ((!$from_post) && ($settings->size_on_comment != 0)) {
            $query .= '&s=' . $settings->size_on_comment;
        }

        if ($settings->default != '') {
            $query .= '&d=' . $settings->default;
        }

        if ($settings->rating != '') {
            $query .= '&r=' . $settings->rating;
        }

        if ($query != '') {
            $query = '?' . substr($query, 1);
        }

        return Html::escapeURL($url . $query);
    }
}
