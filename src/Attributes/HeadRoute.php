<?php
namespace Advancedynamic\Codeigniter\Attributeroutes\Attributes;

#[\Attribute]
class HeadRoute {
    public function __construct(public string $path, public ?array $filter = []) {}
}