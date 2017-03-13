<?php

namespace Dsh\AbstractModel\Exception;

use Psr\Container\NotFoundExceptionInterface;
use Exception;

class PropertyNotFoundException
    extends Exception
    implements NotFoundExceptionInterface
{ }