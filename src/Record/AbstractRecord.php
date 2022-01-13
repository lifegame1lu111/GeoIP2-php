<?php

declare(strict_types=1);

namespace GeoIp2\Record;

abstract class AbstractRecord implements \JsonSerializable
{
    /**
     * @var array<string, mixed>
     */
    private $record;

    /**
     * @ignore
     */
    public function __construct(?array $record)
    {
        $this->record = isset($record) ? $record : [];
    }

    /**
     * @ignore
     *
     * @return mixed
     */
    public function __get(string $attr)
    {
        // XXX - kind of ugly but greatly reduces boilerplate code
        $key = $this->attributeToKey($attr);

        if ($this->__isset($attr)) {
            return $this->record[$key];
        }
        if ($this->validAttribute($attr)) {
            if (preg_match('/^is_/', $key)) {
                return false;
            }

            return null;
        }

        throw new \RuntimeException("Unknown attribute: $attr");
    }

    public function __isset(string $attr): bool
    {
        return $this->validAttribute($attr)
             && isset($this->record[$this->attributeToKey($attr)]);
    }

    private function attributeToKey(string $attr): string
    {
        return strtolower(preg_replace('/([A-Z])/', '_\1', $attr));
    }

    private function validAttribute(string $attr): bool
    {
        // @phpstan-ignore-next-line
        return \in_array($attr, $this->validAttributes, true);
    }

    /**
     * @return mixed data that can be serialized by json_encode
     */
    // We don't specify a return type here as PHP 8 has "mixed" as the return
    // type for "JsonSerializable::jsonSerialize", but PHP 7 doesn't support
    // "mixed".
    public function jsonSerialize()
    {
        return $this->record;
    }
}
