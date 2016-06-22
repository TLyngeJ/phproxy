<?php

namespace phproxy\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use React\Http\Request as ReactRequest;
use React\Http\Response as ReactResponse;

/**
 * Starts the request and response workflow.
 */
class Bootstrap {

  protected $request;
  protected $response;

  /**
   * Constructor.
   *
   * @param \React\Http\Request $request
   * @param \React\Http\Response $response
   */
  public function __construct(ReactRequest $request, ReactResponse $response) {
    $this->request = $request;
    $this->response = $response;
  }

  /**
   * Start handling the request.
   */
  public function handle() {
    $client = new Client();
    $guzzleRequest = new GuzzleRequest('GET', 'http://httpbin.org');
    $guzzleResponse = new GuzzleResponse();
    $promise = $client->sendAsync($guzzleRequest)->then(function ($response) use (&$guzzleResponse) {
      $guzzleResponse = $response;
    });
    $promise->wait();

    $this->response->writeHead($guzzleResponse->getStatusCode(), $guzzleResponse->getHeaders());
    $this->response->write($guzzleResponse->getBody());
  }

  /**
   * Gather response and return it.
   */
  public function response() {
    $this->response->end();
  }

}
