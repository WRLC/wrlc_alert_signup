<?php
/** @noinspection PhpUnused */

namespace Drupal\wrlc_alert_signup\Form;

use Drupal;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;


/**
 * Configure WRLC Alert Signup settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string
  {
    return 'wrlc_alert_signup_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array
  {
    return ['wrlc_alert_signup.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array
  {

    // Get array of node types for select options in node_type
    $entityTypeManager = Drupal::service('entity_type.manager');
    $types = [];
    $contentTypes = $entityTypeManager->getStorage('node_type')->loadMultiple();
    foreach ($contentTypes as $contentType) {
      $types[$contentType->id()] = $contentType->label();
    }

    // Get array of Google API Client entities
    $clients = [];
    try {
      $google_api_clients = Drupal::entityTypeManager()->getStorage('google_api_client')->loadMultiple();
    } catch (InvalidPluginDefinitionException|PluginNotFoundException $e) {
      wrlc_alert_signup_log_error($e, 'wrlc_alert_signup');
      return [
        '#markup' => $this->t('Error loading Google API Clients')
      ];
    }
    foreach ($google_api_clients as $client) {
      $clients[$client->id()] = $client->label();
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

    // Google API Client
    $form['google_api_client'] = [
      '#type' => 'select',
      '#title' => $this->t('Google API Client'),
      '#options' => $clients,
      '#default_value' => $this->config('wrlc_alert_signup.settings')->get('google_api_client'),
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
  public function submitForm(array &$form, FormStateInterface $form_state): void
  {
    $this->config('wrlc_alert_signup.settings')
      ->set('gg_id', $form_state->getValue('gg_id'))
      ->save();
    $this->config('wrlc_alert_signup.settings')
      ->set('node_type', $form_state->getValue('node_type'))
      ->save();
    $this->config('wrlc_alert_signup.settings')
      ->set('google_api_client', intval($form_state->getValue('google_api_client')))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
