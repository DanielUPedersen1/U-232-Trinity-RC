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
function docleanup($data)
{
    global $INSTALLER09, $queries;
    set_time_limit(0);
    ignore_user_abort(1);
    /** sync torrent counts - pdq **/
    $tsql = 'SELECT t.id, t.seeders, (
    SELECT COUNT(*)
    FROM peers
    WHERE torrent = t.id AND seeder = "yes"
    ) AS seeders_num,
    t.leechers, (
    SELECT COUNT(*)
    FROM peers
    WHERE torrent = t.id
    AND seeder = "no"
    ) AS leechers_num,
    t.comments, (
    SELECT COUNT(*)
    FROM comments
    WHERE torrent = t.id
    ) AS comments_num
    FROM torrents AS t
    ORDER BY t.id ASC';
    $updatetorrents = array();
    $tq = sql_query($tsql);
    while ($t = mysqli_fetch_assoc($tq)) {
        if ($t['seeders'] != $t['seeders_num'] || $t['leechers'] != $t['leechers_num'] || $t['comments'] != $t['comments_num']) $updatetorrents[] = '(' . $t['id'] . ', ' . $t['seeders_num'] . ', ' . $t['leechers_num'] . ', ' . $t['comments_num'] . ')';
    }
    ((mysqli_free_result($tq) || (is_object($tq) && (get_class($tq) == "mysqli_result"))) ? true : false);
    if (count($updatetorrents)) sql_query('INSERT INTO torrents (id, seeders, leechers, comments) VALUES ' . implode(', ', $updatetorrents) . ' ON DUPLICATE KEY UPDATE seeders = VALUES(seeders), leechers = VALUES(leechers), comments = VALUES(comments)');
    unset($updatetorrents);
    if ($queries > 0) write_log("Torrent clean-------------------- Torrent cleanup Complete using $queries queries --------------------");
    if (false !== mysqli_affected_rows($GLOBALS["___mysqli_ston"])) {
        $data['clean_desc'] = mysqli_affected_rows($GLOBALS["___mysqli_ston"]) . " items updated";
    }
    if ($data['clean_log']) {
        cleanup_log($data);
    }
}
?>
