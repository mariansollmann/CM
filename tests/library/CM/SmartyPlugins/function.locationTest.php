<?php

require_once CM_Util::getModulePath('CM') . 'library/CM/SmartyPlugins/function.location.php';

class smarty_function_locationTest extends CMTest_TestCase {

    /** @var CM_Model_Location */
    protected $_location;

    /** @var CM_Frontend_Render */
    protected $_render;

    /** @var Smarty_Internal_Template */
    protected $_template;

    public function setUp() {
        $smarty = new Smarty();
        $this->_render = new CM_Frontend_Render();
        $this->_template = $smarty->createTemplate('string:');
        $this->_template->assignGlobal('render', $this->_render);
        $this->_location = CMTest_TH::createLocation();
    }

    public function testCaching() {
        $debug = CM_Debug::getInstance();
        $debug->setEnabled(true);

        $urlFlag = $this->_render->getUrlResource('layout', 'img/flags/fo.png');
        $expected = '<span class="function-location">cityFoo, stateFoo, countryFoo<img class="flag" src="' . $urlFlag . '" /></span>';
        $this->_assertSame($expected, array('location' => $this->_location));

        $debug->setEnabled(false);
        $this->assertEmpty($debug->getStats());
    }

    public function testPartFilter() {
        $partNamer = function (CM_Model_Location $location) {
            return '(' . $location->getName() . ')';
        };
        $expected = '<span class="function-location">(cityFoo), (stateFoo), (countryFoo)</span>';
        $this->_assertSame($expected, array('location' => $this->_location, 'partNamer' => $partNamer));
    }

    /**
     * @param string $expected
     * @param array  $params
     */
    private function _assertSame($expected, array $params) {
        $this->assertSame($expected, smarty_function_location($params, $this->_template));
    }
}
