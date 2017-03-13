<?php

namespace Dsh\AbstractModel\Exception;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Exception;

class AbstractModelException
    extends Exception
    implements ContainerExceptionInterface
{ }