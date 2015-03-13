<?php

namespace Cityware\Generator;

abstract class Factory
{
    /**
     * Adapter plugin manager
     * @var AdapterPluginManager
     */
    protected static $adapters;

    /**
     * @var array Known captcha types
     */
    protected static $classMap = array(
        'module' => 'Cityware\Generator\Adapter\ModuleAdapter',
        'controller' => 'Cityware\Generator\Adapter\ControllerAdapter',
        'form' => 'Cityware\Generator\Adapter\FormAdapter',
        'datagrid' => 'Cityware\Generator\Adapter\DatagridAdapter',
        'model' => 'Cityware\Generator\Adapter\ModelAdapter',
    );

    /**
     * Create a captcha adapter instance
     *
     * @param  array|Traversable                  $options
     * @return AdapterInterface
     * @throws Exception\InvalidArgumentException for a non-array, non-Traversable $options
     * @throws Exception\DomainException          if class is missing or invalid
     */
    public static function factory($adapter = 'controller')
    {
        if (!is_string($adapter)) {
            throw new \Cityware\Exception\InvalidArgumentException(sprintf(
                            '%s expects an string or Traversable argument; received "%s"', __METHOD__, (is_object($adapter) ? get_class($adapter) : gettype($adapter))
            ));
        }

        if (isset(static::$classMap[strtolower($adapter)])) {
            $class = static::$classMap[strtolower($adapter)];
        }

        if (!class_exists($class)) {
            throw new \Cityware\Exception\DomainException(
                    sprintf('%s expects the "class" to resolve to an existing class; received "%s"', __METHOD__, $class)
            );
        }

        switch (strtolower($adapter)) {
            case 'form':
                return new Adapter\FormAdapter();
                break;
            case 'datagrid':
                return new Adapter\DatagridAdapter();
                break;
            case 'controller':
                return new Adapter\ControllerAdapter();
                break;
            case 'module':
                return new Adapter\ModuleAdapter();
                break;
            case 'model':
                return new Adapter\ModelAdapter();
                break;

            default:
                throw new \Cityware\Exception\InvalidArgumentException('Adaptador não definico');
                break;
        }
    }

}
