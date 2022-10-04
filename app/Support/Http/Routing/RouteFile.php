<?php

namespace Support\Http\Routing;

use Illuminate\Routing\Router;
use Illuminate\Routing\RouteRegistrar;

/**
 * @codeCoverageIgnore
 */
abstract class RouteFile
{
    /**
     * @var array<mixed>
     */
    private array $options;

    private Router|RouteRegistrar $router;

    abstract protected function routes(Router $router): void;

    /**
     * @param array<mixed> $options
     */
    public function __construct(array $options = [])
    {
        $this->router = app(Router::class);

        $this->options = $options;
    }

    /**
     * @SuppressWarnings(PHPMD.ElseExpression)
     *
     * @return void
     */
    public function registerRouteGroups(): void
    {
        if ($this->router instanceof RouteRegistrar) {
            $this->router->group(function ($router): void {
                $this->routes($router);
            });

            return;
        }

        $this->router->group($this->options, function ($router): void {
            $this->routes($router);
        });
    }

    /**
     * Dynamically handle calls into the router instance.
     *
     * @param string $method
     * @param array<mixed> $parameters
     *
     * @return $this
     */
    public function __call(string $method, array $parameters): self
    {
        // @phpstan-ignore-next-line
        $this->router = call_user_func_array([$this->router, $method], $parameters);

        return $this;
    }
}
