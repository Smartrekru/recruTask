<?php

namespace Drupal\di_articles;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Service description.
 */
class ArticlesService {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs an ArticlesService object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Get articles.
   */
  public function getArticles($categoryId = NULL): array {
    $nodeStorage = $this->entityTypeManager->getStorage('node');
    $nodes = $nodeStorage->getQuery()
      ->condition('type', 'article')
      ->execute();
    $data = [];
    foreach ($nodes as $node) {
      $node = $this->entityTypeManager->getStorage('node')->load($node);
      if (!in_array($categoryId, $node->field_category->getValues())) {
        continue;
      }
      if ($node->isPublished()) {
        $data[] = [
          'title' => $node->getTitle(),
          'body' => $node->body->value,
        ];
      }
    }

    return $data;
  }

}
