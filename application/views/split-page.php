<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jul 3, 2010
 * Time: 2:31:04 AM
 */
?>

<?php
$breadcrumbs = (isset($breadcrumbs)) ? $breadcrumbs : false;
$main_content = (isset($main_content)) ? $main_content : '';
$sidebar_content = (isset($sidebar_content)) ? $sidebar_content : '';
$object_actions = (isset($object_actions)) ? $object_actions : null;
 
function parse_class($name)
{
    $class = false;
    if (preg_match("/(.*?)\[(.*?)\]/", $name, $match))
    {
        $name = $match[1];
        $class = $match[2];
    }

    return array('name' => $name, 'class' => $class);
}

?>

<div id="page-content-outer">
    <div id="page-content" class="wrapper content admin">
        <div class="info-bar">
        <?php if ($breadcrumbs): ?>
            <h5 class="breadcrumbs secondary"><a
                    href="<?php echo $breadcrumbs[1][1]; ?>"><?php echo $breadcrumbs[1][0];?></a></h5>

            <h1 class="title breadcrumbs primary"><a
                    href="<?php echo $breadcrumbs[0][1]; ?>"><?php echo $breadcrumbs[0][0]; ?></a></h1>
        <?php if (isset($breadcrumbs[2])):?>
            <h5 class="breadcrumbs tertiary"><a
                    href="<?php echo $breadcrumbs[2][1]; ?>">(<?php echo $breadcrumbs[2][0];?>)</a></h5>
        <?php endif; ?>

        <?php endif; ?>

        <?php if (is_array($object_actions)): ?>
            <ul class="sub-tabs">

            <?php foreach ($object_actions as $name => $action): ?>
            <?php $name = parse_class($name); ?>
                <li class="object-action <?php echo $name['class']; ?>"><a
                        href="<?php echo $action; ?>"><span><?php echo $name['name']; ?></span></a></li>
            <?php endforeach; ?>

            </ul>
        <?php endif; ?>
        </div>

        <div class="inner-split clearfix">
            <div class="main-content clearfix">

            <?php if (file_exists($main_content)): ?>
            <?php include($main_content); ?>
            <?php endif;?>

                <div id="file-actions">
                <?php if (isset($admin_actions) && is_array($admin_actions)): ?>
                <?php if ($user['group_id'] == 0): ?>
                <?php foreach ($admin_actions as $action): ?>
                    <a class="small-button <?php echo (isset($action['class'])) ? $action['class'] : ''; ?>"
                       href="<?php echo $action['link']?>"><span><?php echo $action['name']; ?></span></a>

                <?php endforeach; ?>
                <?php endif; ?>
                <?php endif; ?>


                <?php if (isset($actions) && is_array($actions)): ?>
                <?php foreach ($actions as $action): ?>
                    <a class="small-button <?php echo (isset($action['class'])) ? $action['class'] : ''; ?>"
                       href="<?php echo $action['link']?>"><span><?php echo $action['name']; ?></span></a>
                <?php endforeach; ?>
                <?php endif; ?>


                </div>
            </div>

            <div class="sidebar-content clearfix">

            <?php if (file_exists($sidebar_content)): ?>
            <?php include($sidebar_content); ?>
            <?php endif;?>

            </div>


        </div>

    </div>

</div>