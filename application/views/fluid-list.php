<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jul 2, 2010
 * Time: 4:09:48 AM
 */
?>


<?php

$columns = (isset($columns)) ? $columns : null;
$actions = (isset($actions)) ? $actions : null;


?>


<table class="list" style="width:100%;">

    <tr class="table-header">
    <?php if (is_array($columns)): ?>

    <?php foreach ($columns as $column): ?>

        <th class="text-left" style="padding-left:11px;"><?php echo $column; ?></th>

    <?php endforeach; ?>

    <?php endif; ?>

    <?php if (is_array($actions)): ?>
    <?php foreach ($actions as $action): ?>
        <th class="action"></th>
    <?php endforeach; ?>
    <?php endif; ?>
    </tr>


<?php if (is_array($data['page'])): ?>

<?php foreach ($data['page'] as $item): ?>

    <tr>
    <?php foreach ($columns as $key => $column): ?>

        <td>
            <a class="cell-link"
               href="<?php echo $details_link . $item[$details_id_field]; ?>"><?php  echo (!empty($item[$key])) ? $item[$key] : '&nbsp;'; ?></a>
        </td>

    <?php endforeach; ?>

    <?php if (is_array($actions)): ?>
    <?php foreach ($actions as $name => $action): ?>

        <td class="action">
            <a class="small-button <?php if ($name == 'Delete') echo 'danger'; ?>"
               href="<?php echo $action . $item['id']; ?>"><span><?php echo $name ?></span></a>
        </td>

    <?php endforeach; ?>
    <?php endif; ?>
    </tr>

<?php endforeach; ?>

<?php endif; ?>
</table>

