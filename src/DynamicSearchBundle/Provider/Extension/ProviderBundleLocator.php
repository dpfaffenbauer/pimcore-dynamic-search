<?php

namespace DynamicSearchBundle\Provider\Extension;

use Pimcore\Composer;
use Pimcore\Tool\ClassUtils;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ProviderBundleLocator implements ProviderBundleLocatorInterface
{
    protected Composer\PackageInfo $composerPackageInfo;

    public function __construct(Composer\PackageInfo $composerPackageInfo)
    {
        $this->composerPackageInfo = $composerPackageInfo;
    }

    public function findProviderBundles(): array
    {
        $result = $this->findComposerBundles();
        sort($result);

        return [
            'dynamic_search_provider_bundles' => $result
        ];
    }

    /**
     * Finds composer bundles in /vendor
     * if composer package type is "dynamic-search-provider-bundle".
     */
    protected function findComposerBundles(): array
    {
        $pimcoreBundles = $this->composerPackageInfo->getInstalledPackages('dynamic-search-provider-bundle');

        $composerPaths = [];
        foreach ($pimcoreBundles as $packageInfo) {
            $composerPaths[] = PIMCORE_COMPOSER_PATH . '/' . $packageInfo['name'];
        }

        return $this->findBundlesInPaths($composerPaths);
    }

    protected function findBundlesInPaths(array $paths): array
    {
        if (empty($paths)) {
            return [];
        }

        $filteredPaths = [];
        foreach ($paths as $path) {
            if (file_exists($path) && is_dir($path)) {
                $filteredPaths[] = $path;
            }
        }

        $result = [];

        $finder = new Finder();
        $finder
            ->in(array_unique($filteredPaths))
            ->name('*Bundle.php');

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $className = ClassUtils::findClassName($file);
            if ($className) {
                $this->processBundleClass($className, $result);
            }
        }

        return $result;
    }

    protected function processBundleClass($bundle, array &$result): void
    {
        if (empty($bundle) || !is_string($bundle)) {
            return;
        }

        if (!class_exists($bundle)) {
            return;
        }

        try {
            $reflector = new \ReflectionClass($bundle);
        } catch (\ReflectionException $e) {
            return;
        }

        if (!$reflector->isInstantiable() || !$reflector->implementsInterface(ProviderBundleInterface::class)) {
            return;
        }

        $result[$reflector->getName()] = $reflector->getName();
    }
}
