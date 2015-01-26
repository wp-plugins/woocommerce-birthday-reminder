<?php
/**
 * E-mail Lembrete Aniversário
 *
 * @author Carlos Cardoso Dias
 * @version 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<p><?php echo $message; ?></p>

<?php do_action( 'woocommerce_email_footer' ); ?>
