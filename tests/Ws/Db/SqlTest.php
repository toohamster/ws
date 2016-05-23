<?php

use Ws\Db\Sql;

class SqlTest implements ITest
{
	public function __construct() 
	{
		$dsn = [
			'type' => 'mysql',

			'dbpath'  => 'mysql:host=127.0.0.1;port=3306;dbname=ycb',
			'login'	=> 'root',
			'password' => 'root',

			'initcmd' => [
					"SET NAMES 'utf8'",
				],

			'attr'	=> [
					\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
					\PDO::ATTR_PERSISTENT => false,
				],

			'monitor'	=> 'SqlTest::sql_monitor',
		];

		$this->ds = Sql::ds($dsn);
	}

	public static function sql_monitor($sql, $dsn_id)
	{
		output($sql, 'sql');
	}

	public function run()
	{
		$result = $this->ds->all('show tables');
		output($result);

		$result = Sql::assistant( $this->ds )->select_row('ycb_test_task',array('used_vu'=>array(20,'>=')),'task_name,agent_id');
		output($result);

		$result = Sql::assistant( $this->ds )->select('ycb_test_task',array('used_vu'=>array(20,'>=')),'task_name,agent_id');
		output($result);

		$cond = "author_id=123 AND bookname='仓鼠'";
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"author_id=123 AND bookname='仓鼠'");

		// ? 为数组
		$cond = array(
			'author_id' => 123,
			'bookname' => '仓鼠',
		);
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"author_id = 123 AND bookname = '仓鼠'");

		// > < != 
		$cond = array(
			'author_id' => array(123, '>'),
			'bookname' => '仓鼠',
		);
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"author_id > 123 AND bookname = '仓鼠'");

		$cond = array(
			'author_id' => array(123, '<'),
			'bookname' => '仓鼠',
		);
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"author_id < 123 AND bookname = '仓鼠'");

		$cond = array(
			'author_id' => array(123, '!='),
			'bookname' => '仓鼠',
		);
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"author_id != 123 AND bookname = '仓鼠'");

		// 模糊查询 
		$cond = array(
			'bookname' => array('%仓鼠%','LIKE'),
		);
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"bookname LIKE '%仓鼠%'");

		// 'IN','NOT IN'
		$cond = array(
			'author_id' => array( array(123,124,125) ),
		);
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"author_id IN (123,124,125)");

		$cond = array(
			'author_id' => array( array(123,124,125), 'IN'),
		);
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"author_id IN (123,124,125)");

		$cond = array(
			'author_id' => array( array(123,124,125), 'NOT IN'),
		);
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"author_id NOT IN (123,124,125)");

		// BETWEEN AND , NOT BETWEEN AND
		$cond = array(
			'author_id' => array( array(10,25), 'BETWEEN_AND'),
		);
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"author_id  BETWEEN 10 AND 25");

		$cond = array(
			'author_id' => array( array(10,25), 'NOT_BETWEEN_AND'),
		);
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"author_id NOT BETWEEN 10 AND 25");

		// author_id > 15 OR author_id < 5 AND author_id != 32
		$cond = array(
			'author_id' => array(  
				array( array(15,'>','OR'),array(5,'<','AND'), array(32,'!=') ) ,
				'FIELD_GROUP'
			),
		);
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"  (author_id > 15 OR author_id < 5 AND author_id != 32)");

		// OR AND 连接符
		$cond = array(
			'author_id' => array(123, '!=' ,'AND'),
			'bookname' => array('仓鼠', '=' ,'OR'),
			'book_price' => array(45, '<=' ,'AND'),
		);
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"author_id != 123 AND bookname = '仓鼠' OR book_price <= 45");

		// 传入的条件的值中的特殊字符会自动进行 qstr 转义
		$cond = array(
			'bookname' => array("%仓'仓%",'LIKE'),
		);
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"bookname LIKE '%仓\'仓%'");

		// 数据表字段名比较
		$cond = array(
			'author_id' => array(123, '!=' ,'AND'),
			'book_price' => array("market_parce",'>','AND',true),
		);
		$result = Sql::assistant( $this->ds )->cond($this->ds,$cond,FALSE);
		assertEqual($result,"author_id != 123 AND book_price > market_parce");

	}

}