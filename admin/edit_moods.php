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
// edit_moods.php for PTF by pdq 2011
if (!defined('IN_TRINITY20_ADMIN')) {
    require_once(ROOT_DIR.'404.html');
}
require_once(CLASS_DIR.'class_check.php');
$class = get_access(basename($_SERVER['REQUEST_URI']));
class_check($class);
$lang = array_merge($lang, load_language('ad_edit_moods'));
$HTMLOUT = '';
if (isset($_POST) || isset($_GET)) {
    $edit_params = array_merge($_GET, $_POST);
}
$edit_mood['action'] = ($edit_params['action'] ?? 0);
$edit_mood['id'] = (isset($edit_params['id']) ? (int)$edit_params['id'] : 0);
$edit_mood['name'] = ($edit_params['name'] ?? 0);
$edit_mood['image'] = ($edit_params['image'] ?? 0);
$edit_mood['bonus'] = (isset($edit_params['bonus']) ? (int)$edit_params['bonus'] : 0);
if ($edit_mood['action'] == 'added') {
    if ($edit_mood['name'] && $edit_mood['name'] && $edit_mood['name'] && ($edit_mood['name'] != ''.$lang['moods_example'].'' && $edit_mood['image'] != 'smile1.gif')) {
        sql_query('INSERT INTO moods (name, image, bonus) VALUES ('.sqlesc($edit_mood['name']).', '.sqlesc($edit_mood['image']).', '.sqlesc($edit_mood['bonus']).')') || sqlerr(__FILE__,
            __LINE__);
        $cache->delete($cache_keys['topmoods']);
        write_log('<b>'.$lang['moods_added'].'</b> '.htmlsafechars($CURUSER['username']).' - '.htmlsafechars($edit_mood['name']).'<img src="'.$TRINITY20['pic_base_url'].'smilies/'.htmlsafechars($edit_mood['image']).'" alt="" />');
    }
} elseif ($edit_mood['action'] == 'edited') {
    if ($edit_mood['name'] && $edit_mood['name'] && $edit_mood['name']) {
        sql_query('UPDATE moods SET name = '.sqlesc($edit_mood['name']).', image = '.sqlesc($edit_mood['image']).', bonus = '.sqlesc($edit_mood['bonus']).' WHERE id = '.sqlesc($edit_mood['id'])) || sqlerr(__FILE__,
            __LINE__);
        $cache->delete($cache_keys['topmoods']);
        write_log('<b>'.$lang['moods_edited'].'</b> '.htmlsafechars($CURUSER['username']).' - '.htmlsafechars($edit_mood['name']).'<img src="'.$TRINITY20['pic_base_url'].'smilies/'.htmlsafechars($edit_mood['image']).'" alt="" />');
    }
}
/*
elseif ($edit_mood['action'] == 'remove') {
   sql_query('DELETE FROM moods WHERE id = '.$edit_mood['id'].' LIMIT 1') or sqlerr(__FILE__, __LINE__);
   $cache->del_value($cache_keys['topmoods']);
   write_log2('<b>'.$lang['moods_deleted'].'</b> '.$CURUSER['username'].' - '.$edit_mood['id']);
}
*/
if ($edit_mood['action'] == 'edit' && $edit_mood['id']) {
    ($edit_mood['res'] = sql_query('SELECT * FROM moods WHERE id = '.sqlesc($edit_mood['id']))) || sqlerr(__FILE__, __LINE__);
    if ($edit_mood['res']->num_rows) {
        $edit_mood['arr'] = $edit_mood['res']->fetch_assoc();
        $HTMLOUT .= "<div class='row'><div class='col-md-12'><h1>{$lang['moods_edit']}</h1>
            <form method='post' action='staffpanel.php?tool=edit_moods&amp;action=edited'>
            <table class='table table-bordered'>
            <tr><td class='colhead'>{$lang['moods_name']}</td>
            <td><input type='text' name='name' size='40' value ='".htmlsafechars($edit_mood['arr']['name'])."' /></td></tr>
            <tr><td class='colhead'>{$lang['moods_image']}</td>
            <td><input type='text' name='image' size='40' value ='".htmlsafechars($edit_mood['arr']['image'])."' /></td></tr>
            <tr><td class='colhead'>{$lang['moods_bonus']}</td>
            <td><input type='checkbox' name='bonus'".($edit_mood['arr']['bonus'] ? ' checked="checked"' : '')." /></td></tr>
            <tr><td colspan='2' align='center'>
            <input type='hidden' name='id' value='".(int)$edit_mood['id']."' />
            <input type='submit' name='okay' value='{$lang['moods_add']}' class='btn' />
            </td></tr>
            </table></form></div></div>";
    }
} else {
    $HTMLOUT .= "<div class='row'><div class='col-md-12'><h1>{$lang['moods_add_new']}</h1>
         <form method='post' action='staffpanel.php?tool=edit_moods&amp;action=added'>
         <table class='table table-bordered'>
         <tr><td class='colhead'>{$lang['moods_name']}</td>
         <td><input type='text' name='name' size='40' value ='is example mood' /></td></tr>
         <tr><td class='colhead'>{$lang['moods_image']}</td>
         <td><input type='text' name='image' size='40' value ='smiley1.gif' /></td></tr>
         <tr><td class='colhead'>{$lang['moods_bonus']}</td>
         <td><input type='checkbox' name='bonus' /></td></tr>
         <tr><td colspan='2' align='center'>
         <input type='submit' name='okay' value='{$lang['moods_add']}' class='btn' />
         </td></tr>
         </table></form></div></div>";
}
$HTMLOUT .= "<div class='row'><div class='col-md-12'>";
$HTMLOUT .= '<h1>'.$lang['moods_current'].'</h1>';
$HTMLOUT .= "<table class='table table-bordered'>
      <tr><td class='colhead'>{$lang['moods_added1']}</td>
      <td class='colhead' align='left'>{$lang['moods_name']}</td>
      <td class='colhead' align='left'>{$lang['moods_image']}</td>
      <td class='colhead' align='left'>{$lang['moods_bonus']}</td>
      <td class='colhead'>{$lang['moods_edit1']}</td>".
//<td class='colhead'>{$lang['moods_remove']}</td>
    "</tr>";
($res = sql_query('SELECT * FROM moods ORDER BY id ASC')) || sqlerr(__FILE__, __LINE__);
if ($res->num_rows) {
    $color = true;
    while ($arr = $res->fetch_assoc()) {
        $HTMLOUT .= '<tr '.(($color = !$color) ? ' style="background-color:#000000;"' : 'style="background-color:#0f0f0f;"').'>
      <td><img src="'.$TRINITY20['pic_base_url'].'smilies/'.htmlsafechars($arr['image']).'" alt="" /></td>
      <td align="left">'.htmlsafechars($arr['name']).'</td>
      <td align="left">'.htmlsafechars($arr['image']).'</td>
      <td align="left">'.($arr['bonus'] != 0 ? ''.$lang['moods_yes'].'' : ''.$lang['moods_no'].'').'</td>
      <td><a style="color:#FF0000" href="staffpanel.php?tool=edit_moods&amp;id='.(int)$arr['id'].'&amp;action=edit">'.$lang['moods_edit1'].'</a></td></tr>'.
            //<td><a style="color:#FF0000" href="staffpanel.php?tool=edit_moods&amp;action=remove$amp;id='.$arr['id'].'&amp;hash='.$form_hash.'>'.$lang['moods_remove'].'</a></td></tr>
            '';
    }
    $HTMLOUT .= '</table>';
    $HTMLOUT .= "</div></div>";
}
echo stdhead($lang['moods_stdhead']).$HTMLOUT.stdfoot();
?>
