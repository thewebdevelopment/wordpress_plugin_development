<?php
/**
 * Class that handles all updates. From time to time, data must be converted, deleted, etc.
 * @author User
 *
 */
class WC_Worldpay_Admin_Update {

	private static $updates = array( 
			'2.0.0' => 'updates/update-2.0.0.php' 
	);

	public static function init() {
		add_action ( 'admin_init', array( __CLASS__, 
				'update_plugin' 
		) );
	}

	public static function update_plugin() {
		$current_version = get_option ( 'online_worldpay_version', '1.2.6' );
		/**
		 * If the current version is less than the latest version, perform the upgrade.
		 */
		if (version_compare ( $current_version, worldpay ()->version (), '<' )) {
			foreach ( self::$updates as $version => $path ) {
				/*
				 * If the current version is less than the version in the loop, then perform upgrade.
				 */
				if (version_compare ( $current_version, $version, '<' )) {
					include worldpay ()->base_path () . 'includes/admin/' . $path;
					$current_version = $version;
				}
			}
			// save latest version.
			update_option ( 'online_worldpay_version', worldpay ()->version () );
		}
		
		if(isset($_GET['worldpay_upgrade_dismiss']) && $_GET['worldpay_upgrade_dismiss'] == 'true'){
			delete_option('worldpay_donations_message');
		}
		
		self::add_messages();
	}
	
	public static function add_messages(){
		if(get_option('worldpay_donations_message', false) == true){
			add_action('admin_notices', function(){
				?>
				<div class="notice notice-info">
					<p style="font-size: 20px;">
						<?php printf('Notice: The Worldpay plugin no longer supports Donations. If you wish to still use the donation functionality, please revert back to %1$s', '<a href="https://downloads.wordpress.org/plugin/woo-easy-pay.1.2.5.zip" target="_blank">Version 1.2.5</a>')?>
					</p>
					<a class="button-primary" href="<?php echo admin_url('admin.php?page=wc-settings&worldpay_upgrade_dismiss=true')?>">Dismiss</a>
				</div>
				<?php
			});
		}
	}
}
WC_Worldpay_Admin_Update::init ();

