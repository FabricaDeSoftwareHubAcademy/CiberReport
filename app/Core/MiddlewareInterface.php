<?php

namespace Core;

interface MiddlewareInterface
{
    public function handle(): bool;
}