<?php
    $document_url = '#';
    $detail_page = (isset($resource_id) ? true: false);
?>

<div class="row-fluid">
    <h2 class="h2-loop-tit">
        <?php
        echo $resource->title . '<br/>';
        if ($resource->unit){
            foreach ( $resource->unit as $unit ){
                echo $unit . '<br/>';
            }
        }
        ?>
    </h2>
</div>

<div class="row-fluid marginbottom15" >
    <?php
        echo $resource->cooperative_center_code . '<br/>';
        if ($resource->institution_type){
            $exclude_common_types = array('CooperatingCenters', 'ParticipantsUnits', 'VHLNetwork');
            foreach ( $resource->institution_type as $type ){
                if ( !in_array($type, $exclude_common_types) ){
                    echo $type_translated[$type] . '<br/>';
                }
            }
        }
    ?>
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
