<?php

namespace CraigPaul\Moneris\Xml;

use Adbar\Dot;
use SimpleXMLElement;

class Reader
{
    public function __construct(public readonly SimpleXMLElement $xml)
    {
    }

    public function toArray(): array
    {
        return $this->convert($this->xml);
    }

    public function getPath(string $path): mixed
    {
        return (new Dot($this->toArray()))->get($path);
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
