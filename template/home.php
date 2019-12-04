<?php
/*
Template Name: CC Home
*/
global $cc_service_url, $cc_plugin_slug, $cc_plugin_title, $cc_texts;

require_once(CC_PLUGIN_PATH . '/lib/Paginator.php');

$cc_config = get_option('cc_config');
$cc_initial_filter = $cc_config['initial_filter'];

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

$query = ( isset($_GET['s']) ? $_GET['s'] : $_GET['q'] );
$query = stripslashes($query);
$user_filter = stripslashes($_GET['filter']);
$page = ( isset($_GET['page']) ? $_GET['page'] : 1 );
$total = 0;
$count = 10;
$filter = '';

if ($cc_initial_filter != ''){
    if ($user_filter != ''){
        $filter = $cc_initial_filter . ' AND ' . $user_filter;
    }else{
        $filter = $cc_initial_filter;
    }
}else{
    $filter = $user_filter;
}
$start = ($page * $count) - $count;

$cc_search = $cc_service_url . 'api/institution/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&start=' . $start . '&lang=' . $lang;

if ( $user_filter != '' ) {
    $user_filter_list = preg_split("/ AND /", $user_filter);
    $applied_filter_list = array();
    foreach($user_filter_list as $filters){
        preg_match('/([a-z_]+):(.+)/',$filters, $filter_parts);
        if ($filter_parts){
            // convert to internal format
            $applied_filter_list[$filter_parts[1]][] = str_replace('"', '', $filter_parts[2]);
        }
    }
}

$response = @file_get_contents($cc_search);
if ($response){
    $response_json = json_decode($response);
    //var_dump($response_json);
    $total = $response_json->diaServerResponse[0]->response->numFound;
    $start = $response_json->diaServerResponse[0]->response->start;
    $legislation_list = $response_json->diaServerResponse[0]->response->docs;

    $type_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->institution_type;
    $thematic_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->institution_thematic;
    $country_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->country;
}

$page_url_params = real_site_url($cc_plugin_slug) . '?q=' . urlencode($query)  . '&filter=' . urlencode($filter);
$feed_url = real_site_url($cc_plugin_slug) . 'cc-feed?q=' . urlencode($query) . '&filter=' . urlencode($user_filter);

$pages = new Paginator($total, $start, $count);
$pages->paginate($page_url_params);

$home_url = isset($cc_config['home_url_' . $lang]) ? $cc_config['home_url_' . $lang] : real_site_url();
$plugin_breadcrumb = isset($cc_config['plugin_title_' . $lang]) ? $cc_config['plugin_title_' . $lang] : $cc_config['plugin_title'];

/* filters translations */
$type_translated['CoordinatingCentersRg'] = __('CoordinatingCentersRg','cc');
$type_translated['CoordinatingCentersNc'] = __('CoordinatingCentersNc','cc');
$type_translated['CooperatingCenters'] = __('CooperatingCenters','cc');
$type_translated['CooperatingCentersLILACS'] = __('CooperatingCentersLILACS','cc');
$type_translated['CooperatingCentersLEYES'] = __('CooperatingCentersLEYES','cc');
$type_translated['ParticipantsUnits'] = __('ParticipantsUnits','cc');
$type_translated['VHLNetwork'] = __('VHLNetwork','cc');

$thematic_translated['MedCarib'] = __('MedCarib','cc');
$thematic_translated['Nursing'] = __('Nursing','cc');
$thematic_translated['Border'] = __('Border','cc');
$thematic_translated['Disastres'] = __('Disastres','cc');
$thematic_translated['Psychology'] = __('Psychology','cc');
$thematic_translated['MTCI'] = __('MTCI','cc');

?>

<?php get_header('cc');?>

    <div id="content" class="row-fluid">
	  <div class="ajusta2">
          <div class="row-fluid breadcrumb">
              <a href="<?php echo $home_url ?>"><?php _e('Home','cc'); ?></a> >
              <?php if ($query == '' && $filter == ''): ?>
                  <?php echo $plugin_breadcrumb ?>
              <?php else: ?>
                  <a href="<?php echo real_site_url($cc_plugin_slug); ?>"><?php echo $plugin_breadcrumb ?> </a> >
                  <?php _e('Search result', 'cc') ?>
              <?php endif; ?>
          </div>
          <!-- Start sidebar cc-header -->
          <div class="row-fluid">
              <?php dynamic_sidebar('cc-header');?>
          </div>
          <div class="spacer"></div>
          <!-- end sidebar cc-header -->
            <section class="header-search">
                <form role="search" method="get" name="searchForm" id="searchForm" action="<?php echo real_site_url($cc_plugin_slug); ?>">
                    <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
                    <input type="hidden" name="sort" id="sort" value="<?php echo $_GET['sort']; ?>">
                    <input type="hidden" name="format" id="format" value="<?php echo $format ? $format : 'summary'; ?>">
                    <input type="hidden" name="count" id="count" value="<?php echo $count; ?>">
                    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
                    <input value='<?php echo $query; ?>' name="q" class="input-search" id="s" type="text" placeholder="<?php _e('Enter one or more words', 'cc'); ?>">
                    <input id="searchsubmit" value="<?php _e('Search', 'cc'); ?>" type="submit">
                </form>
            </section>

<?php if ($cc_config['page_layout'] != 'whole_page' || $_GET['q'] != '' || $_GET['filter'] != '' ) :  // test for page layout,  query search and Filters ?>

            <div class="content-area result-list">
    			<section id="conteudo">
                    <?php if ( isset($total) && strval($total) == 0) :?>
                        <h1 class="h1-header"><?php _e('No results found','cc'); ?></h1>
                    <?php else :?>
        				<header class="row-fluid border-bottom">
    					   <h1 class="h1-header"> <?php echo $total; ?> <?php _e('Institutions','cc'); ?></h1>
        				</header>
        				<div class="row-fluid">
                            <?php foreach ( $legislation_list as $resource) { ?>
        					    <article class="conteudo-loop">
                                    <?php include('metadata.php') ?>
            					</article>
                            <?php } ?>
        				</div>
                        <div class="row-fluid">
                            <?php echo $pages->display_pages(); ?>
                        </div>
                    <?php endif; ?>
    			</section>
    			<aside id="sidebar">

                    <?php dynamic_sidebar('cc-home');?>

                    <?php if (strval($total) > 0) :?>
                        <div id="filter-link" style="display: none">
                            <div class="mobile-menu" onclick="animateMenu(this)">
                                <a href="javascript:showHideFilters()">
                                    <div class="menu-bar">
                                        <div class="bar1"></div>
                                        <div class="bar2"></div>
                                        <div class="bar3"></div>
                                    </div>
                                    <div class="menu-item">
                                        <?php _e('Filters','cc') ?>
                                    </div>
                                </a>
                           </div>
                        </div>

                        <div id="filters">
                            <?php if ($applied_filter_list) :?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid marginbottom15">
                                        <h1 class="h1-header"><?php echo _e('Selected filters', 'cc') ?></h1>
                                    </header>
                                    <form method="get" name="searchFilter" id="formFilters" action="<?php echo real_site_url($cc_plugin_slug); ?>">
                                        <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
                                        <input type="hidden" name="sort" id="sort" value="<?php echo $sort; ?>">
                                        <input type="hidden" name="format" id="format" value="<?php echo $format; ?>">
                                        <input type="hidden" name="count" id="count" value="<?php echo $count; ?>">
                                        <input type="hidden" name="q" id="query" value="<?php echo $query; ?>" >
                                        <input type="hidden" name="filter" id="filter" value="" >

                                        <?php foreach ( $applied_filter_list as $filter => $filter_values ) :?>
                                            <h2><?php echo translate_label($cc_texts, $filter, 'filter') ?></h2>
                                            <ul>
                                            <?php foreach ( $filter_values as $value ) :?>
                                                <input type="hidden" name="apply_filter" class="apply_filter"
                                                        id="<?php echo md5($value) ?>" value='<?php echo $filter . ':"' . $value . '"'; ?>' >
                                                <li>
                                                    <span class="filter-item">
                                                        <?php
                                                            if ($filter == 'country'){
                                                                echo print_lang_value($value, $site_language);
                                                            }elseif ($filter == 'institution_type'){
                                                                echo $type_translated[$value];
                                                            }elseif ($filter == 'institution_thematic'){
                                                                echo $thematic_translated[$value];
                                                            }else{
                                                                echo $value;
                                                            }
                                                        ?>
                                                    </span>
                                                    <span class="filter-item-del">
                                                        <a href="javascript:remove_filter('<?php echo md5($value) ?>')">
                                                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/del.png">
                                                        </a>
                                                    </span>
                                                </li>
                                            <?php endforeach; ?>
                                            </ul>
                                        <?php endforeach; ?>
                                    </form>
                                </section>
                            <?php endif; ?>

                            <?php
                              $order = explode(';', $cc_config['available_filter']);
                              foreach($order as $index=>$content) {
                                $content = trim($content);
                            ?>

                            <?php if ($content == 'Type') :  ?>
                                <section class="row-fluid widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($cc_texts, 'institution_type', 'filter'); ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $type_list as $type ) { ?>
                                            <li class="cat-item">
                                                <?php
                                                    $filter_link = '?';
                                                    if ($query != ''){
                                                        $filter_link .= 'q=' . $query . '&';
                                                    }
                                                    $filter_link .= 'filter=institution_type:"' . $type[0] . '"';
                                                    if ($user_filter != ''){
                                                        $filter_link .= ' AND ' . $user_filter ;
                                                    }
                                                ?>
                                                <a href='<?php echo $filter_link; ?>'><?php echo $type_translated[$type[0]]; ?></a>
                                                <span class="cat-item-count"><?php echo $type[1]; ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>

                            <?php if ($content == 'Thematic' && $thematic_list ): ?>
                			    <section class="row-fluid marginbottom25 widget_categories">
                					<header class="row-fluid border-bottom marginbottom15">
                						<h1 class="h1-header"><?php echo translate_label($cc_texts, 'institution_thematic', 'filter') ?></h1>
                					</header>
                					<ul>
                                        <?php foreach ( $thematic_list as $thematic) { ?>
                                            <?php
                                                $filter_link = '?';
                                                if ($query != ''){
                                                    $filter_link .= 'q=' . $query . '&';
                                                }
                                                $filter_link .= 'filter=institution_thematic:"' . $thematic[0] . '"';
                                                if ($user_filter != ''){
                                                    $filter_link .= ' AND ' . $user_filter ;
                                                }
                                            ?>
                                            <li class="cat-item">
                                                <a href='<?php echo $filter_link; ?>'><?php echo $thematic_translated[$thematic[0]] ?></a>
                                                <span class="cat-item-count"><?php echo $thematic[1] ?></span>
                                            </li>
                                        <?php } ?>
                					</ul>
                				</section>
                            <?php endif; ?>

                            <?php if ( $content == 'Country' ): ?>
                                <section class="row-fluid marginbottom25 widget_categories">
                                    <header class="row-fluid border-bottom marginbottom15">
                                        <h1 class="h1-header"><?php echo translate_label($cc_texts, 'country', 'filter') ?></h1>
                                    </header>
                                    <ul>
                                        <?php foreach ( $country_list as $country ) { ?>
                                            <?php
                                                $filter_link = '?';
                                                if ($query != ''){
                                                    $filter_link .= 'q=' . $query . '&';
                                                }
                                                $filter_link .= 'filter=country:"' . $country[0] . '"';
                                                if ($user_filter != ''){
                                                    $filter_link .= ' AND ' . $user_filter ;
                                                }
                                            ?>
                                            <li class="cat-item">
                                                <a href='<?php echo $filter_link; ?>'><?php print_lang_value($country[0], $site_language)?></a>
                                                <span class="cat-item-count"><?php echo $country[1] ?></span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </section>
                            <?php endif; ?>
                        <?php } ?>
                    <?php endif; ?>
                </aside>
    			<div class="spacer"></div>
            </div> <!-- close DIV.result-area -->
<?php else: // start whole page ?>

<div class="content-area result-list">
  <section >
    <header class="row-fluid">
     <h1 class="h1-header"> <?php echo $total; ?> <?php _e('Institutions','cc'); ?></h1>
     </header>
  </section>
		</div> <!-- close DIV.ajusta2 -->
<?php
$order = explode(';', $cc_config['available_filter']);

  foreach($order as $index=>$content) {
    $content = trim($content);
?>


  <?php if ($content == 'Type') : ?>
      <section>
        <header class="row-fluid border-bottom">
           <h1 class="h1-header"><?php echo translate_label($cc_texts, 'institution_type', 'filter'); ?></h1>
        </header>
          <ul class="col3">
              <?php foreach ( $type_list as $type ) { ?>
                  <li class="cat-item">
                      <?php
                          $filter_link = '?';
                          if ($query != ''){
                              $filter_link .= 'q=' . $query . '&';
                          }
                          $filter_link .= 'filter=institution_type:"' . $type[0] . '"';
                          if ($user_filter != ''){
                              $filter_link .= ' AND ' . $user_filter ;
                          }
                      ?>
                      <div class="list_bloco">
                        <div class="list_link">
                          <a href='<?php echo $filter_link; ?>'><?php echo $type_translated[$type[0]]; ?></a>
                        </div>
                        <div class="list_badge">
                            <span><?php echo $type[1]; ?></span>
                        </div>
                      </div>
                  </li>
              <?php } ?>
          </ul>
      </section>
  <?php endif; ?>
<?php if ($content == 'Thematic' ): ?>
  <section>
    <header class="row-fluid border-bottom">
      <h1 class="h1-header"><?php echo translate_label($cc_texts, 'institution_thematic', 'filter') ?></h1>
    </header>
    <ul class="col3">
    <?php foreach ( $thematic_list as $thematic ) { ?>
    <?php
      $filter_link = '?';
      if ($query != ''){
        $filter_link .= 'q=' . $query . '&';
      }
      $filter_link .= 'filter=institution_thematic:"' . $thematic[0] . '"';
      if ($user_filter != ''){
        $filter_link .= ' AND ' . $user_filter ;
      }
      ?>
        <li>
        <div class="list_bloco">
          <div class="list_link">
            <a href='<?php echo $filter_link; ?>'><?php echo $thematic_translated[$thematic[0]]; ?></a>
          </div>
          <div class="list_badge">
            <span><?php echo $thematic[1] ?></span>
          </div>
        </div>
      </li>
<?php } ?>
    </ul>
  </section>
<?php endif; ?>

<?php if( $content == 'Country' ): ?>
    <section >
        <header class="row-fluid border-bottom ">
            <h1 class="h1-header"><?php echo translate_label($cc_texts, 'country', 'filter') ?></h1>
        </header>
        <ul class="col3">
            <?php foreach ( $country_list as $country ) { ?>
                <?php
                    $filter_link = '?';
                    if ($query != ''){
                        $filter_link .= 'q=' . $query . '&';
                    }
                    $filter_link .= 'filter=country:"' . $country[0] . '"';
                    if ($user_filter != ''){
                        $filter_link .= ' AND ' . $user_filter ;
                    }
                ?>
                <li>
                    <div class="list_bloco">
                      <div class="list_link">
                        <a href='<?php echo $filter_link; ?>'><?php print_lang_value($country[0], $site_language)?></a>

                      </div>
                      <div class="list_badge">
                        <span><?php echo $country[1] ?></span>

                      </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </section>
<?php endif; ?>

<?php } ?>

</div>
<div class="spacer"></div>

<?php endif; // end whole page?>


	</div>
<?php get_footer();?>
