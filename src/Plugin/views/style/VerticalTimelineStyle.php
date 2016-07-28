<?php
namespace Drupal\vertical_timeline\Plugin\views\style;

use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Datetime\DateTimePlus;

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

  /**
   * {@inheritdoc}
   */
  public function render() {
    if (empty($this->view->rowPlugin)) {
      debug('Drupal\views\Plugin\views\style\VerticalTimeline: Missing row plugin');
      return;
    }

    // If anything needs to be different in preview, do it here.
    if (!empty($this->view->live_preview)) {
      //return;
    }

    $rows = array();
    $options = $this->options;
    $field = $options['date_field'];
    $prev_group = '';

    foreach ($this->view->result as $row_index => $row) {
      $this->view->row_index = $row_index;
      $row = $this->view->rowPlugin->render($row);


      $date = '';
      if (isset($this->view->field[$field])) {

        // Create the group header, when required, and insert it into the rows array.

        $node = $row['#row']->_entity;
        $raw = $node->get($options['date_field'])->value;
        // Massage the date into the format required by the header.
        switch ($options['group_heading']) {
          case 'century':
            $obj = is_numeric($raw) ? DateTimePlus::createFromTimestamp($raw) : new DateTimePlus($raw);
            $date = substr($obj->format('Y'), 0, 2) . '00';
            break;
          case 'format':
            $obj = is_numeric($raw) ? DateTimePlus::createFromTimestamp($raw) : new DateTimePlus($raw);
            $date = $obj->format($options['group_heading_format']);
            break;
          case 'date':
            $date = $style->getField($id, $field);
            $date = strip_tags(htmlspecialchars_decode($date));
            //$date = \Drupal::service('renderer')->render($date);
            break;
          default:
            $date = NULL;
            break;
        }

        // See if this is a new header, different than the previous one.
        $group = $date;
        if ($group != $prev_group) {
          $row['group'] = [
            '#type' => 'markup',
            '#markup' => $date,
          ];
        }

        $prev_group = $group;
      }

      $rows[] = $row;
    }

    $build = array(
      '#theme' => $this->themeFunctions(),
      '#view' => $this->view,
      '#options' => $this->options,
      '#rows' => $rows,
    );
    unset($this->view->row_index);
    return $build;
  }


}
