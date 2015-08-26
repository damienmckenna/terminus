<?php

namespace Terminus\Models\Collections;

use \TerminusCommand;
use \stdClass;

abstract class TerminusCollection {
  protected $models = array();

  /**
   * Instantiates the collection, sets param members as properties
   *
   * @param [array] $options To be set to $this->key
   * @return [TerminusCollection] $this
   */
  public function __construct($options = array()) {
    foreach ($options as $key => $option) {
      $this->$key = $option;
    }
  }

  /**
   * Retrieves all models
   *
   * @return [array] $models
   */
  public function all() {
    $models = array_values($this->getMembers());
    return $models;
  }

  /**
   * Fetches model data from API and instantiates its model instances
   *
   * @return [void]
   */
  public function fetch() {
    $results = $this->getCollectionData();
    $data    = $this->objectify($results['data']);

    foreach (get_object_vars($data) as $id => $model_data) {
      if (!isset($model_data->id)) {
        $model_data->id = $id;
      }
      $this->add($model_data);
    }
  }

  /**
   * Retrieves the model of the given ID
   *
   * @param [string] $id ID of desired model instance
   * @return [TerminusModel] $this->models[$id]
   */
  public function get($id) {
    $models = $this->getMembers();
    if (isset($models[$id])) {
      return $models[$id];
    }
    return null;
  }

  /**
   * List Model IDs
   *
   * @return [array] $ids Array of all model IDs
   */
  public function ids() {
    $models = $this->getMembers();
    $ids    = array_keys($models);
    return $ids;
  }

  /**
   * Adds a model to this collection
   *
   * @param [stdClass] $model_data Data to feed into attributes of new model
   * @param [array]    $options    Data to make properties of the new model
   * @return [TerminusModel] $model
   */
  protected function add($model_data, $options = array()) {
    $model   = $this->getMemberName();
    $owner   = $this->getOwnerName();
    $options = array_merge(array('id' => $model_data->id), $options);

    if ($owner) {
      $options[$owner] = $this->$owner;
    }
    $this->models[$model_data->id] = new $model(
      $model_data,
      $options
    );
  }

  /**
   * Gives the name of this class
   *
   * @return [string] $class_name
   */
  protected function getClassName() {
    $class_name = get_class($this);
    return $class_name;
  }
  
  /**
   * Retrieves collection data from the API
   *
   * @return [array] $results
   */
  protected function getCollectionData() {
    $options = array_merge(
      array('options' => array('method' => 'get')),
      $this->getFetchArgs()
    );
    $results = TerminusCommand::simple_request(
      $this->getFetchUrl(),
      $options
    );
    return $results;
  }

  /**
   * Give necessary args for collection data fetching
   *
   * @return [array]
   */
  protected function getFetchArgs() {
    return array();
  }

  /**
   * Give the URL for collection data fetching
   *
   * @return [string] $url URL to use in fetch query
   */
  abstract protected function getFetchUrl();

  /**
   * Names the model-owner of this collection, false if DNE
   *
   * @return [string|boolean] $owner_name
   */
  protected function getOwnerName() {
    return false;
  }

  /**
   * Returns name of the model collected by this collection
   *
   * @return [string] $model_name Name of model
   */
  protected function getMemberName() {
    $name_array = explode('\\', get_class($this));
    $model_name = $name_array[0]
      . '\\' . $name_array[1]
      . '\\' . substr(array_pop($name_array), 0, -1);
    return $model_name;
  }

  /**
   * Retrieves all members of this collection
   *
   * @return [array] $this->models
   */
  protected function getMembers() {
    if (empty($this->models)) {
      $this->fetch();
    }
    return $this->models;
  }

  /**
   * Turns an associative array into a stdClass object
   *
   * @param [array] $array Array to turn into object
   * @return [stdClass] $object
   */
  private function objectify($array = array()) {
    if (is_array($array)) {
      $object = new stdClass();
      foreach ($array as $key => $value) {
        $object->$key = $value;
      }
      return $object;
    }
    return $array;
  }

}