<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 11/23/16
 * Time: 12:19 PM
 */

namespace WooPreOrderFix;


class WooSession
{

    private static $instance = null;
    private $session, $woo, $session_id;
    private $cart_items = array();
    private $item, $prev;


    // to prevent initiation with outer code.
    private function __construct()
    {
        $this->start_session();
        //add_action('admin_init', array($this, 'verify_session'));
    }

    private function __destruct()
    {
        $this->end_session();
    }


    // The object is created from within the class itself
    // only if the class has no instance.
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new WooSession();
        }

        return self::$instance;
    }


    public function start_session()
    {
        if (!session_id()) {
            session_start();
            $this->session_id = session_id();

            return $this->session_id;
        }
    }

    public function end_session()
    {
        if (session_id()) {
            session_destroy();
            $this->session_id = null;

            return $this->session_id;
        }
    }

    public function verify_session()
    {

        if (session_id() === $this->session_id ) {
            print $this->session_id;
            return $this->session_id;
        } else {
            return null;
        }

    }

    public function add_item_to_array( $object ) {

        if ($object !== null) {
            $this->item = $object;

            array_push($this->cart_items, $this->item);
        }

    }

    public function output_item_array() {
        return $this->cart_items;
    }


    public function create_new_pre_order()
    {

        //$parent_order = new WC_Order(5548);
        $args = array('sdf' => '4', 'nuu' => '2');
        $order = wc_create_order();
        update_post_meta($order->id, '_customer_user', get_current_user_id());
        update_post_meta($order->id, '_wc_pre_orders_is_pre_order', 1);
        // $order->set_address( $parent_order->get_billing_address, 'billing' );
        $order->set_address($args, 'shipping');
        //$order->set_address( $parent_order->get_shipping_address, 'shipping' );
        $order->update_status('pre-ordered');

        $order->order_custom_fields = get_post_custom($order->id);

        //var_dump($order->get_billing_address);
        //$order->add_coupon( 'wmfreeship' ); // not pennies (use dollars amount)
        //$order->calculate_totals();


        /* indicate the order contains a pre-order
        * update_post_meta( $order_id, '_wc_pre_orders_is_pre_order', 1 );*/

    }
}