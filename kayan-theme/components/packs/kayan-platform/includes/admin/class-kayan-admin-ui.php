<?php
/**
 * KAYAN Admin UI Framework — reusable admin components.
 *
 * Cards, tables, forms, tabs, panels, dialogs, drawers, notifications,
 * progress, status, filters, bulk actions, search, pagination.
 *
 * Structural framework only — not a frontend/theme redesign.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kayan_Admin_UI {

	/**
	 * @return void
	 */
	public function register() {
		/**
		 * @param Kayan_Admin_UI $ui UI.
		 */
		do_action( 'kayan_admin_ui_registered', $this );
	}

	/**
	 * @param array<string,mixed> $args Args.
	 * @return string
	 */
	public function card( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'title'   => '',
				'content' => '',
				'footer'  => '',
				'status'  => '',
				'class'   => '',
				'id'      => '',
			)
		);
		ob_start();
		?>
		<section class="kayan-admin-card <?php echo esc_attr( $args['class'] ); ?>" <?php echo $args['id'] ? 'id="' . esc_attr( $args['id'] ) . '"' : ''; ?>>
			<?php if ( $args['title'] || $args['status'] ) : ?>
				<header class="kayan-admin-card__header">
					<?php if ( $args['title'] ) : ?>
						<h2 class="kayan-admin-card__title"><?php echo esc_html( $args['title'] ); ?></h2>
					<?php endif; ?>
					<?php if ( $args['status'] ) : ?>
						<?php echo $this->status( array( 'label' => $args['status'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>
				</header>
			<?php endif; ?>
			<div class="kayan-admin-card__body">
				<?php echo $this->safe_html( $args['content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<?php if ( $args['footer'] ) : ?>
				<footer class="kayan-admin-card__footer"><?php echo $this->safe_html( $args['footer'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></footer>
			<?php endif; ?>
		</section>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string,mixed> $args Args: columns[], rows[], selectable, bulk_actions, empty.
	 * @return string
	 */
	public function table( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'columns'      => array(),
				'rows'         => array(),
				'selectable'   => false,
				'bulk_actions' => array(),
				'empty'        => __( 'No items found.', 'kayan' ),
				'class'        => '',
				'id'           => '',
			)
		);
		ob_start();
		?>
		<div class="kayan-admin-table-wrap <?php echo esc_attr( $args['class'] ); ?>">
			<?php if ( ! empty( $args['bulk_actions'] ) ) : ?>
				<?php echo $this->bulk_actions( array( 'actions' => $args['bulk_actions'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>
			<table class="kayan-admin-table widefat striped" <?php echo $args['id'] ? 'id="' . esc_attr( $args['id'] ) . '"' : ''; ?>>
				<thead>
					<tr>
						<?php if ( $args['selectable'] ) : ?>
							<td class="check-column"><input type="checkbox" class="kayan-admin-table__select-all" /></td>
						<?php endif; ?>
						<?php foreach ( (array) $args['columns'] as $col_id => $col ) : ?>
							<?php
							$label = is_array( $col ) ? (string) ( $col['label'] ?? $col_id ) : (string) $col;
							?>
							<th scope="col"><?php echo esc_html( $label ); ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $args['rows'] ) ) : ?>
						<tr><td colspan="<?php echo esc_attr( (string) ( count( (array) $args['columns'] ) + ( $args['selectable'] ? 1 : 0 ) ) ); ?>"><?php echo esc_html( $args['empty'] ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( (array) $args['rows'] as $row ) : ?>
							<tr>
								<?php if ( $args['selectable'] ) : ?>
									<th scope="row" class="check-column"><input type="checkbox" name="ids[]" value="<?php echo esc_attr( (string) ( $row['id'] ?? '' ) ); ?>" /></th>
								<?php endif; ?>
								<?php foreach ( array_keys( (array) $args['columns'] ) as $col_id ) : ?>
									<td><?php echo $this->safe_html( isset( $row[ $col_id ] ) ? $row[ $col_id ] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string,mixed> $args Args: fields[], action, method, submit_label.
	 * @return string
	 */
	public function form( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'fields'       => array(),
				'action'       => '',
				'method'       => 'post',
				'submit_label' => __( 'Save', 'kayan' ),
				'nonce_action' => 'kayan_admin_form',
				'nonce_name'   => '_kayan_nonce',
				'class'        => '',
			)
		);
		ob_start();
		?>
		<form class="kayan-admin-form <?php echo esc_attr( $args['class'] ); ?>" method="<?php echo esc_attr( $args['method'] ); ?>" action="<?php echo esc_url( $args['action'] ); ?>">
			<?php wp_nonce_field( $args['nonce_action'], $args['nonce_name'] ); ?>
			<?php foreach ( (array) $args['fields'] as $field ) : ?>
				<?php echo $this->field( is_array( $field ) ? $field : array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endforeach; ?>
			<p class="submit">
				<button type="submit" class="button button-primary"><?php echo esc_html( $args['submit_label'] ); ?></button>
			</p>
		</form>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string,mixed> $field Field.
	 * @return string
	 */
	public function field( array $field ) {
		$field = wp_parse_args(
			$field,
			array(
				'type'        => 'text',
				'name'        => '',
				'label'       => '',
				'value'       => '',
				'options'     => array(),
				'description' => '',
				'placeholder' => '',
			)
		);
		$name = (string) $field['name'];
		$id   = 'kayan-field-' . sanitize_key( $name );
		ob_start();
		?>
		<div class="kayan-admin-field kayan-admin-field--<?php echo esc_attr( $field['type'] ); ?>">
			<?php if ( $field['label'] ) : ?>
				<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
			<?php endif; ?>
			<?php
			switch ( $field['type'] ) {
				case 'textarea':
					echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" class="large-text" rows="4" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . esc_textarea( (string) $field['value'] ) . '</textarea>';
					break;
				case 'select':
					echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
					foreach ( (array) $field['options'] as $opt_val => $opt_label ) {
						printf(
							'<option value="%s"%s>%s</option>',
							esc_attr( (string) $opt_val ),
							selected( (string) $field['value'], (string) $opt_val, false ),
							esc_html( (string) $opt_label )
						);
					}
					echo '</select>';
					break;
				case 'checkbox':
					printf(
						'<label><input type="checkbox" id="%s" name="%s" value="1"%s /> %s</label>',
						esc_attr( $id ),
						esc_attr( $name ),
						checked( ! empty( $field['value'] ), true, false ),
						esc_html( (string) $field['description'] )
					);
					$field['description'] = '';
					break;
				default:
					printf(
						'<input type="%s" id="%s" name="%s" value="%s" class="regular-text" placeholder="%s" />',
						esc_attr( $field['type'] ),
						esc_attr( $id ),
						esc_attr( $name ),
						esc_attr( (string) $field['value'] ),
						esc_attr( $field['placeholder'] )
					);
			}
			?>
			<?php if ( ! empty( $field['description'] ) && 'checkbox' !== $field['type'] ) : ?>
				<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string,mixed> $args Args: tabs[ id => label|array ], active, content[ id => html ].
	 * @return string
	 */
	public function tabs( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'tabs'    => array(),
				'active'  => '',
				'content' => array(),
				'class'   => '',
			)
		);
		$tab_ids = array_keys( (array) $args['tabs'] );
		$active  = $args['active'] ? sanitize_key( $args['active'] ) : ( $tab_ids ? sanitize_key( (string) $tab_ids[0] ) : '' );
		ob_start();
		?>
		<div class="kayan-admin-tabs <?php echo esc_attr( $args['class'] ); ?>">
			<nav class="kayan-admin-tabs__nav nav-tab-wrapper">
				<?php foreach ( (array) $args['tabs'] as $tab_id => $tab ) : ?>
					<?php
					$tab_id = sanitize_key( (string) $tab_id );
					$label  = is_array( $tab ) ? (string) ( $tab['label'] ?? $tab_id ) : (string) $tab;
					$class  = 'nav-tab' . ( $tab_id === $active ? ' nav-tab-active' : '' );
					?>
					<a href="#kayan-tab-<?php echo esc_attr( $tab_id ); ?>" class="<?php echo esc_attr( $class ); ?>" data-kayan-tab="<?php echo esc_attr( $tab_id ); ?>"><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</nav>
			<?php foreach ( (array) $args['tabs'] as $tab_id => $tab ) : ?>
				<?php $tab_id = sanitize_key( (string) $tab_id ); ?>
				<div class="kayan-admin-tabs__panel<?php echo $tab_id === $active ? ' is-active' : ''; ?>" id="kayan-tab-<?php echo esc_attr( $tab_id ); ?>" data-kayan-tab-panel="<?php echo esc_attr( $tab_id ); ?>">
					<?php echo $this->safe_html( $args['content'][ $tab_id ] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string,mixed> $args Args.
	 * @return string
	 */
	public function panel( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'title'   => '',
				'content' => '',
				'class'   => '',
			)
		);
		return $this->card(
			array(
				'title'   => $args['title'],
				'content' => $args['content'],
				'class'   => 'kayan-admin-panel ' . $args['class'],
			)
		);
	}

	/**
	 * Dialog contract (markup shell; JS enhances later).
	 *
	 * @param array<string,mixed> $args Args.
	 * @return string
	 */
	public function dialog( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'id'      => 'kayan-dialog',
				'title'   => '',
				'content' => '',
				'footer'  => '',
			)
		);
		ob_start();
		?>
		<div class="kayan-admin-dialog" id="<?php echo esc_attr( $args['id'] ); ?>" hidden data-kayan-dialog>
			<div class="kayan-admin-dialog__backdrop" data-kayan-dialog-close></div>
			<div class="kayan-admin-dialog__panel" role="dialog" aria-modal="true">
				<header class="kayan-admin-dialog__header">
					<h2><?php echo esc_html( $args['title'] ); ?></h2>
					<button type="button" class="button-link" data-kayan-dialog-close>&times;</button>
				</header>
				<div class="kayan-admin-dialog__body"><?php echo $this->safe_html( $args['content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php if ( $args['footer'] ) : ?>
					<footer class="kayan-admin-dialog__footer"><?php echo $this->safe_html( $args['footer'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></footer>
				<?php endif; ?>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Drawer contract.
	 *
	 * @param array<string,mixed> $args Args.
	 * @return string
	 */
	public function drawer( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'id'      => 'kayan-drawer',
				'title'   => '',
				'content' => '',
				'side'    => 'right',
			)
		);
		ob_start();
		?>
		<aside class="kayan-admin-drawer kayan-admin-drawer--<?php echo esc_attr( $args['side'] ); ?>" id="<?php echo esc_attr( $args['id'] ); ?>" hidden data-kayan-drawer>
			<header>
				<h2><?php echo esc_html( $args['title'] ); ?></h2>
				<button type="button" data-kayan-drawer-close>&times;</button>
			</header>
			<div class="kayan-admin-drawer__body"><?php echo $this->safe_html( $args['content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</aside>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string,mixed> $args Args: type, message.
	 * @return string
	 */
	public function notice( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'type'    => 'info', // info|success|warning|error
				'message' => '',
				'dismiss' => true,
			)
		);
		$class = 'notice notice-' . sanitize_html_class( $args['type'] ) . ( $args['dismiss'] ? ' is-dismissible' : '' );
		return '<div class="kayan-admin-notice ' . esc_attr( $class ) . '"><p>' . esc_html( (string) $args['message'] ) . '</p></div>';
	}

	/**
	 * @param array<string,mixed> $args Args.
	 * @return string
	 */
	public function progress( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'value' => 0,
				'max'   => 100,
				'label' => '',
			)
		);
		$value = max( 0, min( (float) $args['max'], (float) $args['value'] ) );
		$max   = max( 1, (float) $args['max'] );
		$pct   = round( ( $value / $max ) * 100 );
		ob_start();
		?>
		<div class="kayan-admin-progress" role="progressbar" aria-valuenow="<?php echo esc_attr( (string) $value ); ?>" aria-valuemin="0" aria-valuemax="<?php echo esc_attr( (string) $max ); ?>">
			<?php if ( $args['label'] ) : ?>
				<span class="kayan-admin-progress__label"><?php echo esc_html( $args['label'] ); ?></span>
			<?php endif; ?>
			<div class="kayan-admin-progress__track"><div class="kayan-admin-progress__bar" style="width:<?php echo esc_attr( (string) $pct ); ?>%"></div></div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string,mixed> $args Args.
	 * @return string
	 */
	public function status( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'label' => '',
				'type'  => 'neutral', // success|warning|error|neutral|info
			)
		);
		return '<span class="kayan-admin-status kayan-admin-status--' . esc_attr( $args['type'] ) . '">' . esc_html( (string) $args['label'] ) . '</span>';
	}

	/**
	 * @param array<string,mixed> $args Args: fields[].
	 * @return string
	 */
	public function filters( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'fields' => array(),
				'action' => '',
			)
		);
		ob_start();
		?>
		<form class="kayan-admin-filters" method="get" action="<?php echo esc_url( $args['action'] ); ?>">
			<?php foreach ( (array) $args['fields'] as $field ) : ?>
				<?php echo $this->field( is_array( $field ) ? $field : array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endforeach; ?>
			<button type="submit" class="button"><?php esc_html_e( 'Filter', 'kayan' ); ?></button>
		</form>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string,mixed> $args Args.
	 * @return string
	 */
	public function bulk_actions( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'actions' => array(),
			)
		);
		ob_start();
		?>
		<div class="kayan-admin-bulk">
			<select name="kayan_bulk_action">
				<option value=""><?php esc_html_e( 'Bulk actions', 'kayan' ); ?></option>
				<?php foreach ( (array) $args['actions'] as $value => $label ) : ?>
					<option value="<?php echo esc_attr( (string) $value ); ?>"><?php echo esc_html( (string) $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<button type="submit" class="button"><?php esc_html_e( 'Apply', 'kayan' ); ?></button>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string,mixed> $args Args.
	 * @return string
	 */
	public function search( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'name'        => 's',
				'value'       => '',
				'placeholder' => __( 'Search…', 'kayan' ),
			)
		);
		return '<input type="search" class="kayan-admin-search" name="' . esc_attr( $args['name'] ) . '" value="' . esc_attr( (string) $args['value'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" />';
	}

	/**
	 * @param array<string,mixed> $args Args: total, per_page, page, base_url.
	 * @return string
	 */
	public function pagination( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'total'    => 0,
				'per_page' => 20,
				'page'     => 1,
				'base_url' => '',
			)
		);
		$total_pages = (int) ceil( max( 0, (int) $args['total'] ) / max( 1, (int) $args['per_page'] ) );
		if ( $total_pages < 2 ) {
			return '';
		}
		$page = max( 1, (int) $args['page'] );
		ob_start();
		?>
		<div class="kayan-admin-pagination tablenav">
			<div class="tablenav-pages">
				<span class="displaying-num"><?php echo esc_html( sprintf( /* translators: %d: total items */ __( '%d items', 'kayan' ), (int) $args['total'] ) ); ?></span>
				<span class="pagination-links">
					<?php
					for ( $i = 1; $i <= $total_pages; $i++ ) {
						$url = add_query_arg( 'paged', $i, $args['base_url'] );
						$class = $i === $page ? 'page-numbers current' : 'page-numbers';
						printf( '<a class="%s" href="%s">%d</a> ', esc_attr( $class ), esc_url( $url ), (int) $i );
					}
					?>
				</span>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Empty-state panel for architecture placeholders.
	 *
	 * @param array<string,mixed> $args Args.
	 * @return string
	 */
	public function empty_state( array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'title'       => __( 'Ready', 'kayan' ),
				'description' => __( 'This module is registered. Implementation arrives in a later phase.', 'kayan' ),
			)
		);
		return $this->card(
			array(
				'title'   => $args['title'],
				'content' => '<p class="kayan-admin-empty">' . esc_html( $args['description'] ) . '</p>',
				'status'  => 'architecture',
				'class'   => 'kayan-admin-card--empty',
			)
		);
	}

	/**
	 * @return array<string,mixed>
	 */
	public function describe() {
		return array(
			'components' => array(
				'card', 'table', 'form', 'field', 'tabs', 'panel', 'dialog', 'drawer',
				'notice', 'progress', 'status', 'filters', 'bulk_actions', 'search', 'pagination', 'empty_state',
			),
			'apis'       => array(
				'card'  => 'kayan_admin()->ui->card( $args )',
				'table' => 'kayan_admin()->ui->table( $args )',
				'form'  => 'kayan_admin()->ui->form( $args )',
				'tabs'  => 'kayan_admin()->ui->tabs( $args )',
			),
		);
	}

	/**
	 * Pass-through for pre-built HTML fragment slots (`content`/`footer` on
	 * card()/table()/tabs()/dialog()/drawer()).
	 *
	 * IMPORTANT — this does NOT escape strings. It is a contract, not a
	 * sanitizer: every caller in this codebase composes these slots from
	 * already-escaped pieces (other `Kayan_Admin_UI` methods, `esc_html()`,
	 * `esc_attr()`, etc.) before passing them in here. Never pass a raw
	 * `$_GET`/`$_POST` value or an unescaped DB value as `content`/`footer` —
	 * escape it with the appropriate `esc_*()` function first.
	 *
	 * @param mixed $content Pre-escaped HTML fragment, or an array (JSON-encoded and escaped for debug display).
	 * @return string
	 */
	private function safe_html( $content ) {
		if ( is_array( $content ) ) {
			return esc_html( wp_json_encode( $content ) );
		}
		return (string) $content;
	}
}
