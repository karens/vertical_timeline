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

    $options['date_field'] = array('default' => '');
    $options['group_heading'] = array('default' => '');
    $options['group_heading_format'] = array('default' => '');

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

    $form['date_field'] = array(
      '#type' => 'select',
      '#title' => $this->t('Date'),
      '#options' => $options,
      '#default_value' => $this->options['date_field'],
      '#description' => $this->t('The field name of the date field that will be used as the date on the timeline.'),
    );
    $form['group_heading'] = array(
      '#type' => 'select',
      '#title' => $this->t('Group heading'),
      '#options' => array('' => $this->t('No Heading'), 'century' => $this->t('Century'), 'date' => $this->t('Full Date'), 'format' => $this->t('Custom Format')),
      '#description' => $this->t('The type of date heading to add to the timeline. This heading will be inserted at the first spot where the value of the heading changes.'),
      '#default_value' => $this->options['group_heading'],
    );
    $form['group_heading_format'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Group heading format'),
      '#description' => $this->t("If 'Custom Format' was selected above, input the format string to use for the date heading, using the formats from http://php.net/manual/en/function.date.php. For instance, 'M Y' will display a heading over all items with the same month and year, formatted as 'Jan 2016'."),
      '#default_value' => $this->options['group_heading_format'],
    );

  }

}
