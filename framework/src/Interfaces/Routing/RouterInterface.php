<?php

namespace Framework\Interfaces\Routing;

use Framework\Http\Request;

interface RouterInterface
{
    public function dispatch(Request $request): array;
}
