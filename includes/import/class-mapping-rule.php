<?php

if (! defined('ABSPATH')) {
    exit;
}

class FCManager_Import_Mapping_Rule implements JsonSerializable
{
    protected string $from;
    protected string $to;
    protected string $bool_equals;

    public function __construct(string $from, string $to, string $bool_equals)
    {
        $this->from = $from;
        $this->to = $to;
        $this->bool_equals = $bool_equals;
    }

    public static function jsonDeserialize(array $data)
    {
        return new self(
            $data['from'] ?? '',
            $data['to'] ?? '',
            $data['bool_equals'] ?? ''
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'bool_equals' => $this->bool_equals,
        ];
    }

    public function from()
    {
        return $this->from;
    }

    public function to()
    {
        return $this->to;
    }

    public function bool_equals()
    {
        return $this->bool_equals;
    }
}
