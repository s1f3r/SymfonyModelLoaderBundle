<?php

namespace s1f3r\SymfonyModelLoaderBundle\ModelManager;


use Exception;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Yaml\Yaml;

class ModelManager extends ContainerAware {
    protected  $modelContainer;
    protected  $bundles;
    protected  $serviceContainer;

    public function __construct(Container $container)
    {
        $this->serviceContainer = $container;
        $kernel = $container->get('kernel');

        $bundles = $container->getParameter('kernel.bundles');
        $bundleList = [];

        $this->modelContainer = new Container();

        foreach ($bundles as $bundleName => $bundle){
            try {
                $bundleList[$bundleName]['path'] = $kernel->locateResource('@'.$bundleName.'/Resources/config/models.yml');
            } catch (Exception $e) {}
        }

        $this->bundles = $bundleList;

        return $this;
    }

    public function get($id)
    {
        if($this->modelContainer->has($id)){
            return $this->modelContainer->get($id);
        }

        $names = explode(':', $id);
        $bundleName = $names[0];
        $modelName = $names[1];

        if(count($names) < 2){
            throw new Exception('Model name supplied is wrong. Use the "BundleName:ModelName" form');
        }

        if (!array_key_exists($bundleName, $this->bundles)){
            throw new Exception('No such bundle exists or there are no models.yml file defined: ' . $bundleName);
        }

        if(!array_key_exists('models', $this->bundles[$bundleName])){
            $models = Yaml::parse($this->bundles[$bundleName]['path']);
            $this->bundles[$bundleName]['models'] = $models;
        }

        $bundleModels = $this->bundles[$bundleName]['models'];
        if(!array_key_exists($modelName, $bundleModels)){
            throw new Exception('No such model defined in bundle: ' . $id);
        }

        if(!array_key_exists('class', $bundleModels[$modelName])){
            throw new Exception('No class defined for model: ' . $modelName);
        }

        $modelClass = $bundleModels[$modelName]['class'];
        $this->modelContainer->set($id, new $modelClass($this->serviceContainer));

        return $this->modelContainer->get($id);
    }
}