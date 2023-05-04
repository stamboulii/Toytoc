<?php

namespace App\Twig;

use Twig\TwigFunction;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Twig\Extension\AbstractExtension;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Helper\HttpQueryHelper;
use App\Repository\CategoryRepository;
use Symfony\Component\Routing\RouterInterface;

final class TwigExtension extends AbstractExtension implements EventSubscriberInterface
{
    private Request $request;

    public function __construct(private readonly RouterInterface $router, private readonly CategoryRepository $categoryRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('order_by', [$this, 'getOrderBy'], ['is_safe' => ['html']]),
            new TwigFunction('order_query_attributes', [$this, 'getOrderQueryAttributes']),
            new TwigFunction('categories_side_bar', [$this, 'categoriesSideBar']),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function categoriesSideBar(): array
    {
        return $this->categoryRepository->findBy([], ['name' => 'ASC'], 10);
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $this->request = $event->getRequest();
        }
    }

    /**
     * Appliquer un tri.
     *
     * @param string $id
     * @param string $name
     *
     * @return string
     */
    public function getOrderBy(string $id, string $name): string
    {
        if (null === $this->request) {
            return $name;
        }

        $route = $this->request->attributes->get('_route');

        return sprintf(
            '<a class="%s %s" href="%s">%s%s</a>',
            '',
            '',
            $this->router->generate($route, array_merge($this->request->query->all(), [
                'page'  => HttpQueryHelper::getPage($this->request),
                'limit'  => HttpQueryHelper::getLimit($this->request),
                'order' => $id,
                'sort'  => 'asc' === HttpQueryHelper::getSort($this->request) ? 'desc' : 'asc',
            ], $this->request->attributes->get('_route_params'))),
            $name,
            ''
        );
    }

    /**
     * Obtenir des attributs de requête de commande.
     *
     * @return array
     */
    public function getOrderQueryAttributes(): array
    {
        return [
            'order' => HttpQueryHelper::getOrder($this->request),
            'sort'  => HttpQueryHelper::getSort($this->request),
        ];
    }
}
