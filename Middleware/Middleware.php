<?php

/*
 * This file is part of the DriftPHP Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Drift\EventBus\Middleware;

use Drift\CommandBus\Exception\InvalidMiddlewareException;
use React\Promise\FulfilledPromise;
use React\Promise\PromiseInterface;

/**
 * Class Middleware.
 */
class Middleware implements DebugableMiddleware
{
    /**
     * @var Middleware
     */
    private $middleware;

    /**
     * @var string
     */
    private $method;

    /**
     * Middleware constructor.
     *
     * @param object $middleware
     * @param string $method
     *
     * @throws InvalidMiddlewareException
     */
    public function __construct(
        $middleware,
        string $method
    ) {
        if (!method_exists($middleware, $method)) {
            throw new InvalidMiddlewareException();
        }

        $this->middleware = $middleware;
        $this->method = $method;
    }

    /**
     * Handle.
     *
     * @param string $eventName
     * @parma Object $event
     *
     * @param callable $next
     *
     * @return PromiseInterface
     */
    public function dispatch(
        string $eventName,
        $event,
        callable $next
    ): PromiseInterface {
        $result = $this
            ->middleware
            ->{$this->method}($eventName, $event, $next);

        return ($result instanceof PromiseInterface)
            ? $result
            : new FulfilledPromise($result);
    }

    /**
     * Get inner middleware info.
     *
     * @return array
     */
    public function getMiddlewareInfo(): array
    {
        return [
            'class' => get_class($this->middleware),
            'method' => $this->method,
        ];
    }
}
