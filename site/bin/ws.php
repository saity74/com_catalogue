#!/usr/bin/env php
<?php

use Icicle\Http\Message\Request;
use Icicle\Http\Message\Response;
use Icicle\Log as LogNS;
use Icicle\Log\Log;
use Icicle\Socket\Socket;
use Icicle\WebSocket\Application;
use Icicle\WebSocket\Connection;
use Icicle\Http\Message\BasicResponse;
use Icicle\Http\Server\RequestHandler;
use Icicle\Loop;
use Icicle\WebSocket\Server\Server;

define('_JEXEC', 1);
define('JPATH_BASE', __DIR__);

include_once JPATH_BASE . '/defines.php';

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';


class WSApplication implements Application
{
	/**
	 * @var \Icicle\Log\Log
	 */
	private $log;
	/**
	 * @param \Icicle\Log\Log|null $log
	 */
	public function __construct(Log $log = null)
	{
		$this->log = $log ?: LogNS\log();
	}
	/**
	 * {@inheritdoc}
	 */
	public function onHandshake(Response $response, Request $request, Socket $socket)
	{
		// This method provides an opportunity to inspect the Request and Response before a connection is accepted.
		// Cookies may be set and returned on a new Response object, e.g.: return $response->withCookie(...);
		return $response; // No modification needed to the response, so the passed Response object is simply returned.
	}
	/**
	 * {@inheritdoc}
	 */
	public function onConnection(Connection $connection, Response $response, Request $request)
	{
		// The Response and Request objects used to initiate the connection are provided for informational purposes.
		// This method will primarily interact with the Connection object.
		yield $connection->send('Connected to echo WebSocket server powered by Icicle.');
		// Messages are read through an Observable that represents an asynchronous set. There are a variety of ways
		// to use this asynchronous set, including an asynchronous iterator as shown in the example below.
		$iterator = $connection->read()->getIterator();
		while (yield $iterator->isValid()) {
			/** @var \Icicle\WebSocket\Message $message */
			$message = $iterator->getCurrent();
			if ($message->getData() === 'close') {
				yield $connection->close();
			} else {
				yield $connection->send($message);
			}
		}
		/** @var \Icicle\WebSocket\Close $close */
		$close = $iterator->getReturn(); // Only needs to be called if the close reason is needed.
		yield $this->log->log(
			Log::INFO,
			'WebSocket connection from %s:%d closed; Code %d; Data: %s',
			$connection->getRemoteAddress(),
			$connection->getRemotePort(),
			$close->getCode(),
			$close->getData()
		);
	}
}


class ExampleRequestHandler implements RequestHandler
{
	private $application;
	public function __construct()
	{
		$this->application = new WSApplication();
	}
	public function onRequest(Request $request, Socket $socket)
	{
		return $this->application;
	}
	public function onError($code, Socket $socket)
	{
		return new BasicResponse($code);
	}
}
$server = new Server(new ExampleRequestHandler());
$server->listen(8887);

Loop\run();