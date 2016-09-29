<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.cis.fiu.edu
 * @since             1.0.0
 * @package           Fiu_Scis_Alumni_Linkedin
 *
 * @wordpress-plugin
 * Plugin Name:       FIU SCIS Alumni Linkedin Plugin
 * Plugin URI:        https://www.cis.fiu.edu/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            https://www.cis.fiu.edu
 * Author URI:        https://www.cis.fiu.edu
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fiu-scis-alumni-linkedin
 * Domain Path:       /languages
 *
 * FIU SCIS Alumni Linkedin Plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * FIU SCIS Alumni Linkedin Plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with FIU SCIS Alumni Linkedin Plugin. If not, see http://www.gnu.org/licenses/gpl-2.0.txt.
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fiu-scis-alumni-linkedin-activator.php
 */
function activate_fiu_scis_alumni_linkedin()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-fiu-scis-alumni-linkedin-activator.php';
    Fiu_Scis_Alumni_Linkedin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fiu-scis-alumni-linkedin-deactivator.php
 */
function deactivate_fiu_scis_alumni_linkedin()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-fiu-scis-alumni-linkedin-deactivator.php';
    Fiu_Scis_Alumni_Linkedin_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_fiu_scis_alumni_linkedin');
register_deactivation_hook(__FILE__, 'deactivate_fiu_scis_alumni_linkedin');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-fiu-scis-alumni-linkedin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fiu_scis_alumni_linkedin()
{

    $plugin = new Fiu_Scis_Alumni_Linkedin();
    $plugin->run();

}

run_fiu_scis_alumni_linkedin();


/**
 * LinkedIn Authorization function
 * First Step in OAuth2 Authentication
 * Requires 3rd party app to create app at
 * https://www.linkedin.com/developer/apps
 *
 * Obtain client ID and assign string to $fslp_client_id.
 * Change $fslp_state to any string and keep confidential
 * Create static page in Wordpress that matches redirect_uri
 * and assign string to $fslp_redirect_uri.
 *
 * All these items will late be added through admin.
 */
function fslp_linkedin_authorization()
{

    $fslp_api = 'Linkedin';
    $fslp_client_id = 'XXXXXXXXXXXXXX';
    $fslp_response_type = 'code';
    $fslp_state = 'YYYYYYYYYYYYYYYYYYYYYYYY';
    $fslp_authorization_endpoint = esc_url('https://www.linkedin.com/oauth/v2/authorization');
    $fslp_redirect_uri = esc_url('https://www.example.com/linkedin/');

    echo esc_html("<a href='" . $fslp_authorization_endpoint . "?client_id=" . $fslp_client_id . "&redirect_uri=" . $fslp_redirect_uri . "&response_type=" . $fslp_response_type . "&state=" . $fslp_state . "'><button type='button' class='btn btn-primary'>Sign up with " . $fslp_api . "</button></a>");
}

/**
* Linked Authorization response function
* Obtains authorization code from LinkedIn if user agrees or error.
* @return authorization code
*/
function fslp_linkedin_authorization_response()
{

    $fslp_state = 'YYYYYYYYYYYYYYYYYYYYYYYY';
    $fslp_authorization_code = $_GET['code'];

    if ($_GET['state'] === $fslp_state) {
        return $fslp_authorization_code;
    } else {
        echo 'Authorization tampered with: ' . $_GET['error_description'];
    }
}


/**
*   LinkedIn Access token request and response function
*   Assign client_id to $fslp_client_id
*   Assign client_secret to $fslp_cs
*   Assign redirect uri to $fslp_redirect_uri
*   To protect our member's data, LinkedIn does not generate excessively long-lived access tokens.
*   You should ensure your application is built to handle refreshing user tokens before they expire,
*   to avoid having to unnecessarily send your users through the authorization process to re-gain access
*   to their LinkedIn profile.
*   @param authorization code
*   @return access_token
*/
function fslp_linkedin_access_token_request_and_response($fslp_temp_code)
{

    $fslp_token_endpoint = esc_url('https://www.linkedin.com/oauth/v2/accessToken');
    $fslp_grant_type = 'authorization_code';
    $fslp_code = $fslp_temp_code;
    $fslp_redirect_uri = esc_url('https://www.example.com/linkedin/');
    $fslp_client_id = 'XXXXXXXXXXXXXX';
    $fslp_cs = 'ZZZZZZZZZZZZZZZZ';

    $fslp_response = wp_remote_post($fslp_token_endpoint, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded'
            ),
            'body' => array(
                'client_id' => $fslp_client_id,
                'client_secret' => $fslp_cs,
                'redirect_uri' => $fslp_redirect_uri,
                'grant_type' => $fslp_grant_type,
                'code' => $fslp_code
            )
        )
    );

    if (is_wp_error($fslp_response)) {
        $error_message = $fslp_response->get_error_message();
        echo "<h4>Access token request denied: $error_message </h4>";
    } else {
        $fslp_access_token_obj = json_decode($fslp_response['body'], true);
    }
    return $fslp_access_token_obj['access_token'];
}

/**
 * LinkedIn basicProfile GET function
 * Call object to view with syntax below:
 * <?php $fslp_linkedin_data = get_basic_profile_linkedin($token); ?>
 * Syntax for property of object call:
 * <?php echo $fslp_linkedin_data['publicProfileUrl']; ?>
 * Fields:
 * firstName, lastName, headline, emailAddress, publicProfileUrl, pictureUrl
 * There is no year from API.  It must be added manually be user at time of registration
 * @param access token
 * @return decoded json object ready for view or model
 */
function fslp_get_basic_profile_linkedin($fslp_temp_token)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.linkedin.com/v1/people/~:(first-name,last-name,headline,email-address,public-profile-url,picture-url,location)?format=json&oauth2_access_token=" . $fslp_temp_token . "",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache"
        ),
    ));

    $fslp_linkedin_response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        $fslp_linkedin_data = json_decode($fslp_linkedin_response, true);
    }
    fslp_pass_into_alumni($fslp_linkedin_data, $fslp_temp_token);
    return $fslp_linkedin_data;
}

/**
* LinkedIn pass_into_alumni function
* Creates draft custom post of type student_alumni and adds all items in JSON object
* and access token
* Will contain business logic regarding hashing access token data
* @param Decoded JSON object, Access token
*/
function fslp_pass_into_alumni($fslp_temp_linkedin_data, $fslp_temp_token)
{

    $fslp_alumni_year = '2016';

    /*TODO
    //ASK FOR YEAR OF GRADUATION AT THIS TIME
    */


//    PASS LINKEDIN DATA ALONG WITH YEAR OF GRADUATION INTO CUSTOM POST TYPE 'ALUMNI'
    $fslp_alumni_post_title = $fslp_temp_linkedin_data['firstName'] . ' ' . $fslp_temp_linkedin_data['lastName'];
    $fslp_alumni_headline = $fslp_temp_linkedin_data['headline'];
    $fslp_post_type = 'student_alumni';

    // Gather post data.
    $fslp_alumni = array(
        'post_title' => $fslp_alumni_post_title,
        'post_content' => $fslp_alumni_headline,
        'post_type' => $fslp_post_type
    );

// Insert the post into the database.
    $fslp_temp_boolean = fslp_alumni_exists($fslp_alumni_post_title, $fslp_alumni_headline);
    if ($fslp_temp_boolean == 0) {
        $fslp_alumni_id = wp_insert_post($fslp_alumni);
        update_field('field_57eaa3cc3af21', $fslp_alumni_post_title, $fslp_alumni_id);
        update_field('field_57eaa3d23af22', $fslp_alumni_year, $fslp_alumni_id);
        update_field('field_57eaa3d73af23', $fslp_alumni_headline, $fslp_alumni_id);
        update_field('field_57eaa3d93af24', $fslp_temp_linkedin_data['publicProfileUrl'], $fslp_alumni_id);
        update_field('field_57eaa4623af25', $fslp_temp_linkedin_data['pictureUrl'], $fslp_alumni_id);
        update_field('field_57eaa48d3af26', $fslp_temp_linkedin_data['emailAddress'], $fslp_alumni_id);
        update_field('field_57eaa49a3af27', $fslp_temp_token, $fslp_alumni_id);
        echo '<h4>Thank you for registering to the FIU Alumni Website.<br />Your information will be reviewed before being published on the site.</h4>';
    } else {
        echo '<h4>Alumni ' . $fslp_alumni_post_title . ' already exists.  Please try again.</h4>';
    }

}

/*
* Alumni Exists functions
* Exact copy of post_exists() to be used on frontend to avoid duplicate custom post
* type student_alumni
*
*/
function fslp_alumni_exists($title, $content = '', $date = '') {
    global $wpdb;

    $post_title = wp_unslash( sanitize_post_field( 'post_title', $title, 0, 'db' ) );
    $post_content = wp_unslash( sanitize_post_field( 'post_content', $content, 0, 'db' ) );
    $post_date = wp_unslash( sanitize_post_field( 'post_date', $date, 0, 'db' ) );

    $query = "SELECT ID FROM $wpdb->posts WHERE 1=1";
    $args = array();

    if ( !empty ( $date ) ) {
        $query .= ' AND post_date = %s';
        $args[] = $post_date;
    }

    if ( !empty ( $title ) ) {
        $query .= ' AND post_title = %s';
        $args[] = $post_title;
    }

    if ( !empty ( $content ) ) {
        $query .= ' AND post_content = %s';
        $args[] = $post_content;
    }

    if ( !empty ( $args ) )
        return (int) $wpdb->get_var( $wpdb->prepare($query, $args) );

    return 0;
}
