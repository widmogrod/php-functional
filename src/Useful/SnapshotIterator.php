<?php

declare(strict_types=1);

namespace Widmogrod\Useful;

class SnapshotIterator extends \IteratorIterator
{
    private $inMemoryValid;
    private $inMemoryCurrent;
    private $inSnapshot;

    public function valid(): bool
    {
        if (null === $this->inMemoryValid) {
            $this->inMemoryValid = parent::valid();
        }

        return $this->inMemoryValid;
    }

    public function current(): mixed
    {
        if (null === $this->inMemoryCurrent) {
            $this->inMemoryCurrent = parent::current();
        }

        return $this->inMemoryCurrent;
    }

    public function snapshot()
    {
        if (null === $this->inSnapshot) {
            $this->inSnapshot = new self($this->getInnerIterator());
            $this->inSnapshot->next();
        }

        return $this->inSnapshot;
    }
}
