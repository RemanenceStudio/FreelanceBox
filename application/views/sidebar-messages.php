<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jul 3, 2010
 * Time: 2:54:32 AM
 */
?>
<?php

$model = explode('Controller', get_class($this));
$model = strtolower(substr($model[0], 0, -1));

$messages = (isset($messages)) ?$messages : null;
?>
<div class="new-message clearfix">

    <form action="index.php?a=messages/add/<?php echo $model . '/' . $item_id; ?>" class="clearfix"
          method="post">
        <textarea rows="2" name="message" id="message"></textarea>

        <div class="small-button"><input type="submit" value="Post Message"></div>
    </form>
</div>

<?php if (isset($messages['page'])): ?>
<?php if (is_array($messages['page'])): ?>
<ul class="messages">
<?php foreach ($messages['page'] as $message): ?>
    <li class="message">
        <div class="message-body"><?php echo $message['message']; ?></div>
        <div class="message-author"><?php echo date("F j, Y - g:i a", $message['created']); ?></div>
    </li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<?php endif; ?>

<?php if (!is_array($messages['page'])): ?>
<h3 class="no-messages">No messages</h3>
<?php endif; ?>