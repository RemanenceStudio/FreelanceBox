<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jun 27, 2010
 * Time: 1:19:10 AM
 */
?>


<?php

$type = (isset($type)) ? $type : '';

function av_player($url)
{
    global $CONFIG;
    $url = $CONFIG['base_url'].
            Controller::redirect('files/download', true) . "/" . $url;
    $player = "
    <script src='plugins/flowplayer/flowplayer-3.2.2.min.js'></script>
    <script type='text/javascript' language='JavaScript'>
    flowplayer('av-player', 'plugins/flowplayer/flowplayer-3.2.2.swf', {
    clip: {
        url: '$url',
        autoPlay: false
   //    provider: 'audio'
    },
    plugins: {
		controls: {
			fullscreen: false,
			height: 30,
			autoHide: false
		}

	}
    });
    </script>";

    return $player;
}

?>

<?php if ($type != 'other' && $type != 'website' && $type != 'audio'): ?>
<div class="top-actions">
    <h2 class="description"><?php echo $file['description']; ?></h2>

    <div class="meta">
        <p class="uploaded-by"><?php echo (isset($file['uploaded_by'])) ? "Uploaded by " . $file['uploaded_by'] : ""; ?></p>

        <p class="date-time"><?php echo date("F j, Y - g:i a", $file['created']); ?></p>
    </div>
</div>
<?php endif; ?>

<?php if ($type == 'video'): ?>
<div id="av-player" class="<?php echo $type; ?> clearfix"></div>
<div class="clear">&nbsp;</div>
<?php endif; ?>

<?php if ($type == 'image'): ?>
<img id="image-file" src="<?php echo $this->redirect('files/download', true) . "/" .
        $file['id']; ?>"/>
<?php endif; ?>

<?php if ($type == 'other' || $type == 'website' || $type == 'audio'): ?>
<div id="file-container">
    <div class="file-inner">

    <?php if (file_exists('application/views/images/files/file_' . ltrim($file['file_type'], '.') . '.png')): ?>
        <img class="file-icon"
             src="application/views/images/files/file_<?php echo ltrim($file['file_type'], '.'); ?>.png"
             alt=""/>
    <?php else: ?>
        <img class="file-icon" src="application/views/images/files/unknown.png" alt=""/>
    <?php endif; ?>
        <div id="file-details">
            <h1 class="file-description"><?php echo $file['description']; ?></h1>

        <p class="file-name">
            <span>
            <?php echo ($file['file_type'] != 'www') ? 'File name' : 'URL'; ?>:
            </span>

        <?php if ($file['file_type'] != 'www'): ?>

        <?php echo $file['path']; ?></p>

        <?php else: ?>
            <a href="<?php echo $file['path']; ?>"><?php echo $file['path']; ?></a></p>
        <?php endif; ?>

        <?php if ($file['file_type'] != 'www'): ?>
            <p class="file-uploaded-by"><span><?php echo $lang['lang_uploaded_by']; ?></span> <?php echo $file['uploaded_by']; ?>
            </p>
        <?php endif; ?>

            <p class="file-upload-date">
                <span><?php echo $lang['lang_created']; ?></span>
            <?php echo date("F j, Y - g:i a", $file['created']); ?>
            </p>
        </div>
    </div>
</div>

<?php endif; ?>


<?php

if ($type == 'video')
    echo av_player($file['id']);

?>