<?php

namespace TheExtensionLab\SassPreProcessor\PreProcessor\Adapter\Sass;

use Magento\Framework\View\Asset\ContentProcessorInterface;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    private $preProcessor;

    public function testClassImplementsContentProcessorInterface()
    {
        $this->preProcessor = new Processor;
        $this->assertInstanceOf(ContentProcessorInterface::class,$this->preProcessor);
    }
}