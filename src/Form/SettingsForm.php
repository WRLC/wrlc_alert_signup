<?php

namespace Drupal\wrlc_alert_signup\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Configure WRLC Alert Signup settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wrlc_alert_signup_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['wrlc_alert_signup.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get array of node types for select options in node_type
    $entityTypeManager = \Drupal::service('entity_type.manager');
    $types = [];
    $contentTypes = $entityTypeManager->getStorage('node_type')->loadMultiple();
    foreach ($contentTypes as $contentType) {
      $types[$contentType->id()] = $contentType->label();
    }

    // Google Group config
    $form['gg_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('WRLC Alerts Google Group ID'),
      '#default_value' => $this->config('wrlc_alert_signup.settings')->get('gg_id'),
      '#required' => TRUE
    ];

    // Node type config
    $form['node_type'] = [
      '#type' => 'select',
      '#title' => $this->t('WRLC Alerts Node Type'),
      '#options' => $types,
      '#default_value' => $this->config('wrlc_alert_signup.settings')->get('node_type'),
      '#required' => TRUE
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('wrlc_alert_signup.settings')
      ->set('gg_id', $form_state->getValue('gg_id'))
      ->save();
    parent::submitForm($form, $form_state);
    $this->config('wrlc_alert_signup.settings')
      ->set('node_type', $form_state->getValue('node_type'))
      ->save();
  }

}
