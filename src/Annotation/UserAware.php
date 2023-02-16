<?php
namespace App\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class UserAware
{
    public $userFieldName;
}