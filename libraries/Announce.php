<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * A class for managing messages to be displayed to the user.
 * Maintains one or more arrays of messages in the session, with methods
 * to add messages and retrieve arrays of messages.
 * 
 * @author Jeremy Elliot
 * @version 0.0.1
 */
class Announce {

  private $_session_handler;
  private $_message_types = array();
  private $_session_id;

  /**
   * Initialise this object.
   * Takes an array of parameters:
   * - CI_Session &$session_handler reference to the session handler, eg. the standard CI Session class or your superior replacement
   * - array $message_types array of message types, eg. array('success', 'error)
   * - string $session_id 
   * @param array parameter array
   */
  public function __construct($params)
  {
    // initialise this object
    $this->_session_handler = & $params[0];
    $this->_message_types = (isset($params[1]))
        ? $params[1]
        : array('message');
    $this->_session_id = (isset($params[2]))
        ? $params[2]
        : 'user_message_store';
    // if there are no messages in session, initialise session
    $messages = $this->_session_get();
    if (empty($messages))
    {
      $this->clear();
    }
  }

  /**
   * Adds a message to the a list.
   * 
   * The default message type is 'message'.
   * The optional parameter $data may be a primitive value or an array.
   * If the $data parameter is present, $message must be a 
   * {@link http://www.php.net/manual/en/function.sprintf.php printf()}-style
   * template string that includes formatting for the value(s) in $data.
   * The $message formatting string is applied to the $data value(s) and 
   * the resulting message string is added to the list.
   * 
   * Eg. $this->messages->add('success', '%d files uploaded (%.2f kB)', array($num_files, $uploaded_size));
   * 
   * @param string $message message to be displayed
   * @param string $message_type one of the message types specified in the constructor params
   * @param mixed $data optional data to be included in the message
   * @return Announce $this
   */
  public function add($message_type, $message, $data = NULL)
  {
    // if $message is an array of messages, add them all
    if (is_array($message))
    {
      foreach ($message as $msg)
      {
        $this->add($message_type, $msg);
      }
      return $this;
    }
    // single message, add it to the appropriate array
    // if there is a $data array, use the message as a sprintf() template
    if (!empty($data))
    {
      if (is_array($data))
      {
        $message = vsprintf($message, $data);
      }
      else
      {
        $message = sprintf($message, $data);
      }
    }
    $messages = $this->_session_get();
    // if $message_type is a new type, add it to the message_types array
    if (!in_array($message_type, $this->_message_types))
    {
      $this->_message_types[] = $message_type;
      $messages[$message_type] = array();
    }
    $messages[$message_type][$message] = $message_type;
    $this->_session_set($messages);
    return $this;
  }

  /**
   * Removes messages from the list.
   * 
   * If $message_type is given, clears only messages of that type
   * If $message_type is not given, clears all messages
   * @param string $message_type type of messages to remove
   * @return Announce $this 
   */
  public function clear($message_type = NULL)
  {
    if (empty($message_type))
    {
      foreach ($this->_message_types as $type)
      {
        $this->clear($type);
      }
    }
    else
    {
      $messages = $this->_session_get();
      $messages[$message_type] = array();
      $this->_session_set($messages);
    }
    return $this;
  }

  /**
   * Returns the number of messages stored in the session.
   * If $message_type is given, returns the number of messages
   * of the that type, otherwise returns total number of messages.
   * @param string $message_type
   * @return int number of messages 
   */
  public function count($message_type = NULL)
  {
    $messages = $this->_session_get();
    if (empty($message_type))
    {
      $msg_count = count($messages, COUNT_RECURSIVE);
    }
    else
    {
      $msg_count = count($this->peek($message_type));
    }
    return $msg_count;
  }

  /**
   * Returns an array of messages to be displayed
   * 
   * If $message_type is given, returns an array of messages of that type
   * If $message_type is not given, returns an array of all messages, 
   * ordered by type, in the same order as in the constructor param $message_types
   * Clears the list of messages.
   * @param string $message_type type of messages to get
   * @return array messages to be displayed
   */
  public function get($message_type = NULL)
  {
    $result = $this->peek($message_type);
    $this->clear($message_type);
    return $result;
  }

  /**
   * Sames as get(), but does not remove messages from the list
   * 
   * @param string $message_type type of messages to peek at
   * @return array messages 
   */
  public function peek($message_type = NULL)
  {
    $result = array();
    if (empty($message_type))
    {
      // get all messages, ordered by type, 
      // same order as given in constructor param $message_types
      foreach ($this->_message_types as $type)
      {
        $result = array_merge($result, $this->peek($type));
      }
    }
    else
    {
      $messages = $this->_session_get();
      if (isset($messages[$message_type]))
      {
        $result = $messages[$message_type];
      }
    }
    return $result;
  }

  /**
   * Returns TRUE if there are messages available, otherwise FALSE.
   * 
   * If $message_type is given, returns TRUE if there are messages
   * of that type, otherwise FALSE.
   * @param string $message_type type of message to check for
   * @return boolean TRUE if messages exist
   */
  public function has_messages($message_type = NULL)
  {
    return ($this->count($message_type) > 0);
  }

  /**
   * Add a message using the method name as message_type 
   * Eg. $this->messages->success('the operation was successful');
   * @param string|array $message message or array of messages
   * @return Announce $this
   */
  public function __call($name, $arguments)
  {
    $message_type = $name;
    $message = $arguments[0];
    $data = (isset($arguments[1]))
        ? $arguments[1]
        : NULL;
    return $this->add($message_type, $message, $data);
  }

  private function _session_set($data)
  {
    return $this->_session_handler->set_userdata($this->_session_id, $data);
  }

  private function _session_get()
  {
    return $this->_session_handler->userdata($this->_session_id);
  }

}

/* End of file Announce.php */
/* Location: /sparks/announce/0.0.1/libraries/Announce.php */
