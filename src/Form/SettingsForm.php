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
    $form['gg_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google Group ID'),
      '#default_value' => $this->config('wrlc_alert_signup.settings')->get('gg_id'),
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
  }

}
