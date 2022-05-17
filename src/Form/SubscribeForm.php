<?php

namespace Drupal\wrlc_alert_signup\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a WRLC Alert Signup form.
 */
class SubscribeForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wrlc_alert_signup_subscribe';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get the current user's email address
    $account = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $email = $account->getEmail();

    $form['signup'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Subscribe to WRLC Alerts'),
      '#required' => TRUE,
      '#description' => 'Subscribe to receive alerts in your inbox at ' . $email . '.',
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Subscribe'),
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
    $member = new \Google_Service_Directory_Member();
    $member->email = $email;
    $member->role = 'MEMBER';

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

      // Insert user email in group
      $config = \Drupal::config('wrlc_alert_signup.settings');
      $subscribe = $object['directory']->members->insert($config->get('gg_id'), $member);
    }

    catch (Exception $e) {
      // ksm($e);
      exit();
    }

    // Provide a confirmation message
    $this->messenger()->addStatus($this->t('You have been subscribed to WRLC alerts.'));

  }
}
