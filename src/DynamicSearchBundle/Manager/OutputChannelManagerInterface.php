<?php

namespace DynamicSearchBundle\Manager;

use DynamicSearchBundle\Context\ContextDefinitionInterface;
use DynamicSearchBundle\Exception\ProviderException;
use DynamicSearchBundle\OutputChannel\OutputChannelInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierActionInterface;
use DynamicSearchBundle\OutputChannel\Modifier\OutputChannelModifierFilterInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeOptionsBuilderInterface;
use DynamicSearchBundle\OutputChannel\RuntimeOptions\RuntimeQueryProviderInterface;

interface OutputChannelManagerInterface
{
    public function getOutputChannel(ContextDefinitionInterface $contextDefinition, string $outputChannel): ?OutputChannelInterface;

    public function getOutputChannelRuntimeQueryProvider(string $provider): ?RuntimeQueryProviderInterface;

    public function getOutputChannelRuntimeOptionsBuilder(string $provider): ?RuntimeOptionsBuilderInterface;

    /**
     * @return OutputChannelModifierActionInterface[]
     */
    public function getOutputChannelModifierAction(string $outputChannelServiceName, string $action): array;

    public function getOutputChannelModifierFilter(string $outputChannelServiceName, string $filter): ?OutputChannelModifierFilterInterface;
}
