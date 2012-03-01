<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Date <?=date("n/j/y")?> - Invoice - <?php echo ($currentProject) ? $currentProject["client_project_name"] : "Select a client or project"?></title>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<base href="<?=BASE_URL?>" />
<link rel="stylesheet" href="styles/s_style.css" type="text/css" media="screen" />
<link rel="stylesheet" href="styles/p_style.css" type="text/css" media="print" />

<?php if (ALLOW_EXTRAS) : ?>
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.9.0/build/button/assets/skins/sam/button.css&2.9.0/build/datatable/assets/skins/sam/datatable.css">
<script type="text/javascript" src="http://yui.yahooapis.com/combo?2.9.0/build/yahoo-dom-event/yahoo-dom-event.js&2.9.0/build/element/element-min.js&2.9.0/build/button/button-min.js&2.9.0/build/datasource/datasource-min.js&2.9.0/build/datatable/datatable-min.js"></script>
<?php endif; ?>

<script type="text/javascript" src="js/jquery+plugins.js"></script>
<script type="text/javascript">
$(document).ready(function() {

  $('#select_client').change(function() {
   var goto = $(this).val();
   if (goto != "#") window.location.href = goto;
  });
  
  $('#select_project').change(function() {
    var goto = $(this).val();
    if (goto != "#") window.location.href = goto;
  });
  
});
</script>

<?php include_once('extras/yui-table-js.php'); ?>

</head>
<body class="yui-skin-sam">

  <?=$extrasForm?>

  <div id="wrapper">
    <img class="print-logo" src="images/logo-print.png" alt="" />
    <div id="header">
      
      <?php if (ALLOW_EXTRAS && $currentProject && $currentClient) : ?>
      <div class="change-form">
        <a style="font-size:12px;padding:1px 5px;" id="edit_extras" class="submit" href="#">Edit Extras</a>
      </div>
      <?php endif; ?>
      
      <form action="#" class="change-form" method="GET" id="change_client">
        <select name="select_client" id="select_client" size="1"><?=$clientOptions?></select>
      </form>
      
      <form action="#" class="change-form" method="GET" id="change_project">
        <select name="select_project" id="select_project" size="1"><?=$projectOptions?></select>
      </form>
  
      <h1>Invoice Sent: <span><?=date("F j, Y")?></span></h1>
      <?php if (DISPLAY_CONTACT) : ?><p><?=CONTACT_NAME?><br/><?=CONTACT_ADDRESS?><br/>Tel: <?=CONTACT_PHONE?><br/>Email: <?=CONTACT_EMAIL?></p><? endif; ?>
      
      <?php if ($currentClient) : ?><h2 class="less-padding">Bill To: <span><?=$currentClient["name"]?></span></h2><? endif; ?>
      <?php if ($currentProject) : ?><h2>Project: <span><?=$currentProject["name"]?></span></h2><? endif; ?>
      <?php if ($totalTime) : ?><h3>Total Time: <span><?=$totalTime?> Hours</span></h3><? endif; ?>
      <?php if ($hourlyRate) : ?><h3>Hourly Rate: <span><?=$hourlyRate?></span></h3><? endif; ?>
      <?php if ($totalExtras) : ?><h3>Extras: <span><?=$totalExtras?></span></h3><? endif; ?>
      <?php if ($totalAmount) : ?><h3>Total USD: <span><?=$totalAmount?></span></h3><? endif; ?>
  
      <h2 class="top-padding">Task List</h2>
    </div>
    
    <div id="content">
      <table cellspacing="0"><thead><tr><th>Description</th><th>Date</th><th>Duration (Hours)</th><th>Amount</th></tr></thead><tbody><?=$reportRows?></tbody></table>
    </div>
    
    <div id="footer">
      <?php if (DISPLAY_CONTACT) : ?><p class="center">If you have any questions concerning this invoice, please contact:<br/><?=CONTACT_NAME?><br/>Tel: <?=CONTACT_PHONE?><br/>Email: <?=CONTACT_EMAIL?></p><? endif; ?>
    </div>
    
  </div>
  
</body>
</html>