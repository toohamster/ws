<?php namespace Ws;

final class Runtime extends SClassBase
{

	/**
	 * 运行时标识
	 * @var string
	 */
	private $id;

	function __construct()
	{
		$this->id = $this->buildId();
	}

	/**
	 * 运行时标识
	 * 
	 * @return string
	 */
	public function id()
    {
        return $this->id;
    }

    private function buildId()
    {
    	$uuid = Env::fast_uuid(1);
    	$server = Env::getServerName();
    	return $server . '/' . Env::identify($uuid);
    }

}