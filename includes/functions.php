<?php 

function roundUp($value,$precision=0)
{
    if ( $precision == 0 ) {
        $precisionFactor = 1;
    } else {
        $precisionFactor = pow(10,$precision );
    }
    return ceil($value*$precisionFactor)/$precisionFactor;
} 

function roundDuration($duration,$dont_round=false) {
  $seconds = $duration;
  
  if ($seconds >= 3600) {
    $hours = floor($seconds/3600);
    $seconds = $seconds % 3600;
  }
  if ($dont_round) {
    $minutes = ceil($seconds/60);
    $decimalTime = $hours + roundUp($minutes/60,2);
  } else {
    switch ($seconds) {
      case 0:
        $seconds = 0;
        break;
      case ($seconds <= 900):
        $seconds = 900;
        break;
      case ($seconds <= 1800):
        $seconds = 1800;
        break;
      case ($seconds <= 2700):
        $seconds = 2700;
        break;
      default:
        $seconds = 3600;
        break;
    }
    $decimalTime = $hours + ($seconds/3600);
  }
  return $decimalTime;
}

function getBillable($decimalTime,$start,$pre,$post,$change) {
  $change = strtotime(date($change." 00:00:00"));
  $start = strtotime($start);

  if ($start < $change) {
    $hourly = $pre;
  } else {
    $hourly = $post;
  }

  return $decimalTime * $hourly;
}

function checkNull($val) {
  if (is_null($val)) {
    return 'NA';
  } else {
    return $val;
  }
}

function sortTasksByStartDate($a, $b) {
  return strcmp($a["start"], $b["start"]);
}


function callToggl($endpoint) {
  
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
  curl_setopt($curl, CURLOPT_URL, TOGGL_API_URL.$endpoint);
  curl_setopt($curl, CURLOPT_USERPWD, TOGGL_API_TOKEN.':api_token');
  $curl_result = json_decode(curl_exec($curl), true);
  curl_close($curl);
  
  return $curl_result["data"];
  
}

function returnTasks() {
  $start_date = date("c",strtotime(date("Y/m/d")." -1 year"));
  $end_date = date("c",strtotime(date("Y/m/d")." +1 day"));
  
  return callToggl('tasks.json?start_date='.$start_date.'&end_date'.$end_date);
}

function returnProjects() {
  return callToggl('projects.json');
}

function returnClients() {
  return callToggl('clients.json');
}
