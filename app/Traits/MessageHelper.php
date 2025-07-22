<?php

namespace App\Traits;

trait MessageHelper
{

    private string $order = 'ASC';
    private bool $last_msg = false;

    public function orderBy(string $order): self
    {
        $this->order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
        return $this;
    }

    public function lastMessage(): self
    {
        $this->last_msg = true;
        return $this;
    }
}
