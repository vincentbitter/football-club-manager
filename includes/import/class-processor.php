<?php

if (! defined('ABSPATH')) {
    exit;
}

class FCManager_Import_Processor
{
    protected FCManager_Import_Parser $parser;

    protected string $target_class;

    protected array $form_fields;

    /** @var FCManager_Import_Mapping_Rule[] */
    protected array $mapping;

    public function __construct(FCManager_Import_Parser $parser, string $target_class, array $mapping)
    {
        $this->parser = $parser;
        $this->target_class = $target_class;
        $this->mapping = $mapping;

        $this->form_fields = $target_class::get_form_fields();
    }

    public function run_chunk(int $offset): int
    {
        $start = microtime(true);
        $max_time = 5;
        $limit = 100;

        $processed = 0;

        while ($row = $this->parser->get_row($offset + $processed)) {

            $data = $this->map_row($row);
            $obj = new $this->target_class();
            foreach ($data as $key => $value) {
                $obj->$key($value);
            }
            $obj->save();

            $processed++;

            if ((microtime(true) - $start) > $max_time) break;
            if ($processed >= $limit) break;
        }

        return $processed;
    }

    protected function map_row(array $row)
    {
        $data = [];

        foreach ($this->mapping as $rule) {
            $to = $rule->to();
            $from = $rule->from();

            $form_field = array_find($this->form_fields, function ($field) use ($to) {
                return $field['key'] === $to;
            });

            if ($to === '' || !$form_field) continue;

            $value = $row[$from] ?? '';

            switch ($form_field['type']) {
                case 'boolean':
                    $value = ($value == $rule->bool_equals());
                    break;
                case 'date':
                    $ts = strtotime($value);
                    $value = ($ts !== false) ? new DateTime("@$ts") : null;
                    break;
                default:
                    $value = sanitize_text_field($value);
                    break;
            }

            $data[$to] = $value;
        }

        return $data;
    }
}
