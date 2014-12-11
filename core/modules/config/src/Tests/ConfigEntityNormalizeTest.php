<?php

namespace Drupal\config\Tests;

use Drupal\simpletest\KernelTestBase;

/**
 * Tests the listing of configuration entities.
 *
 * @group config
 */
class ConfigEntityNormalizeTest extends KernelTestBase {

  /**
   * Set to TRUE to strict check all configuration saved.
   *
   * @see \Drupal\Core\Config\Testing\ConfigSchemaChecker
   *
   * @var bool
   */
  protected $strictConfigSchema = TRUE;

  public static $modules = array('config_test');

  protected function setUp() {
    parent::setUp();
    $this->installConfig(static::$modules);
  }

  public function testNormalize() {
    $config_entity = entity_create('config_test', array('id' => 'system', 'label' => 'foobar', 'weight' => 1));
    $config_entity->save();

    // Modify stored config entity, this is comparable with a schema change.
    $config = \Drupal::config('config_test.dynamic.system');
    $data = array(
      'label' => 'foobar',
      'additional_key' => TRUE
    ) + $config->getRawData();
    $config->setData($data)->save();
    $this->assertNotIdentical($config_entity->toArray(), $config->getRawData(), 'Stored config entity is not is equivalent to config schema.');

    $config_entity = entity_load('config_test', 'system', TRUE);
    $config_entity->save();

    $config = \Drupal::config('config_test.dynamic.system');
    $this->assertIdentical($config_entity->toArray(), $config->getRawData(), 'Stored config entity is equivalent to config schema.');
  }

}
