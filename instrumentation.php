<?php
// Code originally developed by Jonas Rogge for uclab.fh-potsdam.de/rosenthaler
// Here minimally adapted to accomodate other events
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// functions
function handleRequest($request) {
  $content = validateRequest($request);

  if ($content) {
    print
    $logDir = './log/v1';
    $logFilePath = $logDir . '/log_' . $content['id'] . '.log';

    createLogDir($logDir);

    $fh = fopen($logFilePath, 'a');
    processRequestContent($fh, $content);
    fclose($fh);
  }
}

function validateRequest($request) {
  $content = [];
  $requiredRequestKeys = ['id', 'num', 'type', 'data'];
  foreach ($requiredRequestKeys as $key) {
    if (array_key_exists($key, $request)) {
      $content[$key] = $request[$key];
    } else {
      return false;
    }
  }
  return $content;
}

function createLogDir($dir) {
  if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
  }
}

function processRequestContent($fh, $content) {
  switch ($content['type']) {

    // init
    case 10: {
      processInitReqestContent($fh, $content);
      break;
    }

    // stack
    case 20: {
      processStackReqestContent($fh, $content);
      break;
    }
  }
}

function processInitReqestContent($fh, $content) {
  $values = [
    'INIT',
    $content['id'],
    $content['data']['windowSize']['x'],
    $content['data']['windowSize']['y']
  ];
  $message = implode(', ', $values);
  fwrite($fh, $message . "\n");
}

function processStackReqestContent($fh, $content) {
  $eventTypes = [
    10 => 'MOUSE',
    20 => 'RESIZE',
    30 => 'CLICK',
    40 => 'SCROLL'
  ];

  foreach ($content['data'] as $event) {
    $values = [
      $event['timeOffset'],
      $eventTypes[$event['type']]
    ];

    if ($event['type'] === 10 || $event['type'] === 20 || $event['type'] === 40) {
      $values[] = $event['data']['x'];
      $values[] = $event['data']['y'];
    } elseif ($event['type'] === 30) {
      $values[] = $event['data']['x'];
      $values[] = $event['data']['y'];
      $values[] = $event['data']['target'];
    }

    $message = implode(', ', $values);
    fwrite($fh, $message . "\n");
  }

  $message = 'stack' . "\n";

  ob_start();
  var_dump($content);
  $message .= ob_get_clean();


}
/*


  $date = new DateTime();
  $message = $date->format('Y-m-d H:i:s') . "\n";



  $myFile = $logDir . '/test.log';


*/
  /*
  createLogDir($logDir);

  $logFileName = $logDir . '/test.log';
  $logFile = createLogFile($logFileName);

  logRequest($logFile, $_REQUEST);
  */


// script
handleRequest(json_decode(file_get_contents("php://input"),true));
