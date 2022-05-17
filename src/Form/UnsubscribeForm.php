<?php

namespace Drupal\wrlc_alert_signup\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a WRLC Alert Unsubscribe form.
 */
class UnsubscribeForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wrlc_alert_signup_unsubscribe';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get the current user's email address
    $account = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $email = $account->getEmail();

    $form['message'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Unsubscribe from WRLC Alerts'),
      '#required' => TRUE,
      '#description' => 'You are currently subscribed as ' . $email . '.',
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Unsubscribe'),
    ];

    return $form;
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
    // Get the current user's email address
    $account = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $email = $account->getEmail();

    // Load the Google account
    // (id should match id of api client in Drupal admin settings)
    $google_api_client = \Drupal::entityTypeManager()->getStorage('google_api_client')->load(1);

    // Get the service.
    $googleService = \Drupal::service('google_api_client.client');
    // Apply the account to the service
    $googleService->setGoogleApiClient($google_api_client);

    // Try your API Operations.
    try {
      // Fetch service objects.
      $object = $googleService->getServiceObjects();
      $config = \Drupal::config('wrlc_alert_signup.settings');
      $unsubscribe = $object['directory']->members->delete($config->get('gg_id'), $email);
    }
    catch (Exception $e) {
      exit();
    }

    $this->messenger()->addStatus($this->t('You have been unsubscribed.'));
  }

}
