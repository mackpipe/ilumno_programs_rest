<?php

namespace Drupal\ilumno_programs_rest\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "programs_rest_resource",
 *   label = @Translation("Programs rest resource"),
 *   uri_paths = {
 *     "canonical" = "/ilumno-module/data/{id}"
 *   }
 * )
 */
class ProgramsRestResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->logger = $container->get('logger.factory')->get('ilumno_programs_rest');
    $instance->currentUser = $container->get('current_user');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  /*public function __construct() {
    $this->connection = \Drupal::database();
  }*/

  /**
   * Responds to GET requests.
   *
   * @param string $payload
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get($payload) {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    $this->connection = \Drupal::database();
    $responseRecords = [];
    try {
      $query = $this->connection->select('programs', 'p');
      $query->fields('p', [
          'program_id',
          'program_name',
          'program_identifier',
          'program_code',
          'program_date',
          'program_type',
          'state',
      ]);
      if (is_numeric($payload) && $payload != "all") {
          $query->condition('program_id', $payload, "=");
      }

      $result = $query->execute();
      $records = $result->fetchAll();

      foreach ($records as $record) {
          $record->program_date = date('Y-m-d', $record->program_date);
          $responseRecords[] = (array) $record;
      }
    }
    catch (\Exception $e) {
        // Log the exception to watchdog.
        \Drupal::logger('ilumno_programs_rest')->error($e->getMessage());
    }

    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    return new ResourceResponse($responseRecords, 200);
  }

  /**
   * Responds to PATCH requests.
   *
   * @param string $payload
   *
   * @return \Drupal\rest\ModifiedResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function patch($payload,$data) {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
        throw new AccessDeniedHttpException();
    }

    return new ModifiedResourceResponse($data, 204);
  }

}
