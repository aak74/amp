<?php

require_once(__DIR__ . '/../vendor/autoload.php');

echo 1, "\n";

async(function () : bool {
  sleep(1);
  echo 2, "\n";
  return true;
});

echo 3, "\n";