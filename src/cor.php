<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Hhxsv5\Coroutine\Scheduler;

$start = microtime(true);
/**
 * @return Generator
 */
function task($i, $url)
{
  echo "task {$i}:start ", microtime(true), PHP_EOL;
  $sleep = rand(1, 5);
  // usleep($sleep*1000);
  time_sleep_until(microtime(1) + $sleep);
  $ret = yield $sleep;
  // $ret = yield strlen(file_get_contents($url));
  echo "task {$i}:end ", microtime(true), PHP_EOL;
  return $ret;
}

echo "start ", microtime(true), PHP_EOL;
$urls = [
  'https://arealidea1.ru',
  'https://ya.ru',
  'https://google.ru'
];

foreach ($urls as $key => $url) {
  $tasks[] = task($key, $url);
}

$scheduler = new Scheduler();
echo "scheduler created ", microtime(true), PHP_EOL;

foreach ($tasks as $task) {
  $scheduler->createTask($task);
}

echo "scheduler run ", microtime(true), PHP_EOL;
$scheduler->run();
echo "scheduler stop ", microtime(true), PHP_EOL;

foreach ($tasks as $key => $task) {
  print_r([$key, $task->getReturn()]);
}

$end = microtime(true) - $start;
echo $end, PHP_EOL;