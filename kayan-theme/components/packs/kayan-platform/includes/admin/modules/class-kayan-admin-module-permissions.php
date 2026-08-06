<?php
/**
 * Admin module: Permissions.
 *
 * Reuses Kayan_Admin_Permissions (roles/capabilities) and the standard
 * WordPress user role API (set_role) — no second RBAC storage.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_Module_Permissions {

	const NONCE_ASSIGN = 'kayan_admin_permissions_assign';

	/**
	 * @return void
	 */
	public function register() {
		add_action( 'kayan_admin_register_modules', array( $this, 'register_module' ), 15 );
	}

	/**
	 * @param Kayan_Admin_Module_Registry $registry Registry.
	 * @return void
	 */
	public function register_module( $registry ) {
		$registry->register_module(
			'permissions',
			array(
				'label'       => __( 'Permissions', 'kayan' ),
				'description' => __( 'KAYAN roles, capabilities, and WordPress user assignment.', 'kayan' ),
				'icon'        => 'dashicons-shield-alt',
				'position'    => 102,
				'capability'  => 'kayan_manage_settings',
				'group'       => 'system',
				'screen'      => array( $this, 'screen' ),
				'save'        => array( $this, 'save' ),
				'enabled'     => true,
			)
		);
	}

	/**
	 * @param array $module  Module.
	 * @param array $context Context.
	 * @return void
	 */
	public function save( $module, $context ) {
		unset( $module );
		if ( ! current_user_can( 'promote_users' ) ) {
			return;
		}
		if ( ! isset( $_POST['kayan_perm_user'], $_POST['kayan_perm_role'] ) ) {
			return;
		}
		check_admin_referer( self::NONCE_ASSIGN, '_kayan_nonce' );

		$user_id = absint( $_POST['kayan_perm_user'] );
		$role    = sanitize_key( wp_unslash( $_POST['kayan_perm_role'] ) );
		$user    = get_userdata( $user_id );
		$roles   = kayan_admin()->permissions->roles();

		if ( ! $user || ( '' !== $role && ! get_role( $role ) && ! isset( $roles[ $role ] ) ) ) {
			$context['admin']->redirect_module( 'permissions', 'error' );
			return;
		}

		if ( '' === $role ) {
			$context['admin']->redirect_module( 'permissions', 'updated' );
			return;
		}

		// Only allow assigning roles KAYAN actually created as WP roles.
		if ( isset( $roles[ $role ] ) && ! empty( $roles[ $role ]['create_wp_role'] ) && get_role( $role ) ) {
			$user->add_role( $role );
		} elseif ( get_role( $role ) ) {
			$user->set_role( $role );
		}

		if ( function_exists( 'kayan_logger' ) ) {
			kayan_logger()->info( 'security', 'admin.permissions.assigned', array( 'user' => $user_id, 'role' => $role ) );
		}

		$context['admin']->redirect_module( 'permissions', 'updated' );
	}

	/**
	 * @param array $module  Module.
	 * @param array $context Context.
	 * @return void
	 */
	public function screen( $module, $context ) {
		unset( $module );
		/** @var Kayan_Admin_UI $ui */
		$ui          = $context['ui'];
		$admin       = $context['admin'];
		$permissions = kayan_admin()->permissions;
		$roles       = $permissions->roles();
		$caps        = $permissions->capabilities();

		$role_rows = array();
		foreach ( $roles as $id => $role ) {
			$role_rows[] = array(
				'id'    => $id,
				'role'  => '<code>' . esc_html( $id ) . '</code>',
				'label' => esc_html( (string) $role['label'] ),
				'wp'    => $role['create_wp_role'] ? $ui->status( array( 'label' => __( 'Dedicated WP role', 'kayan' ), 'type' => 'info' ) ) : ( ! empty( $role['grant_to_wp_roles'] ) ? esc_html( implode( ', ', $role['grant_to_wp_roles'] ) ) : '—' ),
				'caps'  => esc_html( (string) count( (array) $role['capabilities'] ) ),
			);
		}

		echo $ui->table( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'columns' => array(
					'role'  => __( 'Role', 'kayan' ),
					'label' => __( 'Label', 'kayan' ),
					'wp'    => __( 'WordPress mapping', 'kayan' ),
					'caps'  => __( 'Capabilities', 'kayan' ),
				),
				'rows'    => $role_rows,
			)
		);

		$cap_groups = array();
		foreach ( $caps as $id => $cap ) {
			$group = (string) ( $cap['group'] ?? 'general' );
			$cap_groups[ $group ][] = '<code>' . esc_html( $id ) . '</code>';
		}
		$cap_html = '';
		foreach ( $cap_groups as $group => $items ) {
			$cap_html .= '<p><strong>' . esc_html( $group ) . ':</strong> ' . implode( ', ', $items ) . '</p>';
		}
		echo $ui->card( array( 'title' => __( 'Registered capabilities', 'kayan' ), 'content' => $cap_html ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( current_user_can( 'promote_users' ) ) {
			echo $this->render_assign_form( $ui, $admin, $roles ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * @param Kayan_Admin_UI       $ui    UI.
	 * @param Kayan_Admin_Platform $admin Admin.
	 * @param array                $roles Roles.
	 * @return string
	 */
	private function render_assign_form( $ui, $admin, array $roles ) {
		$users = get_users( array( 'number' => 100, 'orderby' => 'display_name' ) );
		$user_options = array( '' => __( '— Select user —', 'kayan' ) );
		foreach ( $users as $user ) {
			$user_options[ $user->ID ] = $user->display_name . ' (' . $user->user_login . ')';
		}

		$role_options = array( '' => __( '— Select role —', 'kayan' ) );
		foreach ( $roles as $id => $role ) {
			if ( ! empty( $role['create_wp_role'] ) ) {
				$role_options[ $id ] = (string) $role['label'];
			}
		}
		foreach ( wp_roles()->roles as $id => $role ) {
			$role_options[ $id ] = $role['name'];
		}

		ob_start();
		?>
		<form method="post" action="<?php echo esc_url( $admin->module_url( 'permissions' ) ); ?>">
			<?php wp_nonce_field( self::NONCE_ASSIGN, '_kayan_nonce' ); ?>
			<?php
			echo $ui->field( array( 'type' => 'select', 'name' => 'kayan_perm_user', 'label' => __( 'User', 'kayan' ), 'options' => $user_options ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $ui->field( array( 'type' => 'select', 'name' => 'kayan_perm_role', 'label' => __( 'Role', 'kayan' ), 'options' => $role_options ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Assign role', 'kayan' ); ?></button></p>
		</form>
		<?php
		return $ui->card(
			array(
				'title'   => __( 'Assign a KAYAN role to a user', 'kayan' ),
				'content' => (string) ob_get_clean(),
			)
		);
	}
}
