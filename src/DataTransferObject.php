<?php


namespace StructuredHandlers;

use ReflectionClass;
use ReflectionProperty;

abstract class DataTransferObject
{

    /**
     * @var array
     */
    protected $only;

    /**
     * @var array
     */
    protected $except;

    /**
     * DataTransferObject constructor.
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->only = [];
        $this->except = [];
        $class = new ReflectionClass(static::class);
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $property = $reflectionProperty->getName();
            $this->{$property} = $parameters[$property];
        }
    }

    /**
     * @param string $property
     * @return DataTransferObject
     */
    public function only(string $property): DataTransferObject
    {
        if (sizeof($this->except)) {
            return $this;
        }

        $this->only[] = $property;
        return $this;
    }

    /**
     * @param string $property
     * @return DataTransferObject
     */
    public function except(string $property): DataTransferObject
    {
        if (sizeof($this->only)) {
            return $this;
        }

        $this->except[] = $property;
        return $this;
    }

    /**
     * @param bool $snakeCase
     * @return array
     */
    public function toArray(bool $snakeCase = true): array
    {
        $arrayResult = [];
        $class = new ReflectionClass(static::class);
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $property = $reflectionProperty->getName();

            # Omit value from user
            if (in_array($property, $this->except)) {
                continue;
            }

            # Set array key
            $arrayKey = $snakeCase ? $this->toSnake($property) : $property;

            # Set by user
            if (sizeof($this->only)) {
                if (in_array($property, $this->only)) {
                    $arrayResult[$arrayKey] = $this->{$property};
                    if ((is_subclass_of($this->{$property}, DataTransferObject::class))) {
                        $arrayResult[$arrayKey] = $this->{$property}->toArray();
                    }
                    continue;
                }
            } else {
                $arrayResult[$arrayKey] = $this->{$property};
                if ((is_subclass_of($this->{$property}, DataTransferObject::class))) {
                    $arrayResult[$arrayKey] = $this->{$property}->toArray();
                }
            }
        }
        return $arrayResult;
    }

    /**
     * @param string $input
     * @return string
     */
    private function toSnake(string $input): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }
}
