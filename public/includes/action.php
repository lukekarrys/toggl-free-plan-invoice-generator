<?php

include('functions.php');

$requestClients = returnClients();

//---------------------------------------------------------------------------

// Iterate over client data
$clientID = $_REQUEST["client"];
$currentClient = null;
$clientOptions = '';

foreach($requestClients as $client) {
  $selected = '';
  
  if (!in_array($client['id'], $clients)) {
    $clients[$client['id']] = $default_clients_values;
  }
  
  $clients[$client['id']]["name"] = $client["name"];
  $clients[$client['id']]["id"] = $client["id"];
  
  if ($client['id'] == $clientID) {
    $currentClient = $clients[$client['id']];
    $selected = 'selected="selected"';
  }
  
  $clientOptions .= '<option '.$selected.' value="invoice?client='.$client['id'].'">'.$client['name'].'</option>';
}

if (!$currentClient) {
  $clientOptions = '<option value="">Choose a client</option>'.$clientOptions;
}

//---------------------------------------------------------------------------

if ($currentClient) {
  
  $requestProjects = returnProjects();
  
  // Iterate over project data
  $projectCount = 0;
  $currentProject = null;
  $firstClientProject = null;
  $projectId = $_REQUEST['project'];
  
  foreach($requestProjects as $project) {
  
    $selected = "";
    
    if ($project['client']['id'] == $currentClient['id']) {
    
      if ($projectCount == 0) {
        $firstClientProject = $project['id'];
      }
      
      if (($projectId == $project['id']) || (!$projectId && $projectCount == 0)) {
        $selected = 'selected="selected"';
        $currentProject = $project;
      }
      
      $projectOptions .= '<option '.$selected.' value="invoice?client='.$project['client']['id'].'&project='.$project['id'].'">'.$project['name'].'</option>';    
      $projectCount++;
      
    }
    
  }
  
  // Use first project if we have no project
  if (!$currentProject) {
    $currentProject = $firstClientProject;
  }

}

if (!$currentProject) {
  $projectOptions = '<option value="">No available projects</option>';
}

//---------------------------------------------------------------------------

if ($currentClient && $currentProject) {
  
  $requestTasks = returnTasks();
  
  // Iterate over tasks
  $totalTime = 0;
  $totalAmount = 0;
  $rowCount = 0;
  $firstDate = "";
  $lastDate = "";
  
  usort($requestTasks, "sortTasksByStartDate");
  
  foreach($requestTasks as $task) {
  
    if ($task['project']['id'] == $currentProject['id']) {
      
      if ($rownum === 0) { $firstDate = $date; }
      
      $taskDate = date("n/j/y", strtotime($task['start']));
      $taskTime = roundDuration($task['duration'], $currentClient['dontRound']);
      $taskAmount = getBillable($taskTime, $task['start'], $currentClient['preHourly'], $currentClient['postHourly'], $currentClient['hourlyChange']);
      $rowClass = ($rowCount % 2) ? 'even' : 'odd';
      
      $reportRows .= '<tr class="'.$rowClass.'">';
      $reportRows .= '<td class="desc">'.$task['description'].'</td>';
      $reportRows .= '<td class="date center">'.$taskDate.'</td>';
      $reportRows .= '<td class="dur center">'.number_format($taskTime, 2, '.', '').'</td>';
      $reportRows .= '<td class="money center">'.money_format('%.2n',floatval(round($taskAmount, 2))).'</td>';
      $reportRows .= '</tr>';
      
      $lastDate = $taskDate;
      $totalAmount += $taskAmount;
      $totalTime += $taskTime;
      $rowCount++;
      
    }
    
  }

}

//---------------------------------------------------------------------------

if ($firstDate && $lastDate && $currentClient) {
  // Get hourly rate based on date of first and last tasks
  $changeDate = strtotime($currentClient['hourlyChange']." 00:00:00");
  $firstDate = strtotime($firstDate." 00:00:00");
  $lastDate = strtotime($lastDate." 00:00:00");
  $hourlyRate = '';
  
  if ($changeDate > $lastDate) {
    $hourlyRate = money_format('%.2n', $currentClient['preHourly']);
  } elseif ($changeDate >= $firstDate && $changeDate <= $lastDate) {
    $hourlyRate = 'Starting '.$currentClient['hourlyChange'].': '.money_format('%.2n', $currentClient['postHourly']).' -- Prior To '.$currentClient['hourlyChange'].': '.money_format('%.2n', $currentClient['preHourly']);
  } else {
    $hourlyRate = money_format('%.2n', $currentClient['postHourly']);
  }
}

//---------------------------------------------------------------------------

if ($currentClient && $currentProject && ALLOW_EXTRAS && DB_CONN && DB_USER && DB_PASS) {

  // Iterate over extras
  $totalExtras = null;
    
  try {
    $database = new PDO(DB_CONN, DB_USER, DB_PASS);
    $query = "SELECT * FROM extra_items WHERE CLIENT=".$currentClient['id']." AND PROJECT=".$currentProject['id']." ORDER BY AUTONUMBER ASC";
    
    foreach ($database->query($query) as $extra) {
      $rowClass = ($rowCount % 2) ? 'even' : 'odd';
      
      $reportRows .= '<tr class="'.$rowClass.'">';
      $reportRows .= '<td class="desc">'.$extra['DESCRIPTION'].'</td>';
      $reportRows .= '<td class="date center">NA</td>';
      $reportRows .= '<td class="dur center">NA</td>';
      $reportRows .= '<td class="money center">'.money_format('%.2n',round($extra['TOTAL'], 2)).'</td>';
      $reportRows .= '</tr>';
      
      $totalAmount += $extra['TOTAL'];
      $totalExtras += $extra['TOTAL'];
      $rowCount++;
    }
    
    $database = null;
  } catch (PDOException $e) {
    print "<p>Error!: " . $e->getMessage() . "</p>";
    die();
  }
  
}


$reportRows .= '<tr class="totals">';
$reportRows .= '<td class="desc">&nbsp;</td>';
$reportRows .= '<td class="date center">Totals:</td>';
$reportRows .= '<td class="dur center">'.number_format($totalTime, 2, '.', '').'</td>';
$reportRows .= '<td class="money center">'.money_format('%.2n', round($totalAmount, 2)).'</td>';
$reportRows .= '</tr>';

$totalExtras = ($totalExtras) ? money_format('%.2n', $totalExtras) : null;
$totalAmount = ($totalAmount) ? money_format('%.2n', round($totalAmount, 2)) : null;
  


//---------------------------------------------------------------------------

include_once('template.php');
