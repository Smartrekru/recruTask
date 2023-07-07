<?php

namespace Drupal\Tests\rekru_blog\Functional;

use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\taxonomy\Entity\Term;
use Drupal\Tests\BrowserTestBase;

/**
 * Test the functionalities created by rekru_blog.
 *
 * @group rekru_custom_modules
 * @group rekru_blog
 */
class RekruBlogTests extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'rekru';

  /**
   * {@inheritdoc}
   */
  protected $profile = 'standard';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'media_library',
    'media',
    'paragraphs',
    'responsive_image',
    'svg_image_responsive',
    'select2',
    'paragraphs_entity_embed',
    'rekru_breadcrumb',
    'rekru_blog',
    'twig_tweak',
    'rabbit_hole',
  ];

  /**
   * User entity.
   *
   * @var \Drupal\user\Entity\User
   */
  protected User $user;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $rand = bin2hex(time());

    $this->user = $this->drupalCreateUser(['administer blocks'],
    'test_admin' . $rand, TRUE);

    $this->drupalLogin($this->user);
  }

  /**
   * Tests RekruBlogListings.
   */
  public function testRekruBlogListings() {
    \Drupal::service('module_installer')->install(['blog_tests']);

    // Test blog categories data.
    $blogCategories = [
      [
        'name' => 'Blog 1',
        'tid' => '1',
        'vid' => 'blog_category',
        'custom_alias_url' => '/blog/blog-1',
      ],
      [
        'name' => 'Blog 2',
        'tid' => '2',
        'vid' => 'blog_category',
        'custom_alias_url' => '/blog/blog-2',
      ],
      [
        'name' => 'Archived Blog 1',
        'tid' => '3',
        'vid' => 'blog_category_archived',
        'custom_alias_url' => '/blog-archiwum/blog-archiwum-1',
      ],
      [
        'name' => 'Archived Blog 2',
        'tid' => '4',
        'vid' => 'blog_category_archived',
        'custom_alias_url' => '/blog-archiwum/blog-archiwum-2',
      ],
    ];

    // Test blog posts data.
    $blogPosts = [
      [
        'nid' => '1',
        'title' => 'Blog Post 1',
        'field_blog_category' => '1',
      ],
      [
        'nid' => '2',
        'title' => 'Blog Post 2',
        'field_blog_category' => '2',
      ],
      [
        'nid' => '3',
        'title' => 'Archived Post 1',
        'field_blog_category_archived' => '3',
      ],
      [
        'nid' => '4',
        'title' => 'Archived Post 2',
        'field_blog_category_archived' => '4',
      ],
    ];

    // Create test blog categories.
    foreach ($blogCategories as $blogCategory) {
      Term::create([
        'tid' => $blogCategory['tid'],
        'vid' => $blogCategory['vid'],
        'name' => $blogCategory['name'],
      ])->save();
    }

    // Create test blog posts.
    foreach ($blogPosts as $blogPost) {
      $node = Node::create([
        'nid' => $blogPost['nid'],
        'title' => $blogPost['title'],
        'type' => 'blog',
      ]);

      isset($blogPost['field_blog_category']) ?
        $node->set('field_blog_category', $blogPost['field_blog_category']) :
        $node->set('field_blog_category_archived',
          $blogPost['field_blog_category_archived']);

      $node->save();
    }

    // Verify that only posts with blog category exist on /blog page.
    $this->drupalGet('/blog');
    foreach ($blogPosts as $index => $blogPost) {
      $postTitle = $this->xpath(
        '//a[contains(text(), "' . $blogPost['title'] . '")]'
      );
      $index < 2 ? $this->assertNotEmpty($postTitle) :
        $this->assertEmpty($postTitle);
    }

    // Verify that only posts with archived blog category exist on
    // /blog-archiwum page.
    $this->drupalGet('/blog-archiwum');
    foreach ($blogPosts as $index => $blogPost) {
      $postTitle = $this->xpath(
        '//a[contains(text(), "' . $blogPost['title'] . '")]'
      );
      $index > 1 ? $this->assertNotEmpty($postTitle) :
        $this->assertEmpty($postTitle);
    }

    // Verify that posts with specific category exist on this category listing.
    foreach ($blogPosts as $index => $blogPost) {
      if ($index < 2) {
        $this->drupalGet('/blog/' . $blogPost['field_blog_category']);
      }
      else {
        $this->drupalGet('/blog-archiwum/' .
          $blogPost['field_blog_category_archived']);
      }
      $postTitle = $this->xpath(
        '//a[contains(text(), "' . $blogPost['title'] . '")]'
      );
      $this->assertNotEmpty($postTitle);
    }

  }

}
