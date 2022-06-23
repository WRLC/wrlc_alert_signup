<?php

namespace Drupal\wrlc_alert_signup\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\facets\Exception\Exception;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "wrlc_alert_signup_form",
 *   admin_label = @Translation("WRLC Alerts subscription"),
 *   category = @Translation("WRLC Alerts")
 * )
 */
class SignupBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $channel = 'wrlc_alert_signup';

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
    // Fetch service objects.
    try {
      $object = $googleService->getServiceObjects();
    }
    catch (Exception $e) {
      // Error logging function in wrlc_alert_signup.module
      wrlc_alert_signup_log_error($e, $channel);
      exit();
    }

    // Check if current user is member of alerts group
    // (First param should be unique id of alerts group)
    $config = \Drupal::config('wrlc_alert_signup.settings');

    try {
      $membership = $object['directory']->members->hasMember($config->get('gg_id'), $email);
    }
    catch (Exception $e) {
      wrlc_alert_signup_log_error($e, $channel);
      exit();
    }

    // If user is not group member, render subscribe form
    if (!$membership->isMember) {
      return \Drupal::formBuilder()->getForm('Drupal\wrlc_alert_signup\Form\SubscribeForm');
    }

    // If user is a group member, render unsubscribe form
    else {
      return \Drupal::formBuilder()->getForm('Drupal\wrlc_alert_signup\Form\UnsubscribeForm');
    }
  }
}
