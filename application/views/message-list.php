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
$model = explode('Controller', get_class($this));
$model = strtolower($model[0]);


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
        <div class="info-bar">
            <h1 class="title ostrich-medium tall"><?php echo ucfirst($tab) ?></h1>

        <?php if (is_array($object_actions)): ?>
            <ul class="sub-tabs">

            <?php foreach ($object_actions as $name => $action): ?>
                <li class="object-action"><a href="<?php echo $action; ?>"><span><?php echo $name; ?></span></a></li>
            <?php endforeach; ?>

            </ul>
        <?php endif; ?>

        </div>

        <ul class="tab_menu wrapper">

        <?php if ($user['group_id'] == 0): ?>
            <li class="<?php echo ($tab == 'clients') ? 'selected' : ''; echo ($tab == 'projects') ? 'no-div' : ''; ?>">
                <a href="index.php?a=clients/get/all"><span>Clients</span></a>
            </li>
        <?php endif; ?>

            <li class="<?php echo ($tab == 'projects') ? 'selected' : ''; echo ($tab == 'invoices') ? 'no-div' : ''; ?>">
                <a href="index.php?a=projects/get/all"><span><?php echo $lang["lang_projects"]?></span></a>
            </li>
            <li class="<?php echo ($tab == 'invoices') ? 'selected' : ''; echo ($tab == 'payments') ? 'no-div' : ''; ?>">
                <a href="index.php?a=invoices/get/all"><span><?php echo $lang["lang_invoices"]?></span></a>
            </li>
            <li class="<?php echo ($tab == 'payments') ? 'selected' : ''; ?>">
                <a href="index.php?a=payments/get/all"><span><?php echo $lang["lang_payments"]?></span></a>
            </li>

            <li class="<?php echo ($tab == 'messages') ? 'selected' : ''; ?> messages">
                <a href="index.php?a=messages/get/all"><span>&nbsp;</span></a>
            </li>


        </ul>
        <div class="inner">


            <table class="list <?php echo $list_class; ?>">

                <tr class="table-header">
                <?php if (is_array($columns)): ?>

                <?php foreach ($columns as $column): ?>

                <?php $column = parse_class($column); ?>

                    <th class="<?php echo $column['class']; ?>"><?php echo $column['column']; ?></th>

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

                <?php $key = parse_class($key); ?>

                    <td class="<?php echo $key['class']; ?>">
                        <a class="cell-link"
                           href="<?php echo $this->redirect($item['reference_object'] . 's/view/' . $item['reference_id'], true); ?>"><?php  echo (!empty($item[$key['column']])) ? $item[$key['column']] : '&nbsp;'; ?></a>
                    </td>

                <?php endforeach; ?>

                <?php if (is_array($actions)): ?>
                <?php foreach ($actions as $name => $action): ?>

                <?php $name = parse_class($name); ?>
                    <td class="action">
                        <a class="small-button <?php if ($name['column'] == 'Delete') echo 'danger'; echo (isset($name['class'])) ? ' ' . $name['class'] : ''; ?>"
                           href="<?php echo $action . $item['id']; ?>"><span><?php echo $name['column'] ?></span></a>
                    </td>

                <?php endforeach; ?>
                <?php endif; ?>
                </tr>

            <?php endforeach; ?>

            <?php endif; ?>
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