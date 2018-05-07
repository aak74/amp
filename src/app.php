<?php

require __DIR__ . '/../vendor/autoload.php';
use Amp\Loop;

function asyncMultiply($x, $y)
{
    // Create a new promisor
  $deferred = new Amp\Deferred;

    // Resolve the async result one second from now
  Loop::delay($msDelay = 1000, function () use ($deferred, $x, $y) {
    $deferred->resolve($x * $y);
  });

  return $deferred->promise();
}

$promise = asyncMultiply(6, 7);
echo 'some message';
$result = Amp\Promise\wait($promise);
var_dump($result); // int(42)
die;
// use Amp\Loop;

function tick()
{
  echo "tick\n";
}

function stop()
{
  echo "stop\n";
  // Loop::stop();
}

echo "-- before Loop::run()\n";

$increment = 0;
Loop::run(function () {
  Loop::repeat($msDelay = 50, function ($watcherId) use (&$increment) {
    echo "tick\n";
    if (++$increment >= 3) {
      Loop::cancel($watcherId); // <-- cancel myself!
    }
  });
});

echo "-- after Loop::run()\n";
die;
/*

use Amp\Artax\Response;
use Amp\Loop;


Loop::run(function () {
  $uris = [
    "https://portal.arealidea.ru/",
    "https://stackoverflow.com/",
    "https://google.com/",
    "https://github.com/",
  ];

  $client = new Amp\Artax\DefaultClient;
  // $client->setOption(Amp\Artax\Client::OP_DISCARD_BODY, true);

  $requestHandler = function (string $uri) use ($client) {
    $response = yield $client->request($uri);
    return $response->getBody();
  };

  try {
    foreach ($uris as $uri) {
      $promises[$uri] = Amp\call($requestHandler, $uri);
    }

    $responses = yield $promises;

    foreach ($responses as $uri => $response) {
      // print $uri . " - " . $response->getStatus() . $response->getReason() . PHP_EOL;
      print $uri . PHP_EOL;
      // print_r($response->getHeaders());
      print strlen($response) . PHP_EOL;
    }
  } catch (Amp\Artax\HttpException $error) {
        // If something goes wrong Amp will throw the exception where the promise was yielded.
        // The Client::request() method itself will never throw directly, but returns a promise.
    print $error->getMessage() . PHP_EOL;
  }
});
*/