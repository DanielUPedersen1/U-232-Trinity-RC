<?php
/**
 * -------   U-232 Codename Trinity   ----------*
 * ---------------------------------------------*
 * --------  @authors U-232 Team  --------------*
 * ---------------------------------------------*
 * -----  @site https://u-232.duckdns.org/  ----*
 * ---------------------------------------------*
 * -----  @copyright 2020 U-232 Team  ----------*
 * ---------------------------------------------*
 * ------------  @version V6  ------------------*
 */
require_once (CACHE_DIR.'hit_and_run_settings.php');
//==09 Hnr mod - sir_snugglebunny
if ($TRINITY20['hnr_online'] == 1 || $CURUSER['id'] == $id || $CURUSER['class'] >= UC_POWER_USER) {
    $completed = $count2 = $dlc = '';
    if (XBT_TRACKER === false) {
    $r = sql_query("SELECT torrents.name, torrents.added AS torrent_added, snatched.complete_date AS c, snatched.downspeed, snatched.seedtime, snatched.seeder, snatched.torrentid as tid, snatched.id, categories.id as category, categories.image, categories.name as catname, snatched.uploaded, snatched.downloaded, snatched.hit_and_run, snatched.mark_of_cain, snatched.complete_date, snatched.last_action, torrents.seeders, torrents.leechers, torrents.owner, snatched.start_date AS st, snatched.start_date FROM snatched JOIN torrents ON torrents.id = snatched.torrentid JOIN categories ON categories.id = torrents.category WHERE snatched.finished='yes' AND userid=" . sqlesc($id) . " AND torrents.owner != " . sqlesc($id) . " ORDER BY snatched.id DESC") or sqlerr(__FILE__, __LINE__);
} else {
    $r = sql_query("SELECT torrents.name, torrents.added AS torrent_added, xbt_peers.mtime AS c, xbt_peers.active, xbt_peers.left, xbt_peers.tid as tid, categories.id as category, categories.image, categories.name as catname, xbt_peers.uploaded, xbt_peers.downloaded, xbt_peers.mtime, xbt_peers.uid, torrents.seeders, torrents.leechers, torrents.owner FROM xbt_peers JOIN torrents ON torrents.id = xbt_peers.tid JOIN categories ON categories.id = torrents.category WHERE xbt_peers.completed>='1' AND uid=" . sqlesc($id) . " AND torrents.owner != " . sqlesc($id) . " ORDER BY xbt_peers.tid DESC") or sqlerr(__FILE__, __LINE__);
}
    //=== completed
    if ($r->num_rows < 0) {
        $completed.= "<table class='table-expand'>
    <tr>
    <td class='text-center'>{$lang['userdetails_type']}</td>
    <td class='text-center'>{$lang['userdetails_name']}</td>
    <td class='text-center' align='center'>{$lang['userdetails_s']}</td>
    <td class='text-center' align='center'>{$lang['userdetails_l']}</td>
    <td class='text-center' align='center'>{$lang['userdetails_ul']}</td>
    " . ($TRINITY20['ratio_free'] ? "" : "<td class='text-center' align='center'>{$lang['userdetails_dl']}</td>") . "
    <td class='text-center'>{$lang['userdetails_ratio']}</td>
    <td class='text-center'>{$lang['userdetails_wcompleted']}</td>
    <td class='text-center'>{$lang['userdetails_laction']}</td>
    <td class='text-center'>{$lang['userdetails_speed']}</td></tr>";
        while ($a = $r->fetch_assoc()) {
        $What_Id = (XBT_TRACKER == true ? $a['tid'] : $a['id']);
            //=======change colors
            $count2 = (++$count2) % 2;
            $class = ($count2 == 0 ? 'one' : 'two');
            $torrent_needed_seed_time = ($a['st'] - $a['torrent_added']);
            //=== get times per class
            switch (true) {
            case ($user['class'] <= $TRINITY20['firstclass']):
                $days_3 = $TRINITY20['_3day_first'] * 3600; //== 1 days
                $days_14 = $TRINITY20['_14day_first'] * 3600; //== 1 days
                $days_over_14 = $TRINITY20['_14day_over_first'] * 3600; //== 1 day
                break;
 
            case ($user['class'] < $TRINITY20['secondclass']):
                $days_3 = $TRINITY20['_3day_second'] * 3600; //== 12 hours
                $days_14 = $TRINITY20['_14day_second'] * 3600; //== 12 hours
                $days_over_14 = $TRINITY20['_14day_over_second'] * 3600; //== 12 hours
                break;
 
            case ($user['class'] >= $TRINITY20['thirdclass']):
                $days_3 = $TRINITY20['_3day_third'] * 3600; //== 12 hours
                $days_14 = $TRINITY20['_14day_third'] * 3600; //== 12 hours
                $days_over_14 = $TRINITY20['_14day_over_third'] * 3600; //== 12 hours
                break;

	default:
            $days_3 = 0; //== 12 hours
            $days_14 = 0; //== 12 hours
            $days_over_14 = 0; //== 12 hours

            }
            //=== times per torrent based on age
            $foo = $a['downloaded'] > 0 ? $a['uploaded'] / $a['downloaded'] : 0;
            switch (true) {
            case (($a['st'] - $a['torrent_added']) < $TRINITY20['torrentage1'] * 86400):
                $minus_ratio = ($days_3 - $a['seedtime']) - ($foo * 3 * 86400);
                break;
 
            case (($a['st'] - $a['torrent_added']) < $TRINITY20['torrentage2'] * 86400):
                $minus_ratio = ($days_14 - $a['seedtime']) - ($foo * 2 * 86400);
                break;
 
            case (($a['st'] - $a['torrent_added']) >= $TRINITY20['torrentage3'] * 86400):
                $minus_ratio = ($days_over_14 - $a['seedtime']) - ($foo * 86400);
                break;

	default:
           	 $minus_ratio = 0;
            }

/*  Seems to be duplicate code.  Commented by stoner
            //=== times per torrent based on age
            $foo = $a['downloaded'] > 0 ? $a['uploaded'] / $a['downloaded'] : 0;
            switch (true) {
            case (($a['st'] - $a['torrent_added']) < 7 * 86400):
                $minus_ratio = ($days_3 - $a['seedtime']) - ($foo * 3 * 86400);
                break;

            case (($a['st'] - $a['torrent_added']) < 21 * 86400):
                $minus_ratio = ($days_14 - $a['seedtime']) - ($foo * 2 * 86400);
                break;

            case (($a['st'] - $a['torrent_added']) >= 21 * 86400):
                $minus_ratio = ($days_over_14 - $a['seedtime']) - ($foo * 86400);
                break;
            }

*/
            $color = (($minus_ratio > 0 && $a['uploaded'] < $a['downloaded']) ? get_ratio_color($minus_ratio) : 'limegreen');
            $minus_ratio = mkprettytime($minus_ratio);
            //=== speed color red fast green slow ;)
            if ($a["downspeed"] > 0) $dl_speed = ($a["downspeed"] > 0 ? mksize($a["downspeed"]) : ($a["leechtime"] > 0 ? mksize($a["downloaded"] / $a["leechtime"]) : mksize(0)));
            else $dl_speed = mksize(($a["downloaded"] / ($a['c'] - $a['st'] + 1)));
            switch (true) {
            case ($dl_speed > 600):
                $dlc = 'red';
                break;

            case ($dl_speed > 300):
                $dlc = 'orange';
                break;

            case ($dl_speed > 200):
                $dlc = 'yellow';
                break;

            case ($dl_speed < 100):
                $dlc = 'Chartreuse';
                break;
            }
            //=== mark of cain / hit and run
            $checkbox_for_delete = ($CURUSER['class'] >= UC_STAFF ? " [<a href='" . $TRINITY20['baseurl'] . "/userdetails.php?id=" . $id . "&amp;delete_hit_and_run=" . (int)$What_Id . "'>{$lang['userdetails_c_remove']}</a>]" : '');
            $mark_of_cain = ($a['mark_of_cain'] == 'yes' ? "<img src='{$TRINITY20['pic_base_url']}moc.gif' width='40px' alt='{$lang['userdetails_c_mofcain']}' title='{$lang['userdetails_c_tmofcain']}' />" . $checkbox_for_delete : '');
            $hit_n_run = ($a['hit_and_run'] > 0 ? "<img src='{$TRINITY20['pic_base_url']}hnr.gif' width='40px' alt='{$lang['userdetails_c_hitrun']}' title='{$lang['userdetails_c_hitrun1']}' />" : '');
            if (XBT_TRACKER === false)
            $completed.= "<tr><td style='padding: 0px' class='$class'><img src='{$TRINITY20['pic_base_url']}caticons/{$CURUSER['categorie_icon']}/{$a['image']}' alt='{$a['name']}' title='{$a['name']}' /></td>
    <td class='$class'><a class='altlink' href='{$TRINITY20['baseurl']}/details.php?id=" . (int)$a['tid'] . "&amp;hit=1'><b>" . htmlsafechars($a['name']) . "</b></a>
    <br /><font color='.$color.'>  " . (($CURUSER['class'] >= UC_STAFF || $user['id'] == $CURUSER['id']) ? "{$lang['userdetails_c_seedfor']}</font>: " . mkprettytime($a['seedtime']) . (($minus_ratio != '0:00' && $a['uploaded'] < $a['downloaded']) ? "<br />{$lang['userdetails_c_should']}" . $minus_ratio . "&nbsp;&nbsp;" : '') . ($a['seeder'] == 'yes' ? "&nbsp;<font color='limegreen'> [<b>{$lang['userdetails_c_seeding']}</b>]</font>" : $hit_n_run . "&nbsp;" . $mark_of_cain) : '') . "</td>
    <td align='center' class='$class'>" . (int)$a['seeders'] . "</td>
    <td align='center' class='$class'>" . (int)$a['leechers'] . "</td>
    <td align='center' class='$class'>" . mksize($a['uploaded']) . "</td>
    " . ($TRINITY20['ratio_free'] ? "" : "<td align='center' class='$class'>" . mksize($a['downloaded']) . "</td>") . "
    <td align='center' class='$class'>" . ($a['downloaded'] > 0 ? "<font color='" . get_ratio_color(number_format($a['uploaded'] / $a['downloaded'], 3)) . "'>" . number_format($a['uploaded'] / $a['downloaded'], 3) . "</font>" : ($a['uploaded'] > 0 ? 'Inf.' : '---')) . "<br /></td>
    <td align='center' class='$class'>" . get_date($a['complete_date'], 'DATE') . "</td>
    <td align='center' class='$class'>" . get_date($a['last_action'], 'DATE') . "</td>
    <td align='center' class='$class'><font color='$dlc'>[{$lang['userdetails_c_dled']}$dl_speed ]</font></td></tr>";
        else
        $completed.= "<tr><td style='padding: 0px' class='$class'><img src='{$TRINITY20['pic_base_url']}caticons/{$CURUSER['categorie_icon']}/{$a['image']}' alt='{$a['name']}' title='{$a['name']}' /></td>
    <td class='$class'><a class='altlink' href='{$TRINITY20['baseurl']}/details.php?id=" . (int)$a['tid'] . "&amp;hit=1'><b>" . htmlsafechars($a['name']) . "</b></a>
    <br /><font color='.$color.'>  " . (($CURUSER['class'] >= UC_STAFF || $user['id'] == $CURUSER['id']) ? "{$lang['userdetails_c_seedfor']}</font>: " . mkprettytime($a['seedtime']) . (($minus_ratio != '0:00' && $a['uploaded'] < $a['downloaded']) ? "<br />{$lang['userdetails_c_should']}" . $minus_ratio . "&nbsp;&nbsp;" : '') . ($a['active'] == 1 && $a['left'] = 0 ? "&nbsp;<font color='limegreen'> [<b>{$lang['userdetails_c_seeding']}</b>]</font>" : $hit_n_run) : '') . "</td>
    <td align='center' class='$class'>" . (int)$a['seeders'] . "</td>
    <td align='center' class='$class'>" . (int)$a['leechers'] . "</td>
    <td align='center' class='$class'>" . mksize($a['uploaded']) . "</td>
    " . ($TRINITY20['ratio_free'] ? "" : "<td align='center' class='$class'>" . mksize($a['downloaded']) . "</td>") . "
    <td align='center' class='$class'>" . ($a['downloaded'] > 0 ? "<font color='" . get_ratio_color(number_format($a['uploaded'] / $a['downloaded'], 3)) . "'>" . number_format($a['uploaded'] / $a['downloaded'], 3) . "</font>" : ($a['uploaded'] > 0 ? $lang['userdetails_c_inf'] : '---')) . "<br /></td>
    <td align='center' class='$class'>" . get_date($a['completedtime'], 'DATE') . "</td>
    <td align='center' class='$class'>" . get_date($a['mtime'], 'DATE') . "</td>
    <td align='center' class='$class'><font color='$dlc'>[{$lang['userdetails_c_dled']}$dl_speed ]</font></td></tr>";
        }
        $completed.= "</table>\n";
    }
    if ($completed && $CURUSER['class'] >= UC_POWER_USER || $completed && $user['id'] == $CURUSER['id']) {
        if (!isset($_GET['completed'])) $HTMLOUT.= tr('<b>' . $lang['userdetails_completedt'] . '</b><br />', '[ <a href=\'./userdetails.php?id=' . $id . '&amp;completed=1#completed\' class=\'sublink\'>' .$lang['userdetails_c_show'] . '</a> ]&nbsp;&nbsp;-&nbsp;' . $r->num_rows , 1);
        elseif ($r->num_rows == 0) $HTMLOUT.= tr('<b>' . $lang['userdetails_completedt'] . '</b><br />', '[ <a href=\'./userdetails.php?id=' . $id . '&amp;completed=1\' class=\'sublink\'>' .$lang['userdetails_c_show'] . '</a> ]&nbsp;&nbsp;-&nbsp;' . $r->num_rows , 1);
        else $HTMLOUT.= tr('<a name=\'completed\'><b>' . $lang['userdetails_completedt'] . '</b></a><br />[ <a href=\'./userdetails.php?id=' . $id . '#history\' class=\'sublink\'>' .$lang['userdetails_c_hide'] . '</a> ]', $completed, 1);
    }
}
//==End hnr
// End Class
// End File
