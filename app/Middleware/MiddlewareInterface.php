<?php

namespace Middleware;

interface MiddlewareInterface
{
    public function handle(): bool;
}