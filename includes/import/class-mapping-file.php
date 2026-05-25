<?php

if (! defined('ABSPATH')) {
    exit;
}

class FCManager_Import_Mapping_File implements JsonSerializable
{
    protected string $extension;

    protected string $target_class;

    /** @var FCManager_Import_Mapping_Rule[] */
    protected array $mapping;

    public function __construct(string $extension, string $target_class, array $mapping)
    {
        $this->extension = $extension;
        $this->target_class = $target_class;
        $this->mapping = $mapping;
    }

    public static function jsonDeserialize(array $data)
    {
        $mapping = array_map(function ($rule_data) {
            return FCManager_Import_Mapping_Rule::jsonDeserialize($rule_data);
        }, $data['mapping'] ?? []);

        return new self(
            $data['extension'] ?? '',
            $data['target_class'] ?? '',
            $mapping
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'extension' => $this->extension,
            'target_class' => $this->target_class,
            'mapping' => $this->mapping,
        ];
    }

    public function extension()
    {
        return $this->extension;
    }

    public function target_class()
    {
        return $this->target_class;
    }

    public function mapping()
    {
        return $this->mapping;
    }
}
