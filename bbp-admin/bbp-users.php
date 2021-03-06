<?php

/**
 * bbPress Users Admin Class
 *
 * @package bbPress
 * @subpackage Administration
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'BBP_Users_Admin' ) ) :
/**
 * Loads bbPress users admin area
 *
 * @package bbPress
 * @subpackage Administration
 * @since bbPress (r2464)
 */
class BBP_Users_Admin {

	/**
	 * The bbPress users admin loader
	 *
	 * @since bbPress (r2515)
	 *
	 * @uses BBP_Users_Admin::setup_globals() Setup the globals needed
	 * @uses BBP_Users_Admin::setup_actions() Setup the hooks and actions
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Setup the admin hooks, actions and filters
	 *
	 * @since bbPress (r2646)
	 * @access private
	 *
	 * @uses add_action() To add various actions
	 */
	function setup_actions() {

		// Bail if in network admin
		if ( is_network_admin() )
			return;

		// Admin styles
		add_action( 'admin_head',               array( $this, 'admin_head'          ) );

		// User profile edit/display actions
		add_action( 'edit_user_profile',        array( $this, 'user_profile_forums' ) );
		add_action( 'show_user_profile',        array( $this, 'user_profile_forums' ) );

		// User profile save actions
		add_action( 'personal_options_update',  array( $this, 'user_profile_update' ) );
		add_action( 'edit_user_profile_update', array( $this, 'user_profile_update' ) );
	}

	/**
	 * Add some general styling to the admin area
	 *
	 * @since bbPress (r2464)
	 *
	 * @uses bbp_get_forum_post_type() To get the forum post type
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses sanitize_html_class() To sanitize the classes
	 */
	public function admin_head() { 
		?>

		<style type="text/css" media="screen">
		/*<![CDATA[*/
			div.bbp-user-capabilities {
				margin: 0 10px 10px;
				display: inline-block;
				vertical-align: top;
			}
			
			div.bbp-user-capabilities h4 {
				margin: 0 0 10px;
			}
			
			p.bbp-default-caps-wrapper {
				clear: both;
				margin: 80px -10px 0;
			}
		/*]]>*/
		</style>

		<?php
	}

	/**
	 * Responsible for saving additional profile options and settings
	 *
	 * @since bbPress (r2464)
	 *
	 * @param $user_id The user id
	 * @uses do_action() Calls 'bbp_user_profile_update'
	 * @return bool Always false
	 */
	public function user_profile_update( $user_id ) { 

		// Bail if no user
		if ( empty( $user_id ) )
			return;

		// Load up the user
		$user = new WP_User( $user_id );

		// Either reset caps for role
		if ( ! empty( $_POST['bbp-default-caps'] ) ) {

			// Remove all caps
			foreach ( bbp_get_capability_groups() as $group ) {
				foreach ( bbp_get_capabilities_for_group( $group ) as $capability ) {
					$user->remove_cap( $capability );
				}
			}

		// Or set caps individually
		} else {

			// Loop through capability groups
			foreach ( bbp_get_capability_groups() as $group ) {
				foreach ( bbp_get_capabilities_for_group( $group ) as $capability ) {

					// Maybe add cap
					if ( ! empty( $_POST['_bbp_' . $capability] ) && ! $user->has_cap( $capability ) ) {
						$user->add_cap( $capability, true );

					// Maybe remove cap
					} elseif ( empty( $_POST['_bbp_' . $capability] ) && $user->has_cap( $capability ) ) {
						$user->add_cap( $capability, false );
					}
				}
			}
		}
	}

	/**
	 * Responsible for saving additional profile options and settings
	 *
	 * @since bbPress (r2464)
	 *
	 * @param WP_User $profileuser User data
	 * @uses do_action() Calls 'bbp_user_profile_forums'
	 * @return bool Always false
	 */
	public function user_profile_forums( $profileuser ) { 

		// Bail if current user cannot edit users
		if ( ! current_user_can( 'edit_user', $profileuser->ID ) )
			return;

		// Noop WordPress additional caps output area
		add_filter( 'additional_capabilities_display', '__return_false' ); ?>

		<h3><?php _e( 'Forum Capabilities', 'bbpress' ); ?></h3>

		<table class="form-table">
			<tbody>
				<tr>
					<th><?php _e( 'This user can:', 'bbpress' ); ?></th>

					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Additional Capabilities', 'bbpress' ); ?></span></legend>

							<?php foreach ( bbp_get_capability_groups() as $group ) : ?>

								<div class="bbp-user-capabilities">
									<h4><?php bbp_capability_group_title( $group ); ?></h4>

									<?php foreach ( bbp_get_capabilities_for_group( $group ) as $capability ) : ?>

										<label for="_bbp_<?php echo $capability; ?>">
											<input id="_bbp_<?php echo $capability; ?>" name="_bbp_<?php echo $capability; ?>" type="checkbox" id="_bbp_<?php echo $capability; ?>" value="1" <?php checked( user_can( $profileuser->ID, $capability ) ); ?> />
											<?php bbp_capability_title( $capability ); ?>
										</label>
										<br />

									<?php endforeach; ?>

								</div>

							<?php endforeach; ?>

							<p class="bbp-default-caps-wrapper">
								<input type="submit" name="bbp-default-caps" class="button" value="<?php _e( 'Reset to Default', 'bbpress' ); ?>"/>
							</p>

						</fieldset>
					</td>
				</tr>

			</tbody>
		</table>

		<?php
	}
}
new BBP_Users_Admin();
endif; // class exists
