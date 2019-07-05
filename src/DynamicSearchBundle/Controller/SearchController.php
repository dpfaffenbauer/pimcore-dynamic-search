<?php

namespace DynamicSearchBundle\Controller;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Processor\SubProcessor\OutputChannelSubProcessorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var OutputChannelSubProcessorInterface
     */
    protected $outputChannelWorkflowProcessor;

    /**
     * @param ConfigurationInterface             $configuration
     * @param OutputChannelSubProcessorInterface $outputChannelWorkflowProcessor
     */
    public function __construct(
        ConfigurationInterface $configuration,
        OutputChannelSubProcessorInterface $outputChannelWorkflowProcessor
    ) {
        $this->configuration = $configuration;
        $this->outputChannelWorkflowProcessor = $outputChannelWorkflowProcessor;
    }

    /**
     * @param Request $request
     * @param string  $contextName
     *
     * @return JsonResponse
     */
    public function autoCompleteAction(Request $request, string $contextName)
    {
        try {
            $data = $this->outputChannelWorkflowProcessor->dispatchOutputChannelQuery($contextName, 'autocomplete', ['request' => $request]);
        } catch (\Throwable $e) {
            return $this->json(
                ['error' => sprintf('Error while loading auto complete output channel for "%s" context. Error was: %s', $contextName, $e->getMessage())],
                500,
                [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            );
        }

        return $this->json($data);

    }

    /**
     * @param Request $request
     * @param string  $contextName
     *
     * @return JsonResponse
     */
    public function suggestionsAction(Request $request, string $contextName)
    {
        try {
            $data = $this->outputChannelWorkflowProcessor->dispatchOutputChannelQuery($contextName, 'suggestions', ['request' => $request]);
        } catch (\Throwable $e) {
            return $this->json(
                ['error' => sprintf('Error while loading suggestions output channel for "%s" context. Error was: %s', $contextName, $e->getMessage())],
                500,
                [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            );
        }

        return $this->json($data);
    }
}