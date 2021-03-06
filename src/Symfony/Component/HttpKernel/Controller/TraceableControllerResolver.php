<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Controller;

use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\HttpFoundation\Request;

/**
 * TraceableControllerResolver.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TraceableControllerResolver implements ControllerResolverInterface, ArgumentResolverInterface
{
    private $resolver;
    private $stopwatch;
    private $argumentResolver;

    /**
     * Constructor.
     *
     * @param ControllerResolverInterface $resolver         A ControllerResolverInterface instance
     * @param Stopwatch                   $stopwatch        A Stopwatch instance
     * @param ArgumentResolverInterface   $argumentResolver Only required for BC
     */
    public function __construct(ControllerResolverInterface $resolver, Stopwatch $stopwatch, ArgumentResolverInterface $argumentResolver = null)
    {
        $this->resolver = $resolver;
        $this->stopwatch = $stopwatch;
        $this->argumentResolver = $argumentResolver;

        // BC
        if (null === $this->argumentResolver) {
            $this->argumentResolver = $resolver;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getController(Request $request)
    {
        $e = $this->stopwatch->start('controller.get_callable');

        $ret = $this->resolver->getController($request);

        $e->stop();

        return $ret;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated This method is deprecated as of 3.1 and will be removed in 4.0.
     */
    public function getArguments(Request $request, $controller)
    {
        @trigger_error(sprintf('This %s method is deprecated as of 3.1 and will be removed in 4.0. Please use the %s instead.', __METHOD__, TraceableArgumentResolver::class), E_USER_DEPRECATED);

        if ($this->argumentResolver instanceof TraceableArgumentResolver) {
            return $this->argumentResolver->getArguments($request, $controller);
        }

        $e = $this->stopwatch->start('controller.get_arguments');

        $ret = $this->argumentResolver->getArguments($request, $controller);

        $e->stop();

        return $ret;
    }
}
