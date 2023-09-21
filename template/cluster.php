<?php

ini_set('display_errors', '0');

$lang = $_POST['lang'];
$site_lang = $_POST['site_lang'];
$query = $_POST['query'];
$filter = $_POST['filter'];
$user_filter = $_POST['uf'];
$fb = $_POST['fb'];
$cluster = $_POST['cluster'];
$cluster_fb = ( $_POST['cluster'] ) ? $_POST['cluster'].':'.$fb : '';
$count = 1;

$cc_service_request = $cc_service_url . 'api/institution/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&fb=' . $cluster_fb . '&lang=' . $lang . '&count=' . $count;

//echo "<pre>"; print_r($cc_service_request); echo "</pre>"; die();

$response = @file_get_contents($cc_service_request);
if ($response){
    $response_json = json_decode($response);
    //var_dump($response_json);
    $total = $response_json->diaServerResponse[0]->response->numFound;
    $start = $response_json->diaServerResponse[0]->response->start;

    $facet_list = (array) $response_json->diaServerResponse[0]->facet_counts->facet_fields;

    $type_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->institution_type;
    $thematic_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->institution_thematic;
    $country_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->country;
}
?>

<?php if ( $facet_list[$cluster] ) : ?>
    <ul class="filter-list">
        <?php foreach ( $facet_list[$cluster] as $filter_item ) { ?>
            <?php
                $filter_value = $filter_item[0];
                $filter_count = $filter_item[1];
            ?>
            <?php if ( filter_var($filter_value, FILTER_VALIDATE_INT) === false ) : ?>
                <li class="cat-item">
                    <?php
                        $filter_link = '?';
                        if ($query != ''){
                            $filter_link .= 'q=' . $query . '&';
                        }
                        $filter_link .= 'filter=' . $cluster . ':"' . $filter_value . '"';
                        if ($user_filter != ''){
                            $filter_link .= ' AND ' . $user_filter ;
                        }
                    ?>
                    <a href='<?php echo $filter_link; ?>'><?php print_lang_value($filter_value, $site_lang)?></a>
                    <span class="cat-item-count">(<?php echo $filter_count; ?>)</span>
                </li>
            <?php endif; ?>
        <?php } ?>
    </ul>
<?php endif; ?>