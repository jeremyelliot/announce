Announce
============

A CodeIgniter Spark for managing messages to be displayed to the user.

It maintains one or more arrays of messages in the session, with methods
to add messages and retrieve arrays of messages.

Announce uses the `CI_Session` class or your superior replacement that 
also provides methods `userdata($id)` and `set_userdata($id, $data)`.

Example code:
-------------

### Load the library

	$this->load->spark('announce/0.0.1');
	$params = array(&$this->session, array('success', 'error'));
	$this->load->library('Announce', $params, 'messages');
	

### Adding messages

(perhaps in one of your controller methods)

	if ($thing_was_successful) 
	{
		$this->messages->add('success', 'Your thing was successful');
	}
	else
	{
		$this->messages->add('error', 'Your thing failed. Sorry.');
	}

or, the same thing using 'shortcut methods'

	if ($thing_was_successful) 
	{
		$this->messages->success('Your thing was successful');
	}
	else
	{
		$this->messages->error('Your thing failed. Sorry');
	}

In the code above, the message type ('success' or 'error')
is used as the name of the method for adding a message to the 
'success' or 'error' array.


#### More information in your messages

The `add` method and the shortcut methods all take an optional
extra `$data` parameter. 
If the `$data` parameter is present, `$message` must be a `printf()`-style 
template string that includes formatting for the value(s) in `$data`.

For example:

	//	Assume $num_files == 3, $upload_size == 34.561
	//	and $msg_upload_success = '%d files uploaded (%.1f kB)'

	$this->messages->add('success', $msg_upload_success, array($num_files, $upload_size));

	//	or

	$this->messages->success($msg_upload_success, array($num_files, $upload_size));

	//	stores the message as '3 files uploaded (34.5 kB)'

	//  $data can single value instead of an array.
	//	Assume $msg_delete_success = '%d files were deleted'

	$this->messages->success($msg_delete_success, $num_files);


### Getting the messages

Get all types of message:

	$messages = $this->messages->get();
	...
	// then, in your view
	...
	<?php if (!empty($messages)): ?>
		<ul>
		<?php foreach ($messages as $message => $msg_type): ?>
			<li class="<?php echo $msg_type; ?>">
				<?php echo $message; ?>
			</li>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>

or, just one type of message:

	$success_messages = $this->messages->get('success');
	...
	// then, in your view...
	
	<?php if (!empty($success_messages)): ?>
		<ul class="success">
		<?php foreach ($success_messages as $message => $msg_type): ?>
			<li><?php echo $message; ?></li>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>


