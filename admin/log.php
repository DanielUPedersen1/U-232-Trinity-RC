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
if (!defined('IN_TRINITY20_ADMIN')) {
    $HTMLOUT = '';
    $HTMLOUT.= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
		\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns='http://www.w3.org/1999/xhtml'>
		<head>
		<title>Error!</title>
		</head>
		<body>
	<div style='font-size:33px;color:white;background-color:red;text-align:center;'>Incorrect access<br />You cannot access this file directly.</div>
	</body></html>";
    echo $HTMLOUT;
    exit();
}
require_once (INCL_DIR . 'user_functions.php');
require_once (INCL_DIR . 'pager_functions.php');
require_once (CLASS_DIR . 'class_check.php');
$class = get_access(basename($_SERVER['REQUEST_URI']));
class_check($class);
$lang = array_merge($lang, load_language('ad_log'));
$txt = $where = '';
$search = isset($_POST['search']) ? strip_tags($_POST['search']) : '';
if(isset($_GET['search'])) $search = strip_tags($_GET['search']);
if (!empty($search)) $where = "WHERE txt LIKE " . sqlesc("%$search%") . "";
// delete items older than 1 month
$secs = 30 * 86400;
sql_query("DELETE FROM sitelog WHERE " . TIME_NOW . " - added > " . sqlesc($secs)) || sqlerr(__FILE__, __LINE__);
$resx = sql_query("SELECT COUNT(*) FROM sitelog $where");
$rowx = $resx->fetch_array(MYSQLI_NUM);
$count = $rowx[0];
$perpage = 15;
$pager = pager($perpage, $count, "staffpanel.php?tool=log&amp;action=log&amp;" . (empty($search) ? '' : "search=$search&amp;") . "");
$HTMLOUT = '';
($res = sql_query("SELECT added, txt FROM sitelog $where ORDER BY added DESC {$pager['limit']} ")) || sqlerr(__FILE__, __LINE__);
$HTMLOUT .="<div class='grid-x grid-margin-x'><div class='cell medium-12'>";
$HTMLOUT.= "<h1>{$lang['text_sitelog']}</h1>";
$HTMLOUT.= "<table class='striped'>
             <tr>
			 <td>{$lang['log_search']}</td>
			 </tr>
             <tr>
			 <td>
			 <form method='post' action='staffpanel.php?tool=log&amp;action=log'>
			 <input type='text' name='search' size='40' value=''>
			 <input type='submit' value='{$lang['log_search_btn']}' style='height: 20px'>
			 </form></td></tr></table>";
if ($count > $perpage) $HTMLOUT.= $pager['pagertop']."<br>";
if ($res->num_rows == 0) {
    $HTMLOUT.= "<b>{$lang['text_logempty']}</b>";
} else {
    $HTMLOUT.= "<table class='striped'>
      <tr>
        <td>{$lang['header_date']}</td>
        <td>{$lang['header_event']}</td>
      </tr>";
    while ($arr = $res->fetch_assoc()) {
        $color = '#333333';
        if (strpos($arr['txt'], (string) $lang['log_uploaded'])) $color = "#4799ad";
        if (strpos($arr['txt'], (string) $lang['log_created'])) $color = "#CC9966";
        if (strpos($arr['txt'], (string) $lang['log_section'])) $color = "#ba79d8";
        if (strpos($arr['txt'], (string) $lang['log_started'])) $color = "#00E300";
        if (strpos($arr['txt'], (string) $lang['log_finished'])) $color = "#00E300";
        if (strpos($arr['txt'], (string) $lang['log_sticky'])) $color = "#BBaF9B";
        if (strpos($arr['txt'], (string) $lang['log_invited_by'])) $color = "#CC9966";
        if (strpos($arr['txt'], (string) $lang['log_invited_to'])) $color = "#CC9966";
        if (strpos($arr['txt'], (string) $lang['log_deleted_by'])) $color = "#CC6666";
        if (strpos($arr['txt'], (string) $lang['log_deleted_system'])) $color = "#FF6600";
        if (strpos($arr['txt'], (string) $lang['log_sent'])) $color = "#af0b0b";
        if (strpos($arr['txt'], (string) $lang['log_reason'])) $color = "#d34e29";
        if (strpos($arr['txt'], (string) $lang['log_user'])) $color = "#d34e29";
        if (strpos($arr['txt'], (string) $lang['log_promoted'])) $color = "#3ae2f1";
        if (strpos($arr['txt'], (string) $lang['log_demoted'])) $color = "#375d60";
        if (strpos($arr['txt'], (string) $lang['log_updated'])) $color = "#6699FF";
        if (strpos($arr['txt'], (string) $lang['log_edited'])) $color = "#BBaF9B";
        $date = explode(',', get_date($arr['added'], 'LONG'));
        $HTMLOUT.= "<tr>
			<td>
				<font color='black'>{$date[0]}{$date[1]}</font>
			</td>
			<td>
				<font color='black'>" . $arr['txt'] . "</font>
			</td></tr>";
    }
    $HTMLOUT.= "</table>";
}
$HTMLOUT.= "<p>{$lang['text_times']}</p>";
if ($count > $perpage) $HTMLOUT.= $pager['pagerbottom']."<br>";
$HTMLOUT.= "</div></div>";
echo stdhead("{$lang['stdhead_log']}") . $HTMLOUT . stdfoot();
?>
