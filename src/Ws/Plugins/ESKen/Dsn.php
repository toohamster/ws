<?php namespace Ws\Plugins\ESKen;

use Psr\Log\NullLogger;

class Dsn
{

	private static $idvar = 0;
	private $id = false;

	private $config = [
		'hosts' => [],

        'connectionPoolClass' => '\Elasticsearch\ConnectionPool\SniffingConnectionPool',
        'connectionPoolParams' => [
            'sniffingInterval' => 300
        ],
        'selectorClass' => '\Elasticsearch\ConnectionPool\Selectors\StickyRoundRobinSelector',
        'logging' => true,
        'logObject' => null,
        'traceObject' => null,
	];

	function __construct()
	{
		$this->config['logObject'] = new NullLogger();
		$this->config['traceObject'] = new SimpleTrace();

		$this->id = (Dsn::$idvar ++);
	}

	public function setHosts(array $hosts)
	{
		$this->config['hosts'] = $hosts;
		return $this;
	}

	public function setConnectionPoolParams(array $connectionPoolParams)
	{
		$this->config['connectionPoolParams'] = $connectionPoolParams;
		return $this;
	}

	public function id()
	{
		return $this->id;
	}

	public function toArray()
    {
        return $this->config;
    }

}