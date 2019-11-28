<?php
    $document_url = '#';
    $detail_page = (isset($resource_id) ? true: false);
?>

<div class="row-fluid">
    <h2 class="h2-loop-tit">
        <?php echo $resource->title ?>
    </h2>
</div>

<?php if ($resource->unit): ?>
    <div class="row-fluid">
        <?php foreach ( $resource->unit as $unit ):
            echo $unit . '<br/>';
         endforeach; ?>
    </div>
<?php endif; ?>

<div class="row-fluid marginbottom15" >
    <?php echo $resource->cooperative_center_code; ?>
</div>

<?php if ($resource->address): ?>
    <div class="row-fluid marginbottom15">
        <?php echo $resource->address[0]; ?><br/>
        <?php echo $resource->city . ' - ' . $resource->state[0] . '- ' . get_lang_value($resource->country, $site_language); ?>
    </div>
<?php endif; ?>

<?php if ($resource->contact): ?>
    <?php foreach ( $resource->contact as $contact ): ?>
        <div class="row-fluid">
            <?php echo $contact; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
