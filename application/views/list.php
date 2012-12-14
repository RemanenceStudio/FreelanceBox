<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jun 15, 2010
 * Time: 5:53:36 PM
 */
?>
<?php

$columns = (isset($columns)) ? $columns : null;
$actions = (isset($actions)) ? $actions : null;
$object_actions = (isset($object_actions)) ? $object_actions : null;
$list_class = (isset($list_class)) ? $list_class : null;

function parse_class($column)
{
    $class = false;
    if (preg_match("/(.*?)\[(.*?)\]/", $column, $match))
    {
        $column = $match[1];
        $class = $match[2];
    }

    return array('column' => $column, 'class' => $class);
}
?>

<div id="page-content-outer">
  <div id="page-content" class="wrapper content admin">
    <div class="info-bar" style="margin:38px 0;">
      <h1 class="title ostrich-medium tall"><?php echo $lang["lang_" . $tab]; ?></h1>
      <?php if (is_array($object_actions)): ?>
      
      <ul class="sub-tabs">
        <?php foreach ($object_actions as $name => $action): ?>
        <?php $name = parse_class($name); ?>
        <li class="object-action <?php echo (isset($name['class'])) ? $name['class'] : ''; ?>">
        	<a class="button right" href="<?php echo $action; ?>"><span><?php echo $name['column']; ?></span></a>
        </li>
        <?php endforeach; ?>
      </ul>  	

      <?php endif; ?>
    </div>
    <div class="row">
        <ul class="button-group radius ">
          <?php if ($user['group_id'] == 0): ?>
          <li class="<?php echo ($tab == 'clients') ? 'selected' : ''; echo ($tab == 'projects') ? 'no-div' : ''; ?>"> <a class="button" href="index.php?a=clients/get/all"><span><?php echo $lang["lang_clients"] ?></span></a> </li>
          <?php endif; ?>
          <li class="<?php echo ($tab == 'projects') ? 'selected' : ''; echo ($tab == 'invoices') ? 'no-div' : ''; ?>"> <a class="button " href="index.php?a=projects/get/all"><span><?php echo $lang["lang_projects"] ?></span></a> </li>
          <li class="<?php echo ($tab == 'invoices') ? 'selected' : ''; echo ($tab == 'payments') ? 'no-div' : ''; ?>"> <a class="button " href="index.php?a=invoices/get/all"><span><?php echo $lang["lang_invoices"] ?></span></a> </li>
          <li class="<?php echo ($tab == 'calendar') ? 'selected' : ''; ?>"> <a class="button" href="calendar.php"><span><?php echo $lang["lang_calendar"] ?></span></a> </li>
          <li class="<?php echo ($tab == 'timesheet') ? 'selected' : ''; ?>"> <a class="button" href="timesheet.php"><span><?php echo $lang["lang_timesheet"] ?></span></a> </li>
          <li class="<?php echo ($tab == 'payments') ? 'selected' : ''; ?>"> <a class="button" href="index.php?a=payments/get/all"><span><?php echo $lang["lang_payments"] ?></span></a> </li>
          <li class="<?php echo ($tab == 'messages') ? 'selected' : ''; ?> messages"> <a class="button" href="index.php?a=messages/get/all"><span>&nbsp;</span></a> </li>
          </ul>
    </div>
    <div class="row">
      <table class="list <?php echo $list_class; ?>" style="width:100%;">
        <thead class="table-header text-left">
          <?php if (is_array($columns)): ?>
          <?php foreach ($columns as $column): ?>
          <?php $column = parse_class($column); ?>
          <th class="<?php echo $column['class']; ?>"><?php echo $lang["lang_" . str_replace(' ', '_', strtolower($column['column']))]; ?></th>
          <?php endforeach; ?>
          <?php endif; ?>
          <?php if (is_array($actions)): ?>
          <?php foreach ($actions as $action): ?>
          <th class="action"></th>
          <?php endforeach; ?>
          <?php endif; ?>
        </thead>
        <tbody>
        <?php if (is_array($data['page'])): ?>
        <?php foreach ($data['page'] as $item): ?>
        <tr>
          <?php foreach ($columns as $key => $column): ?>
          <?php $key = parse_class($key); ?>
          <td class="<?php echo $key['class']; ?>"><a class="cell-link"
                           href="<?php echo $details_link . $item[$details_id_field]; ?>">
            <?php  echo (!empty($item[$key['column']])) ? $item[$key['column']] : '&nbsp;'; ?>
            </a></td>
          <?php endforeach; ?>
          <?php if (is_array($actions)): ?>
          <?php foreach ($actions as $name => $action): ?>
          <?php $name = parse_class($name); ?>
          <td class="action"><a class="small-button <?php if ($name['column'] == 'Delete') echo 'danger'; echo (isset($name['class'])) ? ' ' . $name['class'] : ''; ?>"
                           href="<?php echo $action . $item['id']; ?>"><span><?php echo $name['column'] ?></span></a></td>
          <?php endforeach; ?>
          <?php endif; ?>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="footer">
      <?php
                    $this->loadLibrary('pagination.class');
        echo pagination_links($data['current_page'], $data['total_pages'], $base);
        ?>
    </div>
  </div>
</div>
