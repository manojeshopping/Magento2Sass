<?php

namespace TheExtensionLab\SassPreProcessor\PreProcessor\Adapter\Sass;

use Magento\Framework\View\Asset\ContentProcessorException;
use Magento\Framework\View\Asset\File;
use Magento\Framework\View\Asset\ContentProcessorInterface;
use Magento\Framework\View\Asset\Source;
use Psr\Log\LoggerInterface;

class Processor implements ContentProcessorInterface
{
    private $logger;

    public function __construct(
        Source $assetSource,
        LoggerInterface $logger
    ) {
        $this->assetSource = $assetSource;
        $this->logger = $logger;
    }

    public function processContent(File $asset)
    {
        $path = $asset->getPath();
        try {
            $scssIn = $this->assetSource->getContent($asset);

            $compiler = new \Leafo\ScssPhp\Compiler;
            $cssOut = $compiler->compile($scssIn);

            return $cssOut;
        } catch (\Exception $e) {
            $errorMessage = PHP_EOL . self::ERROR_MESSAGE_PREFIX . PHP_EOL . $path . PHP_EOL . $e->getMessage();
            $this->logger->critical($errorMessage);
        }
    }
}