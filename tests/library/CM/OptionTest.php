<?php
require_once dirname(__FILE__) . '/../../TestCase.php';

class CM_OptionTest extends TestCase {
	public static function setUpBeforeClass() {
	}

	public static function tearDownAfterClass() {
		TH::clearEnv();
	}

	public function testGetSet() {
		$option = CM_Option::getInstance();

		$this->assertNull($option->get('foo'));

		$option->set('foo', 12);
		$this->assertSame($option->get('foo'), 12);

		$option->set('foo', '12');
		$this->assertSame($option->get('foo'), '12');

		$option->set('foo', array('num' => 12));
		$this->assertSame($option->get('foo'), array('num' => 12));

		$option->set('foo', null);
		$this->assertSame($option->get('foo'), null);

		$this->assertNull($option->get('bar'));

		$option->set('bar', 13);
		$this->assertSame($option->get('bar'), 13);
	}

	public function testInc() {
		$option = CM_Option::getInstance();

		$this->assertSame(1, $option->inc('zoo'));
		$this->assertSame(2, $option->inc('zoo'));
		$this->assertSame(5, $option->inc('zoo', 3));
		$this->assertSame(3, $option->inc('zoo', -2));
	}
}
