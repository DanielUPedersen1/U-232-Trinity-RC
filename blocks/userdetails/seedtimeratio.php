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
//=== testing concept of "share ratio"
$What_Cache = (XBT_TRACKER == true ? 'share_ratio_xbt_' : 'share_ratio_');
$What_Table = (XBT_TRACKER == true ? 'xbt_peers' : 'snatched');
$What_String = (XBT_TRACKER == true ? 'tid' : 'id');
$What_User_String = (XBT_TRACKER == true ? 'uid' : 'userid');
$What_Expire = (XBT_TRACKER == true ? $TRINITY20['expires']['share_ratio_xbt'] : $TRINITY20['expires']['share_ratio']);
if (($cache_share_ratio = $cache->get($What_Cache.$id)) === false) {
    $cache_s_r_query = sql_query("SELECT SUM(seedtime) AS seed_time_total, COUNT($What_String) AS total_number FROM $What_Table WHERE seedtime > '0' AND $What_User_String =" . sqlesc($user['id']));
    $cache_share_ratio = $cache_s_r_query->fetch_assoc();
    $cache_share_ratio['total_number'] = (int)$cache_share_ratio['total_number'];
    $cache_share_ratio['seed_time_total'] = (int)$cache_share_ratio['seed_time_total'];
    $cache->set($What_Cache.$id, $cache_share_ratio, $What_Expire);
}
//=== get times per class
switch (true) {
    //===  member

case ($user['class'] == UC_USER):
    $days = 2;
    break;
    //=== Member +

case ($user['class'] == UC_POWER_USER):
    $days = 1.5;
    break;
    //=== Member ++

case ($user['class'] == UC_VIP || $user['class'] == UC_UPLOADER || $user['class'] == UC_STAFF || $user['class'] == UC_ADMINISTRATOR || $user['class'] == UC_SYSOP):
    $days = 0.5;
    break;
}
if ($cache_share_ratio['seed_time_total'] > 0 && $cache_share_ratio['total_number'] > 0) {
    $avg_time_ratio = (($cache_share_ratio['seed_time_total'] / $cache_share_ratio['total_number']) / 86400 / $days);
    $avg_time_seeding = mkprettytime($cache_share_ratio['seed_time_total'] / $cache_share_ratio['total_number']);
    if ($user["id"] == $CURUSER["id"] || $CURUSER['class'] >= UC_STAFF) {
        $HTMLOUT.= '<tr><td class="clearalt5" align="right"><b>' . $lang['userdetails_time_ratio'] . '</b></td><td align="left" class="clearalt5">' . (($user_stats['downloaded'] > 0 || $user_stats['uploaded'] > 2147483648) ? '<font color="' . get_ratio_color(number_format($avg_time_ratio, 3)) . '">' . number_format($avg_time_ratio, 3) . '</font>     ' . ratio_image_machine(number_format($avg_time_ratio, 3)) . '     [<font color="' . get_ratio_color(number_format($avg_time_ratio, 3)) . '"> ' . $avg_time_seeding . '</font>' . $lang['userdetails_time_ratio_per'] . '' : $lang['userdetails_inf']) . '</td></tr>';
    }
}
//==end
// End Class
// End File
