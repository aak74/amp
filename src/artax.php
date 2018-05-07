<?php
use Amp\Artax\Response;
use Amp\Loop;
use Amp\Socket\ClientTlsContext;

require __DIR__ . '/../vendor/autoload.php';

$tlsContext = new ClientTlsContext;
$tlsContext = $tlsContext->withoutPeerVerification();
// Instantiate the HTTP client
$client = new Amp\Artax\DefaultClient(null, null, $tlsContext);
$requestHandler = function (string $uri) use ($client) {
  try {
    $start = microtime(true);
    // echo 'handler1 - ', $uri, PHP_EOL;
    /** @var Response $response */
    $response = yield $client->request($uri);
    // echo 'handler2 - ', $uri, PHP_EOL;
    // echo microtime(true) - $start, PHP_EOL;
    return $response->getBody();
    return [$response->getBody(), microtime(true) - $start];
  } catch (Amp\Artax\HttpException $error) {
    echo 'error ', $uri, PHP_EOL;
  }
  return '';
  return ['', microtime(true) - $start];
};

// $uris = [
//   "https://arealidea.ru/",
//   "https://google.com/",
//   "https://github.com/",
//   "https://stackoverflow.com/",
//   "https://ya.ru/",
// ];
$uris = json_decode(file_get_contents(__DIR__ . '/sites.json'), true);
$uris = array_slice($uris, 0, 15);

Loop::run(function () use ($uris, $requestHandler) {
  $start = microtime(true);
  
  try {
    while (count($uris)) {
      $promises = [];
      $i = 0;
      while ($i <= 10 && count($uris)) {
        $uri = array_pop($uris);
        echo 'to promise - ', $uri['url'], PHP_EOL;
        $promises[$uri['url']] = Amp\call($requestHandler, $uri['url']);
        $i++;
      }
      $bodies = yield $promises;
      // var_dump($bodies);
      foreach ($bodies as $uri => $body) {
        echo $uri . " - " . \strlen($body) . " bytes", PHP_EOL;
        // echo $uri . " - " . \strlen($body[0]) . " bytes. Execution time: " . $body[1], PHP_EOL;
      }
    }
    // var_dump($promises);
  } catch (Amp\Artax\HttpException $error) {
    echo 'HttpException';
  } catch (Amp\Artax\TimeoutException $error) {
    echo 'TimeoutException';
  }
  echo 'Total time execution: ', microtime(true) - $start, PHP_EOL;
});
