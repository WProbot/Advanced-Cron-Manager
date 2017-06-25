<?php
/**
 * RuntimeProvider class
 */

namespace underDEV\AdvancedCronManager;
use tad_DI52_ServiceProvider;

class RuntimeProvider extends tad_DI52_ServiceProvider {

	public function register() {

		$this->container->singleton( 'underDEV\AdvancedCronManager\Utils\Files', function( $c ) {
			return new Utils\Files( $c->getVar( 'plugin_file' ) );
		} );

		$this->container->singleton( 'underDEV\AdvancedCronManager\ScreenRegisterer', function( $c ) {
			return new ScreenRegisterer( $c->make( 'underDEV\AdvancedCronManager\AdminScreen' ) );
		} );

		$this->container->singleton( 'underDEV\AdvancedCronManager\Utils\Assets', function( $c ) {
			$screen_registerer = $c->make( 'underDEV\AdvancedCronManager\ScreenRegisterer' );
			return new Utils\Assets(
				$c->getVar( 'plugin_version' ),
				$c->make( 'underDEV\AdvancedCronManager\Utils\Files' ),
				$screen_registerer->get_page_hook()
			);
		} );

		$this->container->singleton( 'underDEV\AdvancedCronManager\Cron\SchedulesLibrary', function( $c ) {
			return new Cron\SchedulesLibrary( $c->make( 'underDEV\AdvancedCronManager\Utils\Ajax' ) );
		} );

		$this->add_actions();

	}

	public function add_actions() {

		// Load textdomain
		add_action( 'plugins_loaded', $this->container->callback( 'underDEV\AdvancedCronManager\Internationalization', 'load_textdomain' ) );

		// Add plugin's screen in the admin
		add_action( 'admin_menu', $this->container->callback( 'underDEV\AdvancedCronManager\ScreenRegisterer', 'register_screen' ) );

		// Add main section parts on the admin screen
		add_action( 'advanced-cron-manager/screen/main', array(
			$this->container->make( 'underDEV\AdvancedCronManager\AdminScreen' ),
			'load_searchbox_part'
		), 10, 1 );

		add_action( 'advanced-cron-manager/screen/main', array(
			$this->container->make( 'underDEV\AdvancedCronManager\AdminScreen' ),
			'load_events_table_part'
		), 20, 1 );

		// Add sidebar section parts on the admin screen
		add_action( 'advanced-cron-manager/screen/sidebar', array(
			$this->container->make( 'underDEV\AdvancedCronManager\AdminScreen' ),
			'load_schedules_table_part'
		), 10, 1 );

		// Add general parts on the admin screen
		add_action( 'advanced-cron-manager/screen/wrap/after', array(
			$this->container->make( 'underDEV\AdvancedCronManager\AdminScreen' ),
			'load_slidebar_part'
		), 10, 1 );

		// Add tabs to event details
		add_filter( 'advanced-cron-manager/screep/event/details/tabs', array(
			$this->container->make( 'underDEV\AdvancedCronManager\AdminScreen' ),
			'add_default_event_details_tabs'
		), 10, 1 );

		// Enqueue assets
		add_action( 'admin_enqueue_scripts', $this->container->callback( 'underDEV\AdvancedCronManager\Utils\Assets', 'enqueue_admin' ), 10, 1 );

		// Forms
		add_action( 'wp_ajax_acm/schedule/add/form', array(
			$this->container->make( 'underDEV\AdvancedCronManager\FormProvider' ),
			'add_schedule'
		) );

		add_action( 'wp_ajax_acm/schedule/edit/form', array(
			$this->container->make( 'underDEV\AdvancedCronManager\FormProvider' ),
			'edit_schedule'
		) );

		// Schedules
		add_filter( 'cron_schedules', $this->container->callback( 'underDEV\AdvancedCronManager\Cron\Schedules', 'register' ), 10, 1 );

		add_action( 'wp_ajax_acm/rerender/schedules', array(
			$this->container->make( 'underDEV\AdvancedCronManager\AdminScreen' ),
			'ajax_rerender_schedules_table'
		) );

		add_action( 'wp_ajax_acm/schedule/insert', array(
			$this->container->make( 'underDEV\AdvancedCronManager\Cron\SchedulesLibrary' ),
			'ajax_insert'
		) );

		add_action( 'wp_ajax_acm/schedule/edit', array(
			$this->container->make( 'underDEV\AdvancedCronManager\Cron\SchedulesLibrary' ),
			'ajax_edit'
		) );

		add_action( 'wp_ajax_acm/schedule/remove', array(
			$this->container->make( 'underDEV\AdvancedCronManager\Cron\SchedulesLibrary' ),
			'ajax_remove'
		) );

	}

}
