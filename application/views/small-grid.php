<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jul 8, 2010
 * Time: 4:49:13 PM
 */
?>


<?php

if (!isset($columns))
    $columns = null;
$base = (isset($base)) ? $base : '';

function file_icon($file_type)
{
    if (file_exists('application/views/images/files/file_' . ltrim($file_type, '.') . '.png'))
    {
        return '<img class="padded" src="application/views/images/files/file_' . ltrim($file_type, '.') . '.png" alt=""/>';
    }
    else
    {
        return '<img class="padded" src="application/views/images/files/unknown.png" alt=""/>';
    }
}

?>

<ul class="small grid clearfix">
<?php if (is_array($data)): ?>
<?php foreach ($data as $title => $section): ?>

<?php if (count($data) > 1): ?>
    <li class="section-separator fancy">
        <span class="title"><?php echo $title; ?></span>
    </li>
<?php endif; ?>



<?php $section['page'] = (isset($section['page'])) ? $section['page'] : $section; ?>
<?php $count = 0; ?>
<?php foreach ($section['page'] as $item): ?>
<?php $count++; ?>
    <li class="cell <?php if ($count % 3 == 0) echo 'end-row'; ?>">
        <div class="icon">
            <a class="icon-link" href="index.php?a=files/view/<?php echo $item['id']; ?>">
                        <span class="">
                            <?php
                            if ($this->File->get_type($item['file_type']) == 'image')
                            {
                                $thumb = $CONFIG['uploads']['path'] . 'thumbs/' . $item['path'];
                                if (file_exists($thumb))
                                {
                                    $path = $item['path'];
                                    echo "<img class=thumb src='" . $this->redirect("files/download", true) . "/$path/true" . "'  alt='thumbnail' />";
                                }
                                else
                                {
                                    echo '<img class="thumb" src="application/views/images/files/unknown_pic.png"
                                     alt="thumbnail"/>';
                                }
                            }
                            else
                            {
                                echo file_icon($item['file_type']);
                            }
                            ?>
                        </span>
            </a>
        </div>
        <div class="cell-info">
            <p><a href="index.php?a=files/view/<?php echo $item['id']; ?>"
                  class="name"><?php echo $item['description']; ?></a></p>

            <p class="uploaded-by"><?php echo (isset($item['uploaded_by'])) ? "Uploaded by " . $item['uploaded_by'] : ""; ?></p>

            <p class="date-time"><?php echo date("F j, Y - g:i a", $item['created']); ?></p>
        </div>
    </li>

<?php endforeach; ?>

<?php endforeach; ?>

<?php else: ?>
    <div class="no-files alert fluid large">
        <h3>This Project is empty</h3>

    <?php if ($user['id'] == 0 || $CONFIG['uploads']['allow_client_uploads']): ?>
        <a class="small-button active"
           href="index.php?a=files/add/<?php echo $project['id']; ?>"><span><?php echo $lang["lang_add_a_file"]?></span></a>
        <a class="small-button active" href=""><span><?php echo $lang["lang_add_a_link"]?></span></a>
    <?php endif; ?>
    </div>

<?php endif; ?>
</ul>