<?php
namespace Advancedynamic\Codeigniter\Attributeroutes\Attributes;;

#[\Attribute]
class PostRoute {
    public function __construct(public string $path, public ?array $filter = []) {}
}