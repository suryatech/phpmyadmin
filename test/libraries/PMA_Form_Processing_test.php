<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * tests for methods under Formset processing library
 *
 * @package PhpMyAdmin-test
 */

/*
 * Include to test
 */
require_once 'setup/lib/form_processing.lib.php';
require_once 'libraries/config/ConfigFile.class.php';
require_once 'libraries/core.lib.php';
require_once 'libraries/Util.class.php';

/**
 * tests for methods under Formset processing library
 *
 * @package PhpMyAdmin-test
 */
class PMA_From_Processing_Test extends PHPUnit_Framework_TestCase
{

    /**
     * Test for process_formset()
     * 
     * @return void
     */
    public function testProcessFormSet()
    {

        // case 1
        $formDisplay = $this->getMockBuilder('FormDisplay')
            ->disableOriginalConstructor()
            ->setMethods(array('process', 'display'))
            ->getMock();

        $formDisplay->expects($this->once())
            ->method('process')
            ->with(false)
            ->will($this->returnValue(false));

        $formDisplay->expects($this->once())
            ->method('display')
            ->with(true, true);

        process_formset($formDisplay);

        // case 2
        $formDisplay = $this->getMockBuilder('FormDisplay')
            ->disableOriginalConstructor()
            ->setMethods(array('process', 'hasErrors', 'displayErrors'))
            ->getMock();

        $formDisplay->expects($this->once())
            ->method('process')
            ->with(false)
            ->will($this->returnValue(true));

        $formDisplay->expects($this->once())
            ->method('hasErrors')
            ->with()
            ->will($this->returnValue(true));

        ob_start();
        process_formset($formDisplay);
        $result = ob_get_clean();

        $this->assertContains(
            '<div class="error">',
            $result
        );

        $this->assertContains(
            '<a href="?page=&amp;mode=revert">',
            $result
        );

        $this->assertContains(
            '<a class="btn" href="index.php">',
            $result
        );

        $this->assertContains(
            '<a class="btn" href="?page=&amp;mode=edit">',
            $result
        );

        // case 3
        $formDisplay = $this->getMockBuilder('FormDisplay')
            ->disableOriginalConstructor()
            ->setMethods(array('process', 'hasErrors'))
            ->getMock();

        $formDisplay->expects($this->once())
            ->method('process')
            ->with(false)
            ->will($this->returnValue(true));

        $formDisplay->expects($this->once())
            ->method('hasErrors')
            ->with()
            ->will($this->returnValue(false));

        process_formset($formDisplay);

        $this->assertEquals(
            'HTTP/1.1 303 See OtherLocation: index.php',
            $GLOBALS['header']
        );

    }
    

}
?>