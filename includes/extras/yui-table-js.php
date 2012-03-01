<?php if ($currentClient && $currentProject && ALLOW_EXTRAS && DB_CONN && DB_USER && DB_PASS) : ?>
<script type="text/javascript">
YAHOO.example.Data = {
     <?php
  
  try {
      $database = new PDO(DB_CONN, DB_USER, DB_PASS);
      $query = "SELECT * FROM extra_items WHERE CLIENT=".$currentClient['id']." AND PROJECT=".$currentProject['id']." ORDER BY AUTONUMBER ASC";
      $execute = $database->query($query);
      $num = $execute->rowCount();
      $counter = 1;
      echo('extras: [');          
      foreach ($execute as $row) {
          echo('{AUTONUMBER:'.$row['AUTONUMBER'].',');
          echo('CLIENT:'.$row['CLIENT'].',');
          echo('PROJECT:'.$row['PROJECT'].',');
          echo('DESCRIPTION:"'.$row['DESCRIPTION'].'",');
          echo('TOTAL:'.$row['TOTAL'].',');
          echo('DELETE:"Delete"}');
          if ($counter < $num) {echo ",";}
          $counter++;
        };        
        echo(']');
    
      $database = null;
  } catch (PDOException $e) {
      print "<p>Error!: " . $e->getMessage() . "</p>";
      die();
  }
  ?>
};

YAHOO.util.Event.addListener(window, "load", function() {
    YAHOO.example.InlineCellEditing = function() {

        var myColumnDefs = [
          {key:"AUTONUMBER"},
          {key:"CLIENT"},
          {key:"PROJECT"},
            {key:"DESCRIPTION", sortable:false, editor: new YAHOO.widget.TextareaCellEditor()},
            {key:"TOTAL", sortable:true, editor: new YAHOO.widget.TextboxCellEditor({validator:YAHOO.widget.DataTable.validateNumber})},
            {key:"DELETE", editor: new YAHOO.widget.RadioCellEditor({radioOptions:["Delete","Don't Delete"]})}
        ];
      
      var myDataSource1 = new YAHOO.util.DataSource(YAHOO.example.Data.extras,{
            responseType : YAHOO.util.DataSource.TYPE_JSARRAY,
            responseSchema : {
                fields: ["AUTONUMBER","CLIENT","PROJECT","DESCRIPTION","TOTAL","DELETE"]
            }
      });

        var myDataTable1 = new YAHOO.widget.DataTable("cellediting1", myColumnDefs, myDataSource1, {});

        var highlightEditableCell = function(oArgs) {
            var elCell = oArgs.target;
            if(YAHOO.util.Dom.hasClass(elCell, "yui-dt-editable")) {
                this.highlightCell(elCell);
            }
        };
        myDataTable1.subscribe("cellMouseoverEvent", highlightEditableCell);
        myDataTable1.subscribe("cellMouseoutEvent", myDataTable1.onEventUnhighlightCell);
        myDataTable1.subscribe("cellClickEvent", myDataTable1.onEventShowCellEditor);
        myDataTable1.subscribe("editorSaveEvent", function(oArgs) {
          var oAutonumber = oArgs.editor.getRecord().getData('AUTONUMBER');
          var oColumn = oArgs.editor.getColumn().key;
          var newData = oArgs.newData;
          if (oColumn == "DELETE" && newData == "Delete") {
            myDataTable1.deleteRow(myDataTable1.getRecordSet().getRecordIndex(oArgs.editor.getRecord()));
            $.post("includes/extras/process-extras.php", {AUTONUMBER: oAutonumber, REQUEST: 'delete'});
          } else {
            $.post("includes/extras/process-extras.php", {AUTONUMBER: oAutonumber, COLUMN: oColumn, NEW_DATA: newData, REQUEST: 'update'});
           }
    });
    
        var addRow = function() {
          $.getJSON(
            'includes/extras/process-extras.php',
            {REQUEST:'add',CLIENT:<?=$currentClient['id']?>,PROJECT:<?=$currentProject['id']?>}, 
            function(result) {
              myDataTable1.addRow({AUTONUMBER:result.autonumber, CLIENT:<?=$currentClient['id']?>, PROJECT:<?=$currentProject['id']?>, DESCRIPTION:"", TOTAL:0, DELETE:"Delete"}, 0);
            });
          return;
        };
        
        var btn = new YAHOO.widget.Button("add");
         btn.on("click", addRow);
        
        return {
            oDS: myDataSource1,
            oDT: myDataTable1
        };
    }();
    var height = $('#edit_extras_pane').height();
    height = height + 40;
    $('#edit_extras_pane').css('top','-'+height+'px');
});


$(document).ready(function() {
  
  $('#edit_extras').click(function() {
    $('#edit_extras_pane').animate({top:'50px'},1200,'easeOutCirc');
    $('#wrapper').fadeTo(800,0.1);
    return false;
  });
  
  $('#close').click(function() {
   var paneUp = -1 * ($('#edit_extras_pane').outerHeight() + 50);
   $('#edit_extras_pane').animate({top:paneUp+'px'},400,'easeOutCirc');
   $('#wrapper').fadeTo(800,1);
    return false;
  });
  
});

</script>


<?php

$extrasForm = '<div class="yui-skin-sam" id="edit_extras_pane">
      <span id="add" class="yui-button yui-push-button"><span class="first-child">
        <button type="button">Add an Entry</button>
    </span></span>
    <span id="close" class="yui-button yui-push-button"><span class="first-child">
        <button type="button">Close</button>
    </span></span>
    <div id="cellediting1"></div>
</div>';

endif; ?>