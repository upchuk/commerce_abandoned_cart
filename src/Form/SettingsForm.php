<?php

namespace Drupal\commerce_abandoned_cart\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Commerce Abandoned Cart settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a SettingsForm.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($config_factory);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_abandoned_cart_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['commerce_abandoned_cart.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $messages = $this->entityTypeManager->getStorage('email_message')->loadMultiple();
    $options = [];
    foreach ($messages as $message) {
      $options[$message->id()] = $message->label();
    }

    $form['email_message_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Email message'),
      '#options' => $options,
      '#description' => $this->t('The email message to use.'),
      '#empty_option' => $this->t('Select a message'),
      '#required' => TRUE,
      '#default_value' => $this->config('commerce_abandoned_cart.settings')->get('email_message_id'),
    ];

    $form['age'] = [
      '#type' => 'number',
      '#title' => $this->t('Age'),
      '#description' => $this->t('The number of days the order needs to be untouched before firing the email notification.'),
      '#default_value' => $this->config('commerce_abandoned_cart.settings')->get('age'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('commerce_abandoned_cart.settings')
      ->set('email_message_id', $form_state->getValue('email_message_id'))
      ->set('age', $form_state->getValue('age'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
