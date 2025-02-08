<?php
namespace Advancedynamic\Codeigniter\Attributeroutes\Attributes;

#[\Attribute]
class PatchRoute {
    public function __construct(public string $path, public ?array $filter = []) {}
}