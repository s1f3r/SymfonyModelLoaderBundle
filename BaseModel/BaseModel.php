<?php

namespace s1f3r\SymfonyModelLoaderBundle\BaseModel;


use appDevDebugProjectContainer;
use Symfony\Component\DependencyInjection\ContainerAware;

class BaseModel extends ContainerAware
{
    public function __construct($container = null)
    {
        if ($container instanceof appDevDebugProjectContainer) {
            $this->container = $container;
        } else {
            $this->container = null;
        }
    }

    /**
     * Shortcut to return the Model service.
     *
     * @throws \LogicException If DoctrineBundle is not available
     */
    public function getModel($id)
    {
        if (!$this->container->has('model_manager')) {
            throw new \LogicException('No model manager found.');
        }

        return $this->container->get('model_manager')->get($id);
    }

    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @return Registry
     *
     * @throws \LogicException If DoctrineBundle is not available
     */
    public function getDoctrine()
    {
        if (!$this->container->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->container->get('doctrine');
    }

    /**
     * Get a user from the Security Context
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see Symfony\Component\Security\Core\Authentication\Token\TokenInterface::getUser()
     */
    public function getUser()
    {
        if (!$this->container->has('security.context')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.context')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }

    /**
     * Returns true if the service id is defined.
     *
     * @param string $id The service id
     *
     * @return bool    true if the service id is defined, false otherwise
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * Gets a service by id.
     *
     * @param string $id The service id
     *
     * @return object The service
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

} 