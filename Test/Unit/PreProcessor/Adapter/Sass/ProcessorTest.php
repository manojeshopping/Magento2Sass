<?php

namespace TheExtensionLab\SassPreProcessor\PreProcessor\Adapter\Sass;

use Magento\Framework\View\Asset\ContentProcessorInterface;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    const ERROR_MESSAGE = 'Test exception';
    const ASSET_PATH = 'test-path';

    private $preProcessor;
    private $assetMock;
    private $assetSourceMock;
    private $loggerMock;

    protected function setUp()
    {
        $this->assetMock = $this->getMockBuilder('Magento\Framework\View\Asset\File')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assetSourceMock = $this->getMockBuilder('Magento\Framework\View\Asset\Source')
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->getMockForAbstractClass();

        $this->preProcessor = new Processor(
            $this->assetSourceMock,
            $this->loggerMock
        );
    }

    public function testClassImplementsContentProcessorInterface()
    {
        $this->assertInstanceOf(ContentProcessorInterface::class,$this->preProcessor);
    }

    public function testNoAssetContentReturnsNoContent()
    {
        $this->assetSourceMock->expects(self::once())
            ->method('getContent')
            ->with($this->assetMock)
            ->willReturn('');

        $cssOutput = $this->preProcessor->processContent($this->assetMock);
        $this->assertEquals('', $cssOutput);
    }

    public function testExceptionIsLoggedWithinGetContent()
    {
        $this->assetMock->expects(self::once())
            ->method('getPath')
            ->willReturn(self::ASSET_PATH);

        $this->assetSourceMock->expects(self::once())
            ->method('getContent')
            ->with($this->assetMock)
            ->willThrowException(new \Exception(self::ERROR_MESSAGE));

        $this->loggerMock->expects(self::once())
            ->method('critical')
            ->with(
                PHP_EOL . Processor::ERROR_MESSAGE_PREFIX . PHP_EOL . self::ASSET_PATH  . PHP_EOL . self::ERROR_MESSAGE
            );

        $this->preProcessor->processContent($this->assetMock);
    }

    public function testBasicContentStringIsReturnedTheSame()
    {
        $expected = "body{background-color:red;}";

        $this->assetSourceMock->expects(self::once())
            ->method('getContent')
            ->with($this->assetMock)
            ->willReturn('body{background-color:red;}');

        $cssOutput = $this->preProcessor->processContent($this->assetMock);
        $strippedCssOutput = $this->minifiCss($cssOutput);
        $this->assertEquals($expected, $strippedCssOutput);
    }

    public function testBasicSassIsProcessedCorrectly()
    {
        $expected = 'body{background-color:#fff;}';

        $mockContent = '$primary-color: #fff;

        body {
          background-color:$primary-color;
        }';

        $this->assetSourceMock->expects(self::once())
            ->method('getContent')
            ->with($this->assetMock)
            ->willReturn($mockContent);

        $cssOutput = $this->preProcessor->processContent($this->assetMock);
        $strippedCssOutput = $this->minifiCss($cssOutput);
        $this->assertEquals($expected, $strippedCssOutput);
    }

    private function minifiCss($css){
        $minifiedCss = str_replace(' ','',preg_replace('/\s+/S', " ", $css));
        return $minifiedCss;
    }
}