<?php
function cc_page_admin() {
    $config = get_option('cc_config');

?>
    <div class="wrap">
            <div id="icon-options-general" class="icon32"></div>
            <h2><?php _e('Cooperating Centers Settings', 'cc'); ?></h2>

            <form method="post" action="options.php">

                <?php settings_fields('cc-settings-group'); ?>

                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><?php _e('Plugin page', 'cc'); ?>:</th>
                            <td><input type="text" name="cc_config[plugin_slug]" value="<?php echo ($config['plugin_slug'] != '' ? $config['plugin_slug'] : 'centers'); ?>" class="regular-text code"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Filter query', 'cc'); ?>:</th>
                            <td><input type="text" name="cc_config[initial_filter]" value='<?php echo $config['initial_filter'] ?>' class="regular-text code"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Google Analytics code', 'cc'); ?>:</th>
                            <td><input type="text" name="cc_config[google_analytics_code]" value="<?php echo $config['google_analytics_code'] ?>" class="regular-text code"></td>
                        </tr>
                        <?php
                        if ( function_exists( 'pll_the_languages' ) ) {
                            $available_languages = pll_languages_list();
                            $available_languages_name = pll_languages_list(array('fields' => 'name'));
                            $count = 0;
                            foreach ($available_languages as $lang) {
                                $key_name = 'plugin_title_' . $lang;
                                $home_url = 'home_url_' . $lang;

                                echo '<tr valign="top">';
                                echo '    <th scope="row"> ' . __("Home URL", "cc") . ' (' . $available_languages_name[$count] . '):</th>';
                                echo '    <td><input type="text" name="cc_config[' . $home_url . ']" value="' . $config[$home_url] . '" class="regular-text code"></td>';
                                echo '</tr>';

                                echo '<tr valign="top">';
                                echo '    <th scope="row"> ' . __("Page title", "cc") . ' (' . $available_languages_name[$count] . '):</th>';
                                echo '    <td><input type="text" name="cc_config[' . $key_name . ']" value="' . $config[$key_name] . '" class="regular-text code"></td>';
                                echo '</tr>';
                                $count++;
                            }
                        }else{
                            echo '<tr valign="top">';
                            echo '   <th scope="row">' . __("Page title", "cc") . ':</th>';
                            echo '   <td><input type="text" name="cc_config[plugin_title]" value="' . $config["plugin_title"] . '" class="regular-text code"></td>';
                            echo '</tr>';
                        }

                        ?>

                        <tr valign="top">
                          <th scope="row">
                            <?php _e('Page layout', 'cc'); ?>:
                          </th>
                          <td>
                            <label for="whole_page">
                              <input type="radio" id="whole_page" value="whole_page" name="cc_config[page_layout]"  <?php if($config['page_layout'] == 'whole_page' ){ echo 'checked'; }?>>
                              <?php _e('Show filters as whole page', 'cc'); ?>

                            </label>
                            <br>
                            <br>
                            <label for="normal_page">
                              <input type="radio" id="normal_page" value="normal_page" name="cc_config[page_layout]" <?php if(!isset($config['page_layout']) || $config['page_layout'] == 'normal_page' ){ echo 'checked'; }?> >
                              <?php _e('Show normal page', 'cc'); ?>

                            </label>
                          </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row"><?php _e('Search filters', 'cc');?>:</th>

                            <?php
                              if(!isset($config['available_filter'])){
                                $config['available_filter'] = 'Type;Thematic;Country';
                                $order = explode(';', $config['available_filter'] );
                              }else {
                                $order = array_filter(explode(';', $config['available_filter']));
                            }

                            ?>

                            <td>
                              <table border=0>
                                <tr>
                                <td >
                                    <p align="left"><?php _e('Available', 'cc');?><br>
                                      <ul id="sortable1" class="droptrue">
                                      <?php
                                      if(!in_array('Type', $order) && !in_array('Type ', $order) ){
                                        echo '<li class="ui-state-default" id="Type">'.translate('Type','cc').'</li>';
                                      }
                                      if(!in_array('Thematic', $order) && !in_array('Thematic ', $order) ){
                                        echo '<li class="ui-state-default" id="Thematic">'.translate('Thematic','cc').'</li>';
                                      }
                                      if(!in_array('Country', $order) && !in_array('Country ', $order) ){
                                        echo '<li class="ui-state-default" id="Country">'.translate('Country','cc').'</li>';
                                      }
                                      ?>
                                      </ul>

                                    </p>
                                </td>

                                <td >
                                    <p align="left"><?php _e('Selected', 'cc');?> <br>
                                      <ul id="sortable2" class="sortable-list">
                                      <?php
                                      foreach ($order as $index => $item) {
                                        $item = trim($item); // Important
                                        echo '<li class="ui-state-default" id="'.$item.'">'.translate($item ,'cc').'</li>';
                                      }
                                      ?>
                                      </ul>
                                      <input type="hidden" id="order_aux" name="cc_config[available_filter]" value="<?php echo trim($config['available_filter']); ?> " >
                                    </p>
                                </td>
                                </tr>
                                </table>

                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save changes') ?>" />
                </p>
            </form>
        </div>
        <script type="text/javascript">
            var $j = jQuery.noConflict();

            $j( function() {
              $j( "ul.droptrue" ).sortable({
                connectWith: "ul"
              });

              $j('.sortable-list').sortable({

                connectWith: 'ul',
                update: function(event, ui) {
                  var changedList = this.id;
                  var order = $j(this).sortable('toArray');
                  var positions = order.join(';');
                  $j('#order_aux').val(positions);

                }
              });
            } );
        </script>
<?php
}
?>
