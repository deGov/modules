<?php

namespace Drupal\degov_simplenews\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SettingsForm.
 *
 * @package Drupal\degov_simplenews
 */
class SettingsForm extends ConfigFormBase {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a \Drupal\degov_paragraph_node_reference\Form\SettingsForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The entity display repository service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface
   *   The entity type manager service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, LanguageManagerInterface $language_manager, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($config_factory);
    $this->languageManager = $language_manager;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('language_manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return [
      'degov_simplenews.settings',
    ];
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'degov_simplenews_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $privacy_policy = $this->config('degov_simplenews.settings')->get('privacy_policy');
    $languages = $this->languageManager->getLanguages();
    $default_language_id = $this->languageManager->getDefaultLanguage()->getId();
    $node_storage = $this->entityTypeManager->getStorage('node');

    $form['language'] = [
      '#title'=> $this->t('Simplenews settings'),
      '#type' => 'fieldset',
      '#tree' => TRUE,
    ];

    foreach ($languages as $language) {
      $language_id = $language->getId();
      $default_value = NULL;

      if (isset($privacy_policy[$language_id])) {
        $node = $node_storage->load($privacy_policy[$language_id]);

        if ($node instanceof NodeInterface) {
          $default_value = $node;
        }
      }

      $form['language'][$language_id] = [
        '#title' => $this->t('Privacy policy page (@langcode)', ['@langcode' => $language_id]),
        '#type' => 'entity_autocomplete',
        '#target_type' => 'node',
        '#default_value' => $default_value,
        '#required' => $default_language_id === $language_id,
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $privacy_policy = [];

    foreach ($form_state->getValue('language') as $language_id => $nid) {
      if (!empty($nid)) {
        $privacy_policy[$language_id] = $nid;
      }
    }

    $this->configFactory()->getEditable('degov_simplenews.settings')
      ->set('privacy_policy', $privacy_policy)
      ->save();

    Cache::invalidateTags(['degov_simplenews_front_page']);
    parent::submitForm($form, $form_state);
  }
}
