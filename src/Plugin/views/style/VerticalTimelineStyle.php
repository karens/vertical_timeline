<?php
namespace Drupal\vertical_timeline\Plugin\views\style;

use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Style plugin for the timeline view.
 *
 * @ViewsStyle(
 *   id = "vertical_timeline_style",
 *   title = @Translation("Vertical Timeline"),
 *   help = @Translation("Displays content in a vertical timeline."),
 *   theme = "vertical_timeline_style",
 *   display_types = {"normal"}
 * )
 */
class VerticalTimelineStyle extends StylePluginBase {

  /**
   * Does the style plugin allows to use style plugins.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  /**
   * Does the style plugin support custom css class for the rows.
   *
   * @var bool
   */
  protected $usesRowClass = FALSE;

  /**
   * Does the style plugin support grouping of rows.
   *
   * @var bool
   */
  protected $usesGrouping = FALSE;

  /**
   * Does the style plugin for itself support to add fields to it's output.
   *
   * This option only makes sense on style plugins without row plugins, like
   * for example table.
   *
   * @var bool
   */
  protected $usesFields = TRUE;

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['date'] = array('default' => '');

    return $options;
  }

  /**
   * Builds the configuration form.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {

    parent::buildOptionsForm($form, $form_state);
    $options = array('' => $this->t('- None -'));
    $field_labels = $this->displayHandler->getFieldLabels(TRUE);
    $options += $field_labels;

    $form['date'] = array(
      '#type' => 'select',
      '#title' => $this->t('Date'),
      '#options' => $options,
      '#default_value' => $this->options['date'],
      '#description' => $this->t('Choose the date field.'),
    );

  }

}
