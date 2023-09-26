<?php

namespace CraigPaul\Moneris\Support;

use SimpleXMLElement;

class XmlReader
{
    public function __construct(public readonly SimpleXMLElement $xml)
    {
    }

    public function toArray(): array
    {
        return $this->convert($this->xml);
    }

    protected function convert(SimpleXMLElement $xml): array
    {
        $array = [];

        foreach ($xml as $key => $value) {
            if ($value->count()) {
                $array[$key] = $this->convert($value);
            } else {
                $array[$key] = $this->getValue((string) $value);
            }
        }

        return $array;
    }

    protected function valueMap(): array
    {
        return [
            'true' => true,
            'false' => false,
            'null' => null,
        ];
    }

    protected function getValue(mixed $value): mixed
    {
        if (is_string($value) && array_key_exists($value, $this->valueMap())) {
            return $this->valueMap()[$value];
        }

        return $value;
    }
}
