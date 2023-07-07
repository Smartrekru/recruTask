<?php

declare(strict_types=1);

namespace Drupal\link4_blog;

use Drupal\node\Entity\Node;
use Drupal\node\NodeStorage;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class BlogService that provides blog functions.
 */
class BlogService {

  /**
   * Node storage.
   *
   * @var \Drupal\node\NodeStorage
   */
  protected NodeStorage $nodeStorage;

  /**
   * Constructs a new BlogService object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->nodeStorage = $entity_type_manager->getStorage('node');
  }

  /**
   * Returns blog posts ids with pagination.
   *
   * @param string|null $category
   *   Blog posts category.
   * @param int $limit
   *   How much items per page.
   *
   * @return array
   *   Return array of blog posts ids.
   */
  public function getBlogPosts(
  $category = NULL,
  $limit = 9): array {
    $query = $this->nodeStorage->getQuery();
    $query->condition('type', 'blog')
      ->condition('status', Node::PUBLISHED);
      
    $query->condition('field_blog_category', $category);

    $query->sort('created', 'DESC')
      ->pager($limit);

    return $query->execute();
  }

}
