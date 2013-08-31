<?php

/**
 * Cakemenu component responsible for fetching the menu nodes from the DB or Cache.
 * @author Nik chankov <contact@chankov.net>
 * @date 23.04.2010
 */
App::uses('Component', 'Controller');
App::uses('Menu', 'Cakemenu.Model');

class CakemenuComponent extends Component {

  public $components = array('Session');

  /**
   * if it's set to true the component will store the menu in the cache
   */
  public $cache;

  /**
   * Controller reference
   *
   * @var Controller
   */
  protected $_controller = null;

  /**
   * If set to true it will use Authake. If it's set to false, the the menu
   * will be generated without any restriction
   * the auth component need to have 2 functions at least: isAllowed($path) and getUserId()
   * The first one check the path provided from the menu node
   * The second one is used to fetch the userID of the logged user
   * Use Authake for reference.
   */
  public $auth = null;

  public function __construct(ComponentCollection $collection, $settings = array()) {
    $this->_controller = $collection->getController();
    parent::__construct($collection, $settings);
  }

  /**
   * Initialize callback.
   * If automatically disabled, tell component collection about the state.
   *
   * @return bool
   * */
  public function initialize(Controller $controller) {
    
  }

  /**
   * Component Startup
   *
   * @return bool
   * */
  public function startup(Controller $controller) {
    
  }

  public function shutdown(Controller $controller) {
    
  }

  /**
   * beforeRender callback
   *
   * Calls beforeRender on all the panels and set the aggregate to the controller.
   *
   * @return void
   * */
  public function beforeRender(Controller $controller) {
    
  }

  /**
   * function which return the nodes of the menu
   * @param array $options options similar to Model->find()
   * @param array $auth Auth instance responsible for filtering of the nodes depending from the priviledges of the logged user.
   * @param string $cache if set to false it won't cache the menu, otherwise it's used to set the name of the cache file name
   * @return array array of allowed nodes
   */
  public function nodes($options = array(), &$auth=null, $cache='menu') {
    $this->cache = $cache; //cache setting
    $this->auth = $auth; //auth instance

    $nodes = null;
    if ($this->cache != false && Configure::read('Cache.disable') != true) { //Use cache or not
      $nodes = $this->_useCache($options);
    } else {
      $nodes = $this->_fetch($options);
    }
    if ($this->auth != null) { //Filter if Auth is used
      $nodes = $this->_filter($nodes);
    }
    return $nodes;
  }

  /**
   * Function used to store the fetched data into cache if the cache is empty
   * read the data from the DB and store it in the cache.
   * @param array $options
   * @return array of nodes
   */
  private function _useCache($options) {
    $determinator = '';
    if ($this->auth != null) {
      if ($this->auth->getUserId() != null) {
        $determinator = '_' . $this->auth->getUserId();
      }
    }
    if ($this->cache != false) {
      $nodes = $this->_fetch($options);
      Cache::write($this->cache . $determinator, $nodes);
    }
    $data = Cache::read($this->cache . $determinator);
    return $data;
  }

  /**
   * Function which actially fetch the data from the database
   * @param object $options
   * @return nested array of menu nodes.
   */
  private function _fetch($options = array()) {
    $menu = new Menu();
    if (isset($options['subtree'])) {
      $parent = true;
      if (isset($options['subtree']['parent'])) {
        $parent = $options['subtree']['parent'];
        unset($options['subtree']['parent']);
      }
      $subtree = $menu->find('first', array('conditions' => $options['subtree']));
      if ($subtree != false) {
        if ($parent == true) {
          $conditions = array(
              'Menu.lft >=' => $subtree['Menu']['lft'],
              'Menu.rght <=' => $subtree['Menu']['rght']
          );
        } else {
          $conditions = array(
              'Menu.lft >' => $subtree['Menu']['lft'],
              'Menu.rght <' => $subtree['Menu']['rght']
          );
        }

        if (isset($options['conditions'])) {
          $options['conditions'] = am($options['conditions'], $conditions);
        } else {
          $options['conditions'] = $conditions;
        }
      }
      unset($options['subtree']);
    }
    $nodes = $menu->find('threaded', am(array('order' => 'Menu.lft ASC'), $options));
    return $nodes;
  }

  /**
   * Function which checks the nodes of the menu against the permissions
   * if the Auth is used and user is not logged in, all menu nodes will be
   * hidden.
   * @param array $nodes nodes which are fetched from the database.
   * @return array filtered array of menu nodes
   */
  private function _filter($nodes) {
    if ($this->auth != false) {
      $nodes = $this->_checkAllowed($nodes);
    }
    return $nodes;
  }

  /**
   * Function which check all nodes against the authentication priviledges
   * @param array $nodes recursive array of all nodes
   * @return array filtered array of nodes
   */
  private function _checkAllowed($nodes) {
    foreach ($nodes as $key => $value) {
      //parse the link
      $link = '';
      if ($value['Menu']['link'] != '') {
        $link = $value['Menu']['link'];
        //Try to evaluate the link (if starts with array)
        if (eregi('^array', $link)) {
          eval("\$parse = " . $link . ";");
          if (is_array($parse)) {
            $link = $parse;
          }
          $link = '/' . implode('/', $link);
          //fix for plugin
          $link = str_replace('//', '/', $link);
        }
      }
      //Check children
      if (isset($value['children']) && count($value['children']) > 0) {
        $nodes[$key]['children'] = $this->_checkAllowed($value['children']);
      }

      if (!$this->auth->isAllowed($link)) { //no rights
        if (count($nodes[$key]['children']) > 0) { //node have children with rights
          $nodes[$key]['Menu']['link'] = null;
        } else {
          unset($nodes[$key]); //remove the node if no children and not allowed
        }
      } else {
        if ($nodes[$key]['Menu']['link'] == '' && count($nodes[$key]['children']) == 0) {
          unset($nodes[$key]); //remove the node if no children and not allowed
        }
      }
    }
    return $nodes;
  }

}

?>
