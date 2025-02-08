<?php
namespace Advancedynamic\Codeigniter\Attributeroutes\Attributes;

#[\Attribute]
class PutRoute {
    public function __construct(public string $path, public ?array $filter = []) {}
}