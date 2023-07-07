<?php

declare(strict_types=1);

namespace Drupal\link4_blog\Controller;

use Drupal\link4_blog\BlogService;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a BlogListingController that lists all blog with pager.
 */
class BlogListingController extends ControllerBase {

  /**
   * Constructs BlogListingController object.
   */
  public function __construct(
    protected BlogService $blog_service,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get(BlogService::class)
    );
  }

  /**
   * Returns render array for blog listing.
   *
   * @return array
   *   Return type.
   */
  public function blogList(): array {
    $category = $this->route_match->getParameter('category') ?? '';
    $blogPosts = $this->blog_service->getBlogPosts(
      empty($category) ? NULL : $category->id()
    );

    return [
      'results' => [
        '#theme' => 'blog_listing',
        '#blog_posts' => array_slice($blogPosts, 1, count($blogPosts)),
        '#first_blog_post' => reset($blogPosts),
        '#title' => $this->route_match->getRouteObject()->getDefault('_title'),
        '#category' => empty($category) ? '' : $category->label(),
      ],
      'pager' => [
        '#type' => 'pager',
      ],
    ];
  }

}
