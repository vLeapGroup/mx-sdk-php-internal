<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\Type;
use ReflectionClass;

abstract class TypedValue
{
    public const ClassName = "TypedValue";
    private Type $type;

    public function __construct(Type $type)
    {
        $this->type = $type;
    }

    public function getClassName(): string
    {
        return static::ClassName;
    }

    /**
     * Gets the class hierarchy, filtering only classes that belong to the typesystem
     * @return string[]
     */
    public function getClassHierarchy(): array
    {
        $hierarchy = [];
        $reflection = new ReflectionClass($this);

        do {
            if (method_exists($reflection->getName(), 'belongsToTypesystem')) {
                $hierarchy[] = $reflection->getName()::ClassName;
            }
        } while ($reflection = $reflection->getParentClass());

        return array_reverse($hierarchy);
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function equals(TypedValue $other): bool
    {
        return $this->type->equals($other->type);
    }

    abstract public function valueOf(): mixed;

    public function hasExactClass(string $className): bool
    {
        return $this->getClassName() === $className;
    }

    public function hasClassOrSuperclass(string $className): bool
    {
        return in_array($className, $this->getClassHierarchy(), true);
    }

    public function belongsToTypesystem(): void
    {
    }
}

function isTyped(mixed $value): bool
{
    return method_exists($value, 'belongsToTypesystem');
}
