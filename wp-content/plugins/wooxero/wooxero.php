<?php
/**
 * Plugin Name: WooXero
 * Plugin URI: https://wooxero.com/
 * Description: An eCommerce toolkit that helps you to link woocommerce with xero.
 * Version: 3.6.4
 * Author: Automattic
 * Author URI: https://wooxero.com
 * Text Domain: wooxero
 * Domain Path: /i18n/languages/
 *
 * @package WooXero
 */

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

/*add_action( 'user_new_form', 'crf_admin_registration_form' );
function crf_admin_registration_form( $operation ) {
    if ( 'add-new-user' !== $operation ) {
        // $operation may also be 'add-existing-user'
        return;
    }

    $paymentTerm = ! empty( $_POST['payment_term'] ) ? intval( $_POST['payment_term'] ) : '';

    ?>
    <h3><?php esc_html_e( 'Xero Custom Field', 'crf' ); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="payment_term"><?php esc_html_e( 'Payment term', 'crf' ); ?></label> <span class="description"><?php esc_html_e( '(required)', 'crf' ); ?></span></th>
            <td>
                <input type="text"
                   id="payment_term"
                   name="payment_term"
                   value="<?php echo esc_attr( $paymentTerm ); ?>"
                   class="regular-text"
                />
            </td>
        </tr>
    </table>
    <?php
}*/

add_action( 'show_user_profile', 'crf_show_extra_profile_fields' );
add_action( 'add_user_profile', 'crf_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'crf_show_extra_profile_fields' );

function crf_show_extra_profile_fields( $user ) {
    $paymentTerm = get_the_author_meta( 'payment_term', $user->ID );
    ?>
    <h3><?php esc_html_e( 'Xero Custom Field', 'crf' ); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="payment_term"><?php esc_html_e( 'payment term', 'crf' ); ?></label></th>
            <td>
                <input type="text"                   
                   id="payment_term"
                   name="payment_term"
                   value="<?php echo esc_attr( $paymentTerm ); ?>"
                   class="regular-text"
                />
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'user_profile_update_errors', 'crf_user_profile_update_errors', 10, 3 );
function crf_user_profile_update_errors( $errors, $update, $user ) {
    if ( ! $update ) {
        return;
    }

    if ( empty( $_POST['payment_term'] ) ) {
        $errors->add( 'payment_term_error', __( '<strong>ERROR</strong>: Please enter your payment term.', 'crf' ) );
    }

    if ( ! empty( $_POST['payment_term'] ) && intval( $_POST['payment_term'] ) > 365 ) {
        $errors->add( 'payment_term_error', __( '<strong>ERROR</strong>: You must be between 1 to 365.', 'crf' ) );
    }
}


add_action( 'personal_options_update', 'crf_add_profile_fields' );
add_action( 'edit_user_profile_update', 'crf_add_profile_fields' );

function crf_add_profile_fields( $user_id ) {

    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }

    if ( ! empty( $_POST['payment_term'] ) && intval( $_POST['payment_term'] ) < 365 ) {
       
        add_user_meta( $user_id, 'payment_term', intval( $_POST['payment_term'] ) );
        
    }
}

add_action( 'personal_options_update', 'crf_update_profile_fields' );
add_action( 'edit_user_profile_update', 'crf_update_profile_fields' );

function crf_update_profile_fields( $user_id ) {

    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }

    if ( ! empty( $_POST['payment_term'] ) && intval( $_POST['payment_term'] ) < 365 ) {
       
        update_user_meta( $user_id, 'payment_term', intval( $_POST['payment_term'] ) );
        
    }
}


add_action( 'woocommerce_before_checkout_process', 'initiate_order' , 10, 1 );
function initiate_order($order_id){
    echo  plugin_dir_path( __FILE__ ). 'xero/private.php';
    include_once( plugin_dir_path( __FILE__ ). 'xero/private.php');      
    $response = $XeroOAuth->request('GET', $XeroOAuth->url('Organisations', 'core'), array(), 'xml', '');
    echo "<prE>"; print_r($response); die('========== xero create invoice ===========');  
    $orgResponse = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
    echo "<prE>"; print_r($orgResponse); die('========== xero create invoice ===========');    
}


add_action('woocommerce_thankyou', 'order_compler', 10, 1);
function order_compler( $order_id ) {

    if ( ! $order_id )
        return;

    // Getting an instance of the order object
    $order = wc_get_order( $order_id );

    include  dirname(__FILE__). 'xero/private.php';
    $response = $XeroOAuth->request('GET', $XeroOAuth->url('Organisations', 'core'), array(), 'xml', '');
    $orgResponse = $XeroOAuth->parseResponse($XeroOAuth->response['response'], $XeroOAuth->response['format']);
    echo "<prE>"; print_r($orgResponse); die('========== xero create invoice ===========');
    echo "<prE>"; print_r($order); die;

    if($order->is_paid())
        $paid = 'yes';
    else
        $paid = 'no';

    // iterating through each order items (getting product ID and the product object) 
    // (work for simple and variable products)
    foreach ( $order->get_items() as $item_id => $item ) {

        if( $item['variation_id'] > 0 ){
            $product_id = $item['variation_id']; // variable product
        } else {
            $product_id = $item['product_id']; // simple product
        }

        // Get the product object
        $product = wc_get_product( $product_id );

    }

    // Ouptput some data
    echo '<p>Order ID: '. $order_id . ' — Order Status: ' . $order->get_status() . ' — Order is paid: ' . $paid . '</p>';
}
