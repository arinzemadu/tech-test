<?php

namespace Drupal\custom_layout_builder\Plugin\Layout;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Layout\LayoutDefault;

/**
 * Base class of our custom layouts with configurable HTML classes.
 *
 * @internal
 *   Plugin classes are internal.
 */
class CustomLayoutClassBase extends LayoutDefault {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $configuration = parent::defaultConfiguration();
    return $configuration + [
      'wrapper_classes' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['wrapper_classes'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Wrapper extra classes'),
      '#default_value' => $this->configuration['wrapper_classes'],
      '#description' => $this->t('Extra wrapper classes. Type as many as you want but remember to separate them by using a single space character.'),
    ];

    foreach ($this->getPluginDefinition()->getRegionNames() as $region_name) {
      $form['region_classes'][$region_name] = [
        '#type' => 'textfield',
        '#title' => $this->t('Extra classes for @region region', [
          '@region' => $region_name,
        ]),
        '#default_value' => $this->configuration['region_classes'][$region_name],
        '#description' => $this->t('Extra classes for the @region region wrapper. Type as many as you want but remember to separate them by using a single space character.', [
          '@region' => $region_name,
        ]),
      ];
    }

    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['wrapper_classes'] = $form_state->getValue('wrapper_classes');

    foreach ($this->getPluginDefinition()->getRegionNames() as $region_name) {
      $this->configuration['region_classes'][$region_name] = $form_state->getValue("region_classes")[$region_name] ?? '';
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $regions): array {
    $build = parent::build($regions);
    $wrapper_classes = explode(' ', (string) $this->configuration['wrapper_classes']);
    $build['#attributes']['class'] = $wrapper_classes;

    foreach (array_keys($regions) as $region_name) {
      $region_classes = explode(' ', (string) $this->configuration['region_classes'][$region_name]);
      $build[$region_name]['#attributes']['class'] = $region_classes;
    }

    return $build;
  }

}
