<?php

namespace MultiversX\SmartContracts\Typesystem;

class EndpointDefinition
{
    private const NamePlaceholder = '?';

    /**
     * @param string $name
     * @param EndpointParameterDefinition[] $input
     * @param EndpointParameterDefinition[] $output
     * @param EndpointModifiers $modifiers
     * @param string $title
     */
    public function __construct(
        public readonly string $name,
        public readonly array $input = [],
        public readonly array $output = [],
        public readonly EndpointModifiers $modifiers,
        public readonly string $title = ''
    ) {
    }

    public function isConstructor(): bool
    {
        return $this->name === 'constructor';
    }

    /**
     * @param array{
     *     name?: string,
     *     title?: string,
     *     onlyOwner?: bool,
     *     mutability: string,
     *     payableInTokens: string[],
     *     inputs?: array<mixed>,
     *     outputs?: array<mixed>
     * } $json
     */
    public static function fromJSON(array $json): self
    {
        $json['name'] = $json['name'] ?? self::NamePlaceholder;
        $json['onlyOwner'] = $json['onlyOwner'] ?? false;
        $json['title'] = $json['title'] ?? '';
        $json['payableInTokens'] = $json['payableInTokens'] ?? [];
        $json['inputs'] = $json['inputs'] ?? [];
        $json['outputs'] = $json['outputs'] ?? [];

        $input = array_map(
            fn($param) => EndpointParameterDefinition::fromJSON($param),
            $json['inputs']
        );
        $output = array_map(
            fn($param) => EndpointParameterDefinition::fromJSON($param),
            $json['outputs']
        );
        $modifiers = new EndpointModifiers(
            $json['mutability'],
            $json['payableInTokens'],
            $json['onlyOwner']
        );

        return new self($json['name'], $input, $output, $modifiers, $json['title']);
    }
}
