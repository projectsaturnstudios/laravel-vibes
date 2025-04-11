<?php

namespace ProjectSaturnStudios\Vibes\Exceptions;

use DomainException;

class InvalidPrimitiveHandler extends DomainException
{
    public static function primitiveHandlingMethodDoesNotExist(object $primitiveHandler, object $primitive, string $methodName): self
    {
        $primitiveHandlerClass = get_class($primitiveHandler);
        $primitiveClass = get_class($primitive);

        return new static("Tried to call `$methodName` on `$primitiveHandlerClass` to handle an event of class `$primitiveClass` but that method does not exist.");
    }

    public static function doesNotExist(string $primitiveHandlerClass): self
    {
        return new static("The primitive handler class `{$primitiveHandlerClass}` does not exist.");
    }

    public static function notATool(object $object): self
    {
        return new static('`'.get_class($object).'` must extend ProjectSaturnStudios\Vibes\Data\Tools\VibeTool');
    }

    public static function notAnPrimitiveHandler(object $object): self
    {
        return new static('`'.get_class($object).'` must implement ProjectSaturnStudios\Vibes\Contracts\PrimitiveHandler');
    }

    public static function notAnPrimitiveHandlingClassName(string $className): self
    {
        return new static('`'.$className.'` must implement ProjectSaturnStudios\Vibes\Contracts\PrimitiveHandler');
    }
}
