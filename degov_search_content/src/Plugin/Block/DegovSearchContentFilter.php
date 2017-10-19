<?php

namespace Drupal\degov_search_content\Plugin\Block;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilder;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element\Form;
use Drupal\facets\FacetManager\DefaultFacetManager;
use Drupal\facets\FacetSource\FacetSourcePluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides a block to filter the search.
 *
 * @Block(
 *   id = "degov_search_content_filter",
 *   admin_label = @Translation("DeGov search content filters")
 * )
 */
class DegovSearchContentFilter extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The facet manager.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $facetStorage;

  /**
   * The facet manager.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $facetSourceStorage;

  /**
   * The Manager of facet sources.
   *
   * @var \Drupal\facets\FacetManager\DefaultFacetManager
   */
  protected $facetManager;

  /**
   * DegovSearchContentFilter constructor.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\facets\FacetSource\FacetSourcePluginManager $facetSourcePluginManager
   * @param \Drupal\Core\Entity\EntityStorageInterface $facet_storage
   * @param \Drupal\facets\FacetManager\DefaultFacetManager $facet_manager
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FacetSourcePluginManager $facetSourcePluginManager, EntityStorageInterface $facet_storage, DefaultFacetManager $facet_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->facetSourceManager = $facetSourcePluginManager;
    $this->facetStorage = $facet_storage;
    $this->facetManager = $facet_manager;
  }

  /**
   * Creates an instance of the plugin.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The container to pull out services used in the plugin.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns an instance of this plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.facets.facet_source'),
      $container->get('entity_type.manager')->getStorage('facets_facet'),
      $container->get('facets.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'facet_source_id' => 'search_api:views_page__search_content__page_1',
    ];
  }

  /**
   * Gets the list of Facet sources.
   *
   * @return array
   */
  private function getFacetSourcesOptions() {
    $options = [];
    $facet_sources = $this->facetSourceManager->getDefinitions();
    if (!empty($facet_sources)) {
      /**
       * @var string $plugin_id
       * @var array $facet_source
       */
      foreach($facet_sources as $plugin_id => $facet_source) {
        $options[$plugin_id] = $facet_source['label'];
      }
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $options = $this->getFacetSourcesOptions();
    $form['facet_source_id'] = [
      '#type' => 'radios',
      '#title' => $this->t('Select the search source entity'),
      '#options' => $options,
      '#default_value' => isset($this->configuration['facet_source_id']) ? $this->configuration['facet_source_id'] : '',
      '#required' => TRUE,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['facet_source_id'] = $form_state->getValue('facet_source_id');
  }

  /**
   * Converts list of links to select form element.
   *
   * @param array $facet_build
   *
   * @param \Drupal\facets\FacetInterface $facet
   *
   * @return mixed
   */
  private function convertFacetWidget($facet_build, $facet) {
    $result = $facet_build;
    $result[0]['#title'] = $facet->getName();
    if (!empty($facet_build[0]['#items'])) {
      $widget = $facet->getWidget();
      if ($widget['type'] == 'dropdown') {
        unset($result[0]['#theme']);
        unset($result[0]['#context']);
        unset($result[0]['#attached']);
        $result[0]['#type'] = 'select';
        $result[0]['#name'] = $facet->getUrlAlias();
        $result[0]['#options'][''] = $widget['config']['default_option_label'];
        $result[0]['#wrapper_attributes']['class'] = 'selectric-wrapper selectric-facets-dropdown';
        foreach ($result[0]['#items'] as $item) {
          /** @var \Drupal\Core\Url $url */
          $url = $item['#url'];
          $options = $url->getOptions();
          $title = render($item['#title']);
          $result[0]['#options'][$options['query']['f'][0]] = strip_tags($title);
        }
        unset($result[0]['#items']);
      }
    }
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::service('form_builder')->getForm('\Drupal\degov_search_content\Form\SearchForm');
    $configuration = $this->getConfiguration();
    /** @var \Drupal\facets\FacetSource\FacetSourcePluginInterface $facetSourcePlugin */
    $facetSourcePlugin = $this->facetSourceManager->createInstance($configuration['facet_source_id']);
    if ($facetSourcePlugin->isRenderedInCurrentRequest()) {
      $facets = $this->facetStorage->loadByProperties(['facet_source_id' => $facetSourcePlugin->getPluginId()]);
      $form['filter'] = [
        '#type' => 'details',
        '#title' => t('Filter search results'),
        '#open' => TRUE,
        '#attributes' => ['class' => ['block-degov-search-content-filter']],
      ];
      /** @var \Drupal\facets\FacetInterface $facet */
      foreach ($facets as $facet) {
        $facet_build = $this->facetManager->build($facet);
        if (!empty($facet_build)) {
          $facet_build = $this->convertFacetWidget($facet_build, $facet);
          $form['filter'][$facet->getUrlAlias()] = $facet_build[0];
        }
      }

    }

    $form['actions'] = [
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Search'),
      ]
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    // A facet block cannot be cached, because it must always match the current
    // search results, and Search API gets those search results from a data
    // source that can be external to Drupal. Therefore it is impossible to
    // guarantee that the search results are in sync with the data managed by
    // Drupal. Consequently, it is not possible to cache the search results at
    // all. If the search results cannot be cached, then neither can the facets,
    // because they must always match.
    // Fortunately, facet blocks are rendered using a lazy builder (like all
    // blocks in Drupal), which means their rendering can be deferred (unlike
    // the search results, which are the main content of the page, and deferring
    // their rendering would mean sending an empty page to the user). This means
    // that facet blocks can be rendered and sent *after* the initial page was
    // loaded, by installing the BigPipe (big_pipe) module.
    //
    // When BigPipe is enabled, the search results will appear first, and then
    // each facet block will appear one-by-one, in DOM order.
    // See https://www.drupal.org/project/big_pipe.
    //
    // In a future version of Facet API, this could be refined, but due to the
    // reliance on external data sources, it will be very difficult if not
    // impossible to improve this significantly.
    //
    // Note: when using Drupal core's Search module instead of the contributed
    // Search API module, the above limitations do not apply, but for now it is
    // not considered worth the effort to optimize this just for Drupal core's
    // Search.
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    // The ID saved in the configuration is of the format
    // 'base_plugin:facet_id'. We're splitting that to get to the facet ID.
//    $facet_mapping = $this->getPluginId();
//    $facet_id = explode(PluginBase::DERIVATIVE_SEPARATOR, $facet_mapping)[1];
//
//    /** @var \Drupal\facets\FacetInterface $facet */
//    $facet = $this->facetStorage->load($facet_id);
//
//    return ['config' => [$facet->getConfigDependencyName()]];
  }


}
