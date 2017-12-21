<?php

declare(strict_types=1);

namespace Widmogrod\Useful;

class SnapshotIterator extends \IteratorIterator
{
    private $inMemoryValid;
    private $inMemoryCurrent;
    private $inSnapshot;

    public function valid()
    {
        if (null === $this->inMemoryValid) {
            $this->inMemoryValid = parent::valid();
        }

        return $this->inMemoryValid;
    }

    public function current()
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
