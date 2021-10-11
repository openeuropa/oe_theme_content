<?php

declare(strict_types = 1);

namespace Drupal\Tests\oe_theme_content_publication\Kernel;

use Drupal\node\Entity\Node;
use Drupal\Tests\oe_content\Traits\EntityReferenceRevisionTrait;
use Drupal\Tests\oe_theme\Kernel\ContentRenderTestBase;
use Drupal\Tests\oe_theme\PatternAssertions\ListItemAssert;
use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Tests call for tenders rendering.
 *
 * @group batch2
 */
class PublicationRenderTest extends ContentRenderTestBase {

  use UserCreationTrait;
  use EntityReferenceRevisionTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'oe_content_sub_entity',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('oe_author');
    module_load_include('install', 'oe_content');
    oe_content_install(FALSE);
  }

  /**
   * Test a publication being rendered as a teaser.
   */
  public function testTeaser(): void {
    $author = $this->container->get('entity_type.manager')->getStorage('oe_author')->create([
      'type' => 'oe_corporate_body',
      'oe_skos_reference' => [
        'http://publications.europa.eu/resource/authority/corporate-body/ACM',
      ],
    ]);
    $author->save();

    /** @var \Drupal\node\Entity\Node $node */
    $node = Node::create([
      'type' => 'oe_publication',
      'title' => 'Test Publication node',
      'oe_teaser' => 'Test teaser text.',
      'oe_publication_type' => [
        'http://publications.europa.eu/resource/authority/resource-type/DIR_DEL',
      ],
      'oe_publication_date' => [
        'value' => '2020-04-15',
      ],
      'oe_authors' => [$author],
      'uid' => 0,
      'status' => 1,
    ]);
    $node->save();

    $build = $this->nodeViewBuilder->view($node, 'teaser');
    $html = $this->renderRoot($build);

    $assert = new ListItemAssert();
    $expected_values = [
      'title' => 'Test Publication node',
      'meta' => "Delegated directive | 15 April 2020\n | Arab Common Market",
      'description' => 'Test teaser text.',
    ];
    $assert->assertPattern($expected_values, $html);

    // Test short title fallback.
    $node->set('oe_content_short_title', 'Publication short title')->save();
    $build = $this->nodeViewBuilder->view($node, 'teaser');
    $html = $this->renderRoot($build);
    $expected_values['title'] = 'Publication short title';
    $assert->assertPattern($expected_values, $html);

    // Add thumbnail.
    $media_image = $this->createMediaImage('publication_image');
    $node->set('oe_publication_thumbnail', $media_image)->save();

    $build = $this->nodeViewBuilder->view($node, 'teaser');
    $html = $this->renderRoot($build);

    $expected_values['image'] = [
      'src' => 'styles/oe_theme_publication_thumbnail/public/placeholder_publication_image.png',
      'alt' => '',
    ];
    $assert->assertPattern($expected_values, $html);

    // Add a second resource type.
    $node->set('oe_publication_type', [
      'http://publications.europa.eu/resource/authority/resource-type/ABSTRACT_JUR',
      'http://publications.europa.eu/resource/authority/resource-type/AID_STATE',
    ]);
    // Add a second responsible department.
    $author2 = $this->container->get('entity_type.manager')->getStorage('oe_author')->create([
      'type' => 'oe_corporate_body',
      'oe_skos_reference' => [
        'http://publications.europa.eu/resource/authority/corporate-body/ACP-EU_JPA',
      ],
    ]);
    $author2->save();
    $node->set('oe_authors', [$author, $author2]);
    $node->save();

    $build = $this->nodeViewBuilder->view($node, 'teaser');
    $html = $this->renderRoot($build);

    $expected_values['meta'] = "Abstract, State aid | 15 April 2020\n | Arab Common Market, ACP–EU Joint Parliamentary Assembly";
    $assert->assertPattern($expected_values, $html);
  }

}
