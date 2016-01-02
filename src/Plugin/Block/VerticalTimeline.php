<?php

/**
 * @file
 * Contains \Drupal\vertical_timeline\Plugin\Block\VerticalTimeline.
 */

namespace Drupal\vertical_timeline\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\Render\Renderer;

/**
 * Provides a 'VerticalTimeline' block.
 *
 * @Block(
 *  id = "vertical_timeline",
 *  admin_label = @Translation("Vertical timeline"),
 * )
 */
class VerticalTimeline extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Entity\EntityManager definition.
   *
   * @var Drupal\Core\Entity\EntityManager
   */
  protected $entity_manager;

  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entity_query;

  protected $renderer;


  /**
   * Construct.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(
        array $configuration,
        $plugin_id,
        $plugin_definition,
        EntityManager $entity_manager,
        QueryFactory $entity_query,
        Renderer $renderer
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entity_manager = $entity_manager;
    $this->entity_query = $entity_query;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.manager'),
      $container->get('entity.query'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['content_type'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Content type'),
      '#description' => $this->t('The machine name of the content type to display in this timeline.'),
      '#default_value' => isset($this->configuration['content_type']) ? $this->configuration['content_type'] : '',
    );
    $form['view_mode'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('View mode'),
      '#description' => $this->t('The content view mode to display in this timeline.'),
      '#default_value' => isset($this->configuration['view_mode']) ? $this->configuration['view_mode'] : 'teaser',
    );
    $form['date_field'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Date field'),
      '#description' => $this->t('The field name of the date field that will be used as the date on the timeline.'),
      '#default_value' => isset($this->configuration['date_field']) ? $this->configuration['date_field'] : '',
    );
    $form['sort_order'] = array(
      '#type' => 'select',
      '#title' => $this->t('Sort order'),
      '#options' => array('DESC' => 'descending', 'ASC' => 'ascending'),
      '#description' => $this->t('The sort order to use for the selected date field.'),
      '#default_value' => isset($this->configuration['sort_order']) ? $this->configuration['sort_order'] : 'DESC',
    );
    $form['group_heading'] = array(
      '#type' => 'select',
      '#title' => $this->t('Group heading'),
      '#options' => array('' => $this->t('No Heading'), 'century' => $this->t('Century'), 'date' => $this->t('Full Date'), 'format' => $this->t('Custom Format')),
      '#description' => $this->t('The type of date heading to add to the timeline. This heading will be inserted at the first spot where the value of the heading changes.'),
      '#default_value' => isset($this->configuration['group_heading']) ? $this->configuration['group_heading'] : '',
    );
    $form['group_heading_format'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Group heading format'),
      '#description' => $this->t("If 'Custom Format' was selected above, input the format string to use for the date heading, using the formats from http://php.net/manual/en/function.date.php. For instance, 'M Y' will display a heading over all items with the same month and year, formatted as 'Jan 2016'."),
      '#default_value' => isset($this->configuration['group_heading_format']) ? $this->configuration['group_heading_format'] : '',
    );
    $form['pager_limit'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Items per page'),
      '#description' => $this->t('The number of items to display on each page.'),
      '#default_value' => isset($this->configuration['pager_limit']) ? $this->configuration['pager_limit'] : 10,
    );
    $form['pager_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Pager ID'),
      '#description' => $this->t('A unique ID for this pager that will not conflict with any other pagers on the same page.'),
      '#default_value' => isset($this->configuration['pager_id']) ? $this->configuration['pager_id'] : 9,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['content_type'] = $form_state->getValue('content_type');
    $this->configuration['view_mode'] = $form_state->getValue('view_mode');
    $this->configuration['date_field'] = $form_state->getValue('date_field');
    $this->configuration['pager_id'] = $form_state->getValue('pager_id');
    $this->configuration['pager_limit'] = $form_state->getValue('pager_limit');
    $this->configuration['sort_order'] = $form_state->getValue('sort_order');
    $this->configuration['group_heading'] = $form_state->getValue('group_heading');
    $this->configuration['group_heading_format'] = $form_state->getValue('group_heading_format');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Use the injected queryFactory to get a query of entity type 'node'.
    $query = $this->entity_query->get('node')
      ->condition('status', 1)
      ->condition('type', $this->configuration['content_type'])
      ->sort($this->configuration['date_field'], $this->configuration['sort_order']);

    $query->pager($this->configuration['pager_limit'], $this->configuration['pager_id']);
    $nids = $query->execute();

    // Load the results into an array of node objects.
    $nodes = $this->entity_manager->getStorage('node')->loadMultiple($nids);
    $prev_group = '';

    $rows = array();
    foreach ($nodes as $delta => $node) {
      $rows[$delta]['content'] = entity_view($node, $this->configuration['view_mode']);
      $raw = $node->get($this->configuration['date_field'])->value;

      switch ($this->configuration['group_heading']) {
        case 'century':
          $obj = is_numeric($raw) ? DateTimePlus::createFromTimestamp($raw) : new DateTimePlus($raw);
          $date = substr($obj->format('Y'), 0, 2) . '00';
          break;
        case 'format':
          $obj = is_numeric($raw) ? DateTimePlus::createFromTimestamp($raw) : new DateTimePlus($raw);
          $date = $obj->format($this->configuration['group_heading_format']);
          break;
        case 'date':
          $date = $node->{$this->configuration['date_field']}->view($this->configuration['view_mode']);
          // $date = \Drupal::service('renderer')->render($date);
          $date = $this->renderer->render($date);
          $date = strip_tags(htmlspecialchars_decode($date));
          break;
        default:
          $date = NULL;
          break;
      }

      $group = $date;
      if ($group != $prev_group) {
        $rows[$delta]['group'] = array(
          '#type' => 'markup',
          '#markup' => $date,
        );
      }
      $prev_group = $group;

    }

    $rows_array = array(
      '#theme' => 'vertical_timeline_rows',
      '#rows' => $rows,
    );
    $pager_array = array(
      '#type' => 'pager',
      '#element' => $this->configuration['pager_id'],
    );

    // Theme the array of results into a timeline.
    return [
        '#theme' => 'vertical_timeline_wrapper',
        '#content' => $rows_array,
        '#pager' => $pager_array,
    ];

    return $build;
  }

}
