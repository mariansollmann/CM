<?php

class CM_Db_Query_AbstractTest extends CMTest_TestCase {

	public function testWhereNull() {
		$query = new CM_Db_Query_AbstractMockWhere(null);
		$this->assertSame('', $query->getSqlTemplate());
		$this->assertSame(array(), $query->getParameters());
	}

	public function testWhereString() {
		$query = new CM_Db_Query_AbstractMockWhere('hello world');
		$this->assertSame('WHERE hello world', $query->getSqlTemplate());
		$this->assertSame(array(), $query->getParameters());
	}

	public function testWhereArray() {
		$query = new CM_Db_Query_AbstractMockWhere(array('foo' => 'foo1', 'bar' => 2));
		$this->assertSame('WHERE `foo` = ? AND `bar` = ?', $query->getSqlTemplate());
		$this->assertSame(array('foo1', 2), $query->getParameters());
	}

	public function testOrderByNull() {
		$query = new CM_Db_Query_AbstractMockOrderBy(null);
		$this->assertSame('', $query->getSqlTemplate());
		$this->assertSame(array(), $query->getParameters());
	}

	public function testOrderByString() {
		$query = new CM_Db_Query_AbstractMockOrderBy('hello world');
		$this->assertSame('ORDER BY hello world', $query->getSqlTemplate());
		$this->assertSame(array(), $query->getParameters());
	}

	public function testOrderByArray() {
		$query = new CM_Db_Query_AbstractMockOrderBy(array('foo' => 'ASC', 'bar' => 'DESC'));
		$this->assertSame('ORDER BY `foo` ASC, `bar` DESC', $query->getSqlTemplate());
		$this->assertSame(array(), $query->getParameters());
	}

	public function testOrderByArrayInvalidDirection() {
		try {
			new CM_Db_Query_AbstractMockOrderBy(array('foo' => 'NONEXISTENT'));
			$this->fail();
		} catch (CM_Exception_Invalid $e) {
			$this->assertContains('Invalid order direction', $e->getMessage());
		}
	}

	public function testOrderByArrayInvalidField() {
		try {
			new CM_Db_Query_AbstractMockOrderBy(array('foo'));
			$this->fail();
		} catch (CM_Exception_Invalid $e) {
			$this->assertContains('Order field name is not string', $e->getMessage());
		}
	}
}

class CM_Db_Query_AbstractMockWhere extends CM_Db_Query_Abstract {

	public function __construct($where) {
		$this->_addWhere($where);
	}
}

class CM_Db_Query_AbstractMockOrderBy extends CM_Db_Query_Abstract {

	public function __construct($orderBy) {
		$this->_addOrderBy($orderBy);
	}
}
