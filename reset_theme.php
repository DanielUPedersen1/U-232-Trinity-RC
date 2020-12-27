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
 //Theme Reset
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'include' . DIRECTORY_SEPARATOR . 'bittorrent.php');
dbconn();
loggedinorreturn();
$lang = array_merge(load_language('global'));
global $cache, $INSTALLER09;
$sid = 1;
if ($sid > 0 && $sid != $CURUSER['id'])
    sql_query('UPDATE users SET stylesheet=' . sqlesc($sid) . ' WHERE id = ' . sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
    $cache->update_row('MyUser_' . $CURUSER['id'], [
        'stylesheet' => $sid
    ], $INSTALLER09['expires']['curuser']);
    $cache->update_row('user' . $CURUSER['id'], [
        'stylesheet' => $sid
    ], $INSTALLER09['expires']['user_cache']);
header("Location: {$INSTALLER09['baseurl']}/index.php");
?>
