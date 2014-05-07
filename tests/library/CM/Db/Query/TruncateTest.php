<?php

class CM_Db_Query_TruncateTest extends CMTest_TestCase {

    /** @var CM_Db_Client */
    private static $_client;

    public static function setUpBeforeClass() {
        $config = CM_Db_Db::getConfigDefault();
        self::$_client = new CM_Db_Client($config['host'], $config['port'], $config['username'], $config['password'], $config['db']);
    }

    public function testAll() {
        $query = new CM_Db_Query_Truncate(self::$_client, 't`est');
        $this->assertSame('TRUNCATE TABLE `t``est`', $query->getSqlTemplate());
    }
}
