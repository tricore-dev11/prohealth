<?php
  require('includes/application_top.php');
  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  if (zen_not_null($action)) {
    switch ($action) {
      case 'save':
        $id = (int)$_POST['id'];
        $sql_data_array = array(
          'link_url' => $_POST['link_url'],
          'link_alias' => $_POST['link_alias']
        );
        zen_db_perform(TABLE_LINKS_ALIASES, $sql_data_array, 'update', 'id=' . $id);
        $messageStack->add_session('Updated', 'success');
        zen_redirect(zen_href_link('ssu_link_alias', zen_get_all_get_params(array('action'))));
        break;
      case 'deleteconfirm':
        $db->Execute('DELETE FROM ' . TABLE_LINKS_ALIASES . ' WHERE id = ' . (int)$_POST['id']);
        $messageStack->add_session('Deleted', 'success');
        zen_redirect(zen_href_link('ssu_link_alias'));
        break;
    }
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
  }
  // -->
</script>
</head>
<body onLoad="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">SSU ALIAS MANAGER</td>
            <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
            	<table width="100%" cellpadding="10" cellspacing="0" border="1" style="border:1px solid #ccc;  border-collapse: collapse;">
  							<tr>
            			<td>ID</td>
            			<td>URL</td>
            			<td>Alias</td>
            			<td>Status</td>
            			<td>Permanent</td>
            			<td>&nbsp;</td>
            		</tr>
            		<?php
                $ssu_alias_sql = 'SELECT * FROM ' . TABLE_LINKS_ALIASES . ' ORDER BY id DESC';
                $ssu_alias_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $ssu_alias_sql, $ssu_alias_query_numrows);
                $ssu_alias = $db->Execute($ssu_alias_sql); 
                while(!$ssu_alias->EOF) {
                  $status='Disabled';
              		$permanent='No';
              		if($ssu_alias->fields['status']) $status='Enabled';
              		if($ssu_alias->fields['permanent_link']) $permanent='Yes';
                  if ((!isset($_GET['id']) || (isset($_GET['id']) && ($_GET['id'] == $ssu_alias->fields['id']))) && !isset($ssuInfo) && (substr($action, 0, 3) != 'new')) {
                    $ssuInfo = new objectInfo($ssu_alias->fields);
                  }
              
                  if (isset($ssuInfo) && is_object($ssuInfo) && ($ssu_alias->fields['id'] == $ssuInfo->id) ) {
                    echo '<tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link('ssu_link_alias', 'page=' . $_GET['page'] . '&id=' . $ssuInfo->id . '&action=edit') . '\'">' . "\n";
                  } else {
                    echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link('ssu_link_alias', 'page=' . $_GET['page'] . '&id=' . $ssu_alias->fields['id']) . '\'">' . "\n";
                  }
                  ?>
                  	<td><?php echo $ssu_alias->fields['id'];?></td>
                  	<td><?php echo $ssu_alias->fields['link_url'];?></td>
                  	<td><?php echo $ssu_alias->fields['link_alias'];?></td>
                  	<td><?php echo $status;?></td>
                  	<td><?php echo $permanent;?></td>
                  	<td><a href="<?php echo zen_href_link('ssu_link_alias', 'action=edit&id=' . $ssu_alias->fields['id'])?>">Edit</a> | <a href="<?php echo zen_href_link('ssu_link_alias', 'action=delete&id=' . $ssu_alias->fields['id'])?>">Delete</a></td>
                  </tr>
                  <?php 
                  $ssu_alias->MoveNext();
                }
                ?>
                <tr>
                	<td colspan="5">
                		<div align="left"><?php echo $ssu_alias_split->display_count($ssu_alias_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_REPORTS, $_GET['page'], 'Displaying %s to %s (of %s url)'); ?></div>
                		<div align="right"><?php echo $ssu_alias_split->display_links($ssu_alias_query_numrows, MAX_DISPLAY_SEARCH_RESULTS_REPORTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?>
                	</td>
                </tr>
  						</table>
            </td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_CURRENCY . '</b>');

      $contents = array('form' => zen_draw_form('currencies', 'ssu_link_alias', 'page=' . $_GET['page'] . '&id=' . $ssuInfo->id . '&action=save'));
      $contents[] = array('text' => 'Edit the SSU alias');
      $contents[] = array('text' => '<br>' . 'ID: ' . '<br>' . zen_draw_input_field('id', $ssuInfo->id));
      $contents[] = array('text' => '<br>' . 'URL: ' . '<br>' . zen_draw_input_field('link_url', $ssuInfo->link_url, 'size="100"'));
      $contents[] = array('text' => '<br>' . 'Alias: ' . '<br>' . zen_draw_input_field('link_alias', $ssuInfo->link_alias, 'size="100"'));
      $contents[] = array('text' => zen_draw_hidden_field('id', $ssuInfo->id));
      $contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_update.gif', IMAGE_UPDATE) . ' <a href="' . zen_href_link('ssu_link_alias', 'page=' . $_GET['page'] . '&id=' . $ssuInfo->id) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CURRENCY . '</b>');
      $contents = array('form'=>zen_draw_form('delete', 'ssu_link_alias', 'page=' . $_GET['page'] . '&id=' . $ssuInfo->id . '&action=deleteconfirm', 'post'));
      $contents[] = array('text' => 'Delete SSU Link');
      $contents[] = array('text' => '<br>' . 'ID: ' . '<br>' . zen_draw_input_field('id', $ssuInfo->id));
      $contents[] = array('text'=> zen_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . zen_href_link('ssu_link_alias', 'page=' . $_GET['page'] . '&id=' . $_GET['id']) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>', 'align'=>'center');
//      $contents[] = array('align' => 'center', 'text' => '<br>' . (($remove_currency) ? '<a href="' . zen_href_link('ssu_link_alias', 'page=' . $_GET['page'] . '&id=' . $ssuInfo->id . '&action=deleteconfirm') . '">' . zen_image_button('button_delete.gif', IMAGE_DELETE) . '</a>' : '') . ' <a href="' . zen_href_link('ssu_link_alias', 'page=' . $_GET['page'] . '&id=' . $ssuInfo->id) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (is_object($ssuInfo)) {
        $heading[] = array('align' => 'center', 'text' => '<b>' . strtoupper($ssuInfo->alias_type) . '</b>');
        $contents[] = array('text' => '<br>' . 'ID:' . ' ' . $ssuInfo->id);
        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link('ssu_link_alias', 'page=' . $_GET['page'] . '&id=' . $ssuInfo->id . '&action=edit') . '">' . zen_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . zen_href_link('ssu_link_alias', 'page=' . $_GET['page'] . '&id=' . $ssuInfo->id . '&action=delete') . '">' . zen_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
        
      }
      break;
  }

  if ( (zen_not_null($heading)) && (zen_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
