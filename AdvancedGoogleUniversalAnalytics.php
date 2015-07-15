<?php
/*
  Plugin Name: Advanced Google Universal Analytics
  Description: Enter the tracking code for google analytics universal, in your wordpress site by simply putting your ID in the settings. You can also choose which role or user not will be tracked. You can track /wp-admin/ also
  Author: StefanoAI
  Version: 0.5
  Author URI: http://www.stefanoai.com
 */

class AdvancedGoogleUniversalAnalytics {

    function __construct() {
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('wp_ajax_find_user', array($this, 'wp_ajax_find_user'));
        $header_footer = get_option('AI_GoogleUniversalAnalytics_headerfooter');
        $wp_panel = get_option('AI_GoogleUniversalAnalytics_wp-panel');
        if ($header_footer == "header") {
            add_action('wp_head', array($this, 'track_code'), 999);
        } else {
            add_action('wp_footer', array($this, 'track_code'), 999);
        }
        if ($wp_panel) {
            add_action('login_head', array($this, 'track_code'));
            add_action('admin_footer', array($this, 'track_code'));
        }
    }

    function admin_init() {
        register_setting("AI-GoogleUniversalAnalytics", "AI_GoogleUniversalAnalytics_ID");
        register_setting("AI-GoogleUniversalAnalytics", "AI_GoogleUniversalAnalytics_domain");
        register_setting("AI-GoogleUniversalAnalytics", "AI_GoogleUniversalAnalytics_headerfooter");
        register_setting("AI-GoogleUniversalAnalytics", "AI_GoogleUniversalAnalytics_wp-panel");
        register_setting("AI-GoogleUniversalAnalytics", "AI_GoogleUniversalAnalytics_track");
    }

    function admin_menu() {
        add_submenu_page('options-general.php', 'Google Universal Analytics', "Advanced Google Universal Analytics", 'manage_options', 'AI-AdvancedGoogleUniversalAnalytics', array($this, 'page'));
    }

    function page() {
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('jquery-ui-button');
        ?><style>
            #StefanoAI-GUA div.line{
                display: block;
                padding: 2px;
            }
            #StefanoAI-GUA div.line label{
                display: inline-block;
                width: 200px;
            }
            #StefanoAI-GUA div.line tiny{
                font-size: 10px;
            }
            #StefanoAI-GUA div.line input,#StefanoAI-GUA div.line select{
                width: 150px;
            }
            div.user{
                position: relative;
                border: 1px solid #ddd;
                display: inline-block;
                -webkit-border-radius: 3px 3px 3px 3px;
                border-radius: 3px 3px 3px 3px;
                vertical-align: middle;
                line-height: 30px;
                overflow: hidden;
                min-width: 250px;
                margin: 5px;
            }
            div.user h3{
                margin: 0px;
                padding: 0px;
                display: block;
                text-align: center;
                background-color: #444;
                color: #fff;
                cursor: default;
            }
            div.user .left span.label,div.user .right span.label{
                font-size: 10px;
                min-width: 60px;
                display: inline-block;
            }
            div.user .left span.value,div.user .right span.value{
                font-weight: bold;
                display: inline-block;
            }
            div.user .left,div.user .right{
                display: inline-block;
                vertical-align: top;
                padding: 4px;
            }
            div.user .left>div,div.user .right>div{
                display: block;
                line-height: 20px;
            }
            div.user input.delete{
                display: block;
                width: 100%;
            }
            div#roles div.role{
                padding: 5px;
                margin-right: 10px;
                margin-left: 10px;
                display: inline-block;
            }
            #StefanoAI-GUA .users_ div.line label{
                width: auto;
            }
            #StefanoAI-GUA .users_ div.line input{
                width: 250px;
            }
            #StefanoAI-GUA div.line input[type=checkbox],
            #StefanoAI-GUA div.line input[type=radio]{
                width: auto;
            }
            #StefanoAI-GUA .users_ div.line{
                margin-bottom: 20px;
            }
        </style>
        <div class="wrap">
            <h2>Google Universal Analytics</h2>
            <div id="StefanoAI-GUA">
                <form method="post" action="options.php">
                    <div id="tabs">
                        <ul>
                            <li><a href="#tabs-1">Settings</a></li>
                            <li><a href="#tabs-2">No tracking Roles</a></li>
                            <li><a href="#tabs-3">No tracking users</a></li>
                        </ul>
                        <?php
                        settings_fields('AI-GoogleUniversalAnalytics');
                        do_settings_sections('AI-GoogleUniversalAnalytics');
                        $trackid = get_option('AI_GoogleUniversalAnalytics_ID');
                        $domain = get_option('AI_GoogleUniversalAnalytics_domain');
                        $headerfooter = get_option('AI_GoogleUniversalAnalytics_headerfooter');
                        $wp_panel = get_option('AI_GoogleUniversalAnalytics_wp-panel');
                        $tmpusers = get_option('AI_GoogleUniversalAnalytics_track');
                        ?>
                        <div id="tabs-1">
                            <div class="line">
                                <label for="trackid"><?php echo constant('AIGUA TRACKING ID') ?></label>
                                <input id="trackid" type="text" name="AI_GoogleUniversalAnalytics_ID" value="<?php echo esc_attr($trackid) ?>" />
                            </div>
                            <div class="line">
                                <label for="domain"><?php echo constant('AIGUA Domain') ?></label>
                                <input id="domain" type="text" name="AI_GoogleUniversalAnalytics_domain" value="<?php echo esc_attr($domain) ?>" />
                            </div>
                            <div class="line">
                                <label for="loading"><?php echo constant('AIGUA Loading on') ?></label>
                                <select id="loading" name="AI_GoogleUniversalAnalytics_headerfooter">
                                    <option value="header" <?php echo $headerfooter == 'header' ? 'selected' : '' ?>>Header</option>
                                    <option value="footer" <?php echo $headerfooter == 'footer' ? 'selected' : '' ?>>Footer</option>
                                </select>
                            </div>
                            <div class="line">
                                <label for="wp-panel"><?php echo constant('AIGUA Loading on WP panel') ?></label>
                                <input id="wp-panel" type="checkbox" name="AI_GoogleUniversalAnalytics_wp-panel" value="1" <?php echo!empty($wp_panel) ? 'checked' : ''; ?> />
                            </div>
                        </div>
                        <div id="tabs-2">
                            <div id="roles">
                                <?php
                                global $wp_roles;
                                $all_roles = $wp_roles->roles;
                                if (!empty($all_roles)) {
                                    foreach ($all_roles as $role => $v) {
                                        ?>
                                        <div class="role">
                                            <input id="AI_GoogleUniversalAnalytics_track_<?php echo esc_attr($role) ?>" type="checkbox" value="1" name="AI_GoogleUniversalAnalytics_track[notrack_roles][<?php echo esc_attr($role) ?>]" <?php echo is_array($tmpusers) && !empty($tmpusers['notrack_roles'][$role]) ? 'checked' : ''; ?>>
                                            <label for="AI_GoogleUniversalAnalytics_track_<?php echo esc_attr($role) ?>"><?php echo esc_attr($role) ?></label>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div id="tabs-3">
                            <div class="users_">
                                <div class="line">
                                    <label for="find_user"><?php echo constant('AIGUA Find user') ?></label>
                                    <input type="text" name="find_user" id="find_user" />
                                </div>
                                <div id="users"><?php
                                    if (!empty($tmpusers['notrack_users'])) {
                                        $users = array();
                                        foreach ($tmpusers['notrack_users'] as $t => $v) {
                                            $u = get_user_by('id', $t);
                                            $users[$u->user_login] = $u;
                                        }
                                        if (!empty($users)) {
                                            ksort($users);
                                            foreach ($users as $user) {
                                                ?>
                                                <div class='user'>
                                                    <h3><?php echo $user->user_login ?></h3>
                                                    <input type='hidden' name='AI_GoogleUniversalAnalytics_track[notrack_users][<?php echo $user->ID ?>]' value='1' />
                                                    <div class="left">
                                                        <?php echo get_avatar($user->ID); ?>
                                                    </div>
                                                    <div class="right">
                                                        <div>
                                                            <span class="label">Firstname</span>
                                                            <span class="value"><?php echo get_user_meta($user->ID, 'first_name', true); ?></span>
                                                        </div>
                                                        <div>
                                                            <span class="label">Lastname</span>
                                                            <span class="value"><?php echo get_user_meta($user->ID, 'last_name', true); ?></span>
                                                        </div>
                                                    </div>
                                                    <input type="button" class='delete button-secondary' value="delete" />
                                                </div>
                                                <?php
                                            }
                                        }
                                    }
                                    ?></div>
                            </div>
                        </div>
                    </div>
                    <?php submit_button(); ?>
                </form>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                jQuery("#tabs").tabs();
                jQuery("#find_user").autocomplete({
                    source: function (request, response) {
                        jQuery.ajax({
                            url: ajaxurl,
                            dataType: 'json',
                            type: 'POST',
                            data: {
                                action: "find_user",
                                style: "full",
                                maxRows: 10,
                                request: request.term
                            },
                            success: function (data) {
                                response(jQuery.map(data.users, function (item) {
                                    return {
                                        label: item.firstname + " " + item.lastname + " " + item.email,
                                        name: item.firstname + " " + item.lastname + " " + item.nickname,
                                        email: item.email,
                                        id: item.id,
                                        value: ''
                                    };
                                }));
                            }
                        });
                    },
                    minLength: 1,
                    select: function (event, user) {
                        jQuery("#users").append("<div class='user'><input type='hidden' name='AI_GoogleUniversalAnalytics_track[notrack_users][" + user.item.id + "]' value='1' />" + user.item.name + "<div class='delete button-secondary'>delete</div></div>");
                        AIUG_prepare_delete();
                    },
                    open: function () {
                        jQuery(this).removeClass("ui-corner-all").addClass("ui-corner-top");
                    },
                    close: function () {
                        jQuery(this).removeClass("ui-corner-top").addClass("ui-corner-all");
                    }
                });
            });
            function AIUG_prepare_delete() {
                jQuery("#users .delete").each(function () {
                    if (jQuery(this).attr('hasJS') !== '1') {
                        jQuery(this).click(function () {
                            jQuery(this).closest("div.user").remove();
                        });
                    }
                });
            }
            jQuery(document).ready(function () {
                AIUG_prepare_delete();
            });
        </script>
        </div>
        <?php
    }

    function wp_ajax_find_user() {
        $return = array('find' => $_POST['request'], 'users' => array());

        $users = new \WP_User_Query(array(
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'first_name',
                    'value' => $_POST['request'],
                    'compare' => 'LIKE'
                ),
                array(
                    'key' => 'last_name',
                    'value' => $_POST['request'],
                    'compare' => 'LIKE'
                ),
                array(
                    'key' => 'nickname',
                    'value' => $_POST['request'],
                    'compare' => 'LIKE'
                )
            )
        ));
        $user = get_user_by('id', $_POST['request']);
        if (!empty($user)) {
            $users->results[] = $user;
        }
        $user = get_user_by('email', $_POST['request']);
        if (!empty($user)) {
            $users->results[] = $user;
        }
        if (!empty($users->results)) {
            foreach ($users->results as $user) {
                $return['users'][] = array(
                    'nickname' => $user->data->user_login,
                    'firstname' => get_user_meta($user->data->ID, 'first_name', true),
                    'lastname' => get_user_meta($user->data->ID, 'last_name', true),
                    'email' => $user->data->user_email,
                    'id' => $user->data->ID,
                    'user' => $user
                );
            }
        }
        echo json_encode($return);
        exit;
    }

    function track_code() {
        $users = get_option('AI_GoogleUniversalAnalytics_track');
        $current_user = wp_get_current_user();
        $roles = array();
        foreach ($current_user->roles as $role) {
            $roles[$role] = 1;
        }
        foreach ($roles as $k => $v) {
            if (!empty($users['notrack_roles'][$k]) && $users['notrack_roles'][$k] == "1") {
                return;
            }
        }
        if (!empty($users['notrack_users'][$current_user->ID]) && $users['notrack_users'][$current_user->ID] == "1") {
            return;
        }
        $UA = get_option('AI_GoogleUniversalAnalytics_ID');
        $domain = get_option('AI_GoogleUniversalAnalytics_domain', 'auto');
        if (preg_match('/^UA\-[0-9]+[-]+[0-9]+$/', $UA) && preg_match('/^[^\']+$/', $domain)) {
            echo <<<JS
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '$UA', '$domain');
  ga('send', 'pageview');

</script>
JS;
        }
    }

}

if (file_exists(plugin_dir_path(__FILE__) . "lang/" . get_locale() . '.php')) {
    include_once plugin_dir_path(__FILE__) . "lang/" . get_locale() . '.php';
} else {
    include_once plugin_dir_path(__FILE__) . "lang/en_US.php";
}

new AdvancedGoogleUniversalAnalytics();
