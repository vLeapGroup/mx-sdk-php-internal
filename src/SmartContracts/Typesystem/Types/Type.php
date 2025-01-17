<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\Utils\Guards;
use MultiversX\SmartContracts\Typesystem\Types\TypeCardinality;

/**
 * An abstraction that represents a Type. Handles both generic and non-generic types.
 * Once instantiated as a Type, a generic type is "closed" (as opposed to "open").
 */
class Type
{
    protected const ClassName = "Type";

    private string $name;
    private array $typeParameters;
    private TypeCardinality $cardinality;
    protected mixed $metadata;

    public function __construct(
        string $name,
        array $typeParameters = [],
        ?TypeCardinality $cardinality = null,
        mixed $metadata = null
    ) {
        Guards::guardValueIsSet("name", $name);

        $this->name = $name;
        $this->typeParameters = $typeParameters;
        $this->cardinality = $cardinality ?? TypeCardinality::fixed(1);
        $this->metadata = $metadata;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClassName(): string
    {
        return static::ClassName;
    }

    public function getClassHierarchy(): array
    {
        $hierarchy = [];
        $currentClass = get_class($this);

        while ($currentClass) {
            if (method_exists($currentClass, 'belongsToTypesystem')) {
                $hierarchy[] = $currentClass::ClassName;
            }
            $currentClass = get_parent_class($currentClass);
        }

        return array_reverse($hierarchy);
    }

    public function getFullyQualifiedName(): string
    {
        return $this->isGenericType() || $this->hasMetadata()
            ? $this->getFullNameForGeneric()
            : "multiversx:types:{$this->getName()}";
    }

    private function getFullNameForGeneric(): string
    {
        $hasTypeParameters = count($this->getTypeParameters()) > 0;
        $joinedTypeParameters = $hasTypeParameters
            ? implode(", ", array_map(fn($type) => $type->getFullyQualifiedName(), $this->getTypeParameters()))
            : "";

        $baseName = "multiversx:types:{$this->getName()}";
        if ($hasTypeParameters) {
            $baseName = "{$baseName}<{$joinedTypeParameters}>";
        }
        if ($this->metadata !== null) {
            $baseName = "{$baseName}*{$this->metadata}*";
        }
        return $baseName;
    }

    public function hasExactClass(string $className): bool
    {
        return $this->getClassName() === $className;
    }

    public function hasClassOrSuperclass(string $className): bool
    {
        return in_array($className, $this->getClassHierarchy());
    }

    public function getTypeParameters(): array
    {
        return $this->typeParameters;
    }

    public function getMetadata(): mixed
    {
        return $this->metadata;
    }

    public function isGenericType(): bool
    {
        return count($this->typeParameters) > 0;
    }

    public function hasMetadata(): bool
    {
        return isset($this->metadata);
    }

    public function getFirstTypeParameter(): Type
    {
        Guards::guardTrue(count($this->typeParameters) > 0, "type parameters length > 0");
        return $this->typeParameters[0];
    }

    public function __toString(): string
    {
        $typeParameters = implode(", ", array_map(fn($type) => (string)$type, $this->getTypeParameters()));
        $typeParametersExpression = $typeParameters ? "<{$typeParameters}>" : "";
        return "{$this->name}{$typeParametersExpression}";
    }

    public function equals(Type $other): bool
    {
        return $this->getFullyQualifiedName() === $other->getFullyQualifiedName();
    }

    public static function equalsMany(array $a, array $b): bool
    {
        return count($a) === count($b) && array_reduce(
            array_keys($a),
            fn($carry, $i) => $carry && $a[$i]->equals($b[$i]),
            true
        );
    }

    public function differs(Type $other): bool
    {
        return !$this->equals($other);
    }

    public function valueOf(): string
    {
        return $this->name;
    }

    public function isAssignableFrom(Type $other): bool
    {
        $invariantTypeParameters = self::equalsMany($this->getTypeParameters(), $other->getTypeParameters());
        if (!$invariantTypeParameters) {
            return false;
        }

        $fullyQualifiedNameOfThis = $this->getFullyQualifiedName();
        $fullyQualifiedNamesInHierarchyOfOther = self::getFullyQualifiedNamesInHierarchy($other);
        if (in_array($fullyQualifiedNameOfThis, $fullyQualifiedNamesInHierarchyOfOther)) {
            return true;
        }

        return $other->hasClassOrSuperclass($this->getClassName());
    }

    private static function getFullyQualifiedNamesInHierarchy(Type $type): array
    {
        $hierarchy = [];
        $currentClass = get_class($type);

        while ($currentClass) {
            $reflection = new \ReflectionClass($currentClass);
            if ($reflection->hasMethod('belongsToTypesystem')) {
                $hierarchy[] = $type->getFullyQualifiedName();
            }
            $currentClass = get_parent_class($currentClass);
        }

        return $hierarchy;
    }

    public function getNamesOfDependencies(): array
    {
        $dependencies = [];

        foreach ($this->typeParameters as $type) {
            $dependencies[] = $type->getName();
            $dependencies = array_merge($dependencies, $type->getNamesOfDependencies());
        }

        return array_values(array_unique($dependencies));
    }

    public function toJSON(): array
    {
        return [
            'name' => $this->name,
            'typeParameters' => array_map(fn($item) => $item->toJSON(), $this->typeParameters),
        ];
    }

    public function getCardinality(): TypeCardinality
    {
        return $this->cardinality;
    }

    public function belongsToTypesystem(): void
    {
    }
}
