<?php

namespace Drupal\di_articles\Controller;

use Drupal\di_articles\ArticlesService;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Di articles routes.
 */
class ArticlesController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The controller constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ArticlesService $service) {
    $this->entityTypeManager = $entity_type_manager;
    $this->service = $service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('di_articles.service'),
    );
  }

  /**
   * Builds the response.
   */
  public function index(Request $request): JsonResponse {
    $category = $request->query->get('q');

    $taxoonomy = $this->entityTypeManager->getStorage('taxonomy_term')->load($category);

    if (!$taxoonomy) {
      return new JsonResponse('', 404);
    }
    $data = $this->service->getArticles($category);
    return new JsonResponse($data);
  }

}
