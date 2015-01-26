<?php
/**
 * WooCommerce User Birthday Email.
 *
 * @category Email
 * @author   Carlos Cardoso Dias
 *
 */

/**
 * Anti cheating code
 **/
defined( 'ABSPATH' ) or die( 'A Ag&ecirc;ncia Magma n&atilde;o deixa voc&ecirc; trapacear ;)' );

if( ! class_exists( 'WC_User_Birthday_Email' ) ):
class WC_User_Birthday_Email extends WC_Email {

	public $enabled_day;
	public $subject_day;
	public $heading_day;
	public $message_day;
	public $email_type_day;

	public $enabled_month;
	public $subject_month;
	public $heading_month;
	public $message_month;
	public $email_type_month;

	public function __construct() {
		$this->id               = 'wc_user_birthday_email';
		$this->title            = __( 'Aniversário' , 'woocommerce-birthday-coupon' );
		$this->description      = __( 'E-mails de aniversário serão mandados no mês e/ou dia do aniversário do usuário' , 'woocommerce-birthday-coupon' );

		$this->enabled_day      = $this->get_option('enabled_day');
		$this->subject_day      = $this->get_option('subject_day');
		$this->heading_day      = $this->get_option('heading_day');
		$this->message_day      = $this->get_option('message_day');
		$this->email_type_day   = $this->get_option('email_type_day');

		$this->enabled_month    = $this->get_option('enabled_month');
		$this->subject_month    = $this->get_option('subject_month');
		$this->heading_month    = $this->get_option('heading_month');
		$this->message_month    = $this->get_option('message_month');
		$this->email_type_month = $this->get_option('email_type_month');

		$this->template_html  = 'day-birthday-reminder.php';
		$this->template_plain = 'plain-day-birthday-reminder.php';
		$this->template_base    = WooCommerce_Birthday_Reminder::plugin_path() . 'emails/';

		add_action( 'birthdays_users_of_the_day' , array( $this , 'trigger_day' ) );
		add_action( 'birthdays_users_of_the_month' , array( $this , 'trigger_month' ) );

		parent::__construct();
	}

	public function trigger_month( $users ) {
		$this->enabled        = $this->enabled_month;
		$this->subject        = $this->subject_month;
		$this->heading        = $this->heading_month;
		$this->message        = $this->message_month;
		$this->email_type     = $this->email_type_month;
		$this->template_html  = 'month-birthday-reminder.php';
		$this->template_plain = 'plain-month-birthday-reminder.php';
		
		$this->trigger( $users );
	}

	public function trigger_day( $users ) {
		$this->enabled        = $this->enabled_day;
		$this->subject        = $this->subject_day;
		$this->heading        = $this->heading_day;
		$this->message        = $this->message_day;
		$this->email_type     = $this->email_type_day;
		$this->template_html  = 'day-birthday-reminder.php';
		$this->template_plain = 'plain-day-birthday-reminder.php';
		
		$this->trigger( $users );
	}

	public function trigger( $users ) {
		if ( ! $users || ! $this->is_enabled() ) {
			return;
		}
		$position = count( $this->find );
		
		$this->find[ $position ] = '{user}';

		foreach ( $users as $user ) {
			$this->object = $user;
			
			$this->replace[ $position ] = $this->object->user_nicename;

			$this->send( $this->object->user_email , $this->get_subject() . ' - ' . $this->get_blogname() , $this->get_content() , $this->get_headers() , $this->get_attachments() );
		}
	}

	public function get_content_html() {
		return $this->my_get_content( $this->template_html );
	}

	public function get_content_plain() {
		return $this->my_get_content( $this->template_plain );
	}

	private function my_get_content( $from ) {
		ob_start();
		wc_get_template( $from , array(
			'email_heading'   => $this->get_heading(),
			'message'         => $this->format_string( $this->message )
		), $this->template_base , $this->template_base );
		return ob_get_clean();
	}

	public function init_form_fields() {
		$this->form_fields = array(
			'first_section'  => array(
				'title'             => __( 'Dia do aniversário' , 'woocommerce-birthday-coupon' ),
				'type'              => 'title',
				'description'       => __( 'Configurações para o e-mail enviado para os aniversariantes do dia' , 'woocommerce-birthday-coupon' )
			),
			'enabled_day'    => array(
				'title'   => __( 'Habilitar/Desabilitar' , 'woocommerce-birthday-coupon' ),
				'type'    => 'checkbox',
				'label'   => __( 'Habilita o envio do e-mail no dia do aniversário do usuário' , 'woocommerce-birthday-coupon' ),
				'default' => 'yes'
			),
			'subject_day'    => array(
				'title'       => __( 'Assunto' , 'woocommerce-birthday-coupon' ),
				'type'        => 'text',
				'description' => __( 'Assunto do e-mail' , 'woocommerce-birthday-coupon' ),
				'placeholder' => __( 'Assunto' , 'woocommerce-birthday-coupon' ),
				'default'     => __( 'Dia do aniversário' , 'woocommerce-birthday-coupon' )
			),
			'heading_day'    => array(
				'title'       => __( 'Título' , 'woocommerce-birthday-coupon' ),
				'type'        => 'text',
				'description' => __( 'Título do e-mail' , 'woocommerce-birthday-coupon' ),
				'placeholder' => __( 'Título' , 'woocommerce-birthday-coupon' ),
				'default'     => __( 'Parabéns!' , 'woocommerce-birthday-coupon' )
			),
			'message_day'    => array(
				'title'       => __( 'Mensagem' , 'woocommerce-birthday-coupon' ),
				'type'        => 'textarea',
				'description' => __( 'Conteúdo do e-mail. Use: <code>{user}</code> para o nome do aniversariante' , 'woocommerce-birthday-coupon' ),
				'placeholder' => __( 'Conteúdo' , 'woocommerce-birthday-coupon' ),
				'default'     => __( 'Hoje é o seu aniversário!' , 'woocommerce-birthday-coupon' )
			),
			'email_type_day' => array(
				'title'       => __( 'Tipo de e-mail' , 'woocommerce-birthday-coupon' ),
				'type'        => 'select',
				'description' => __( 'Escolha o formato em que o e-mail será enviado' , 'woocommerce-birthday-coupon' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => 'Plain text',
					'html'      => 'HTML'
				)
			),
			'second_section'   => array(
				'title'             => __( 'Mês do aniversário' , 'woocommerce-birthday-coupon' ),
				'type'              => 'title',
				'description'       => __( 'Configurações para o e-mail enviado para os aniversariantes do mês' , 'woocommerce-birthday-coupon' )
			),
			'enabled_month'    => array(
				'title'   => __( 'Habilitar/Desabilitar' , 'woocommerce-birthday-coupon' ),
				'type'    => 'checkbox',
				'label'   => __( 'Habilita o envio do e-mail no mês do aniversário do usuário' , 'woocommerce-birthday-coupon' ),
				'default' => 'yes'
			),
			'subject_month'    => array(
				'title'       => __( 'Assunto' , 'woocommerce-birthday-coupon' ),
				'type'        => 'text',
				'description' => __( 'Assunto do e-mail' , 'woocommerce-birthday-coupon' ),
				'placeholder' => __( 'Assunto' , 'woocommerce-birthday-coupon' ),
				'default'     => __( 'Mês do aniversário' , 'woocommerce-birthday-coupon' )
			),
			'heading_month'    => array(
				'title'       => __( 'Título' , 'woocommerce-birthday-coupon' ),
				'type'        => 'text',
				'description' => __( 'Título do e-mail' , 'woocommerce-birthday-coupon' ),
				'placeholder' => __( 'Título' , 'woocommerce-birthday-coupon' ),
				'default'     => __( 'Parabéns antecipado!' , 'woocommerce-birthday-coupon' )
			),
			'message_month'    => array(
				'title'       => __( 'Mensagem' , 'woocommerce-birthday-coupon' ),
				'type'        => 'textarea',
				'description' => __( 'Conteúdo do e-mail. Use: <code>{user}</code> para o nome do aniversariante' , 'woocommerce-birthday-coupon' ),
				'placeholder' => __( 'Conteúdo' , 'woocommerce-birthday-coupon' ),
				'default'     => __( 'Preparamos uma oferta especial para o mês do seu aniversário :)' , 'woocommerce-birthday-coupon' )
			),
			'email_type_month' => array(
				'title'       => __( 'Tipo de e-mail' , 'woocommerce-birthday-coupon' ),
				'type'        => 'select',
				'description' => __( 'Escolha o formato em que o e-mail será enviado' , 'woocommerce-birthday-coupon' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => 'Plain text',
					'html'      => 'HTML'
				)
			)
		);
	}
}

endif;