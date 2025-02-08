<?php
namespace Advancedynamic\Codeigniter\Attributeroutes\Attributes;

#[\Attribute]
class GetRoute {
    public function __construct(public string $path, public ?array $filter = []) {}
}