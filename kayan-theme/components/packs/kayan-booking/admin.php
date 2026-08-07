<?php
/**
 * لوحة إدارة الحجوزات — kayan-booking (Phase 1)
 */

if ( ! class_exists( 'Kayan_Booking_Admin' ) ) {

	class Kayan_Booking_Admin {

		const STATUSES = array(
			'pending'   => 'قيد الانتظار',
			'confirmed' => 'مؤكد',
			'assigned'  => 'تم إسناده لفني',
			'on_the_way'=> 'الفني في الطريق',
			'completed' => 'مكتمل',
			'cancelled' => 'ملغي',
			'refunded'  => 'مسترجع',
		);

		public function Setup() {
			add_action( 'admin_menu', array( $this, 'Menu' ) );
			add_action( 'admin_post_kayan_booking_update_status', array( $this, 'UpdateStatus' ) );
		}

		public function Menu() {
			add_menu_page(
				'الحجوزات',
				'الحجوزات',
				'manage_options',
				'kayan-bookings',
				array( $this, 'RenderList' ),
				'dashicons-calendar-alt',
				26
			);
		}

		public function UpdateStatus() {
			if ( ! current_user_can( 'manage_options' ) ) wp_die( 'غير مصرح' );
			check_admin_referer( 'kayan_booking_status' );

			global $wpdb;
			$booking_id = isset( $_POST['booking_id'] ) ? absint( $_POST['booking_id'] ) : 0;
			$status     = isset( $_POST['status'] ) ? sanitize_key( $_POST['status'] ) : '';

			if ( $booking_id && array_key_exists( $status, self::STATUSES ) ) {
				$bookings_table = $wpdb->prefix . 'kayan_bookings';
				$wpdb->update(
					$bookings_table,
					array( 'status' => $status, 'updated_at' => current_time( 'mysql' ) ),
					array( 'id' => $booking_id ),
					array( '%s', '%s' ),
					array( '%d' )
				);
				$wpdb->insert(
					$wpdb->prefix . 'kayan_booking_activity',
					array(
						'booking_id' => $booking_id,
						'status'     => $status,
						'note'       => 'تم تحديث الحالة من لوحة التحكم',
						'actor'      => wp_get_current_user()->user_login,
						'created_at' => current_time( 'mysql' ),
					),
					array( '%d', '%s', '%s', '%s', '%s' )
				);
			}

			wp_safe_redirect( add_query_arg( array( 'page' => 'kayan-bookings', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
			exit;
		}

		public function RenderList() {
			if ( ! current_user_can( 'manage_options' ) ) wp_die( 'غير مصرح' );

			global $wpdb;
			$bookings_table = $wpdb->prefix . 'kayan_bookings';
			$items_table    = $wpdb->prefix . 'kayan_booking_items';

			$paged    = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
			$per_page = 20;
			$offset   = ( $paged - 1 ) * $per_page;

			$status_filter = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '';
			$where  = '';
			$params = array();
			if ( '' !== $status_filter && array_key_exists( $status_filter, self::STATUSES ) ) {
				$where    = 'WHERE status = %s';
				$params[] = $status_filter;
			}

			$count_sql = "SELECT COUNT(id) FROM {$bookings_table} {$where}";
			$total     = $params ? $wpdb->get_var( $wpdb->prepare( $count_sql, $params ) ) : $wpdb->get_var( $count_sql );

			$sql = "SELECT * FROM {$bookings_table} {$where} ORDER BY id DESC LIMIT %d OFFSET %d";
			$query_params = array_merge( $params, array( $per_page, $offset ) );
			$rows = $wpdb->get_results( $wpdb->prepare( $sql, $query_params ) );

			$total_revenue = $wpdb->get_var( "SELECT SUM(total) FROM {$bookings_table} WHERE payment_status = 'paid'" );
			$total_bookings = $wpdb->get_var( "SELECT COUNT(id) FROM {$bookings_table}" );
			$total_pending  = $wpdb->get_var( "SELECT COUNT(id) FROM {$bookings_table} WHERE status = 'pending'" );

			echo '<div class="wrap">';
			echo '<h1>الحجوزات</h1>';

			echo '<div style="display:flex;gap:16px;margin:16px 0;">';
				echo '<div style="background:#fff;border:1px solid #ccd0d4;border-radius:6px;padding:16px 20px;"><strong style="font-size:22px;display:block;">' . (int) $total_bookings . '</strong>إجمالي الحجوزات</div>';
				echo '<div style="background:#fff;border:1px solid #ccd0d4;border-radius:6px;padding:16px 20px;"><strong style="font-size:22px;display:block;">' . (int) $total_pending . '</strong>قيد الانتظار</div>';
				echo '<div style="background:#fff;border:1px solid #ccd0d4;border-radius:6px;padding:16px 20px;"><strong style="font-size:22px;display:block;">' . esc_html( number_format( (float) $total_revenue, 2 ) ) . '</strong>إجمالي الإيرادات (مدفوع)</div>';
			echo '</div>';

			echo '<ul class="subsubsub">';
			echo '<li><a href="' . esc_url( admin_url( 'admin.php?page=kayan-bookings' ) ) . '"' . ( '' === $status_filter ? ' class="current"' : '' ) . '>الكل</a> | </li>';
			$i = 0; $c = count( self::STATUSES );
			foreach ( self::STATUSES as $skey => $slabel ) {
				$i++;
				echo '<li><a href="' . esc_url( add_query_arg( array( 'page' => 'kayan-bookings', 'status' => $skey ), admin_url( 'admin.php' ) ) ) . '"' . ( $status_filter === $skey ? ' class="current"' : '' ) . '>' . esc_html( $slabel ) . '</a>' . ( $i < $c ? ' | ' : '' ) . '</li>';
			}
			echo '</ul>';

			echo '<table class="wp-list-table widefat fixed striped">';
			echo '<thead><tr>
				<th>رقم الحجز</th>
				<th>العميل</th>
				<th>الخدمات</th>
				<th>الموعد</th>
				<th>الإجمالي</th>
				<th>الحالة</th>
				<th>الدفع</th>
				<th>تاريخ الإنشاء</th>
				<th>إجراء</th>
			</tr></thead><tbody>';

			if ( empty( $rows ) ) {
				echo '<tr><td colspan="9">لا توجد حجوزات حتى الآن</td></tr>';
			} else {
				foreach ( $rows as $row ) {
					$items = $wpdb->get_results( $wpdb->prepare( "SELECT service_title FROM {$items_table} WHERE booking_id = %d", $row->id ) );
					$titles = wp_list_pluck( $items, 'service_title' );

					echo '<tr>';
					echo '<td><strong>' . esc_html( $row->booking_ref ) . '</strong></td>';
					echo '<td>' . esc_html( $row->customer_name ) . '<br><small>' . esc_html( $row->customer_phone ) . '</small></td>';
					echo '<td>' . esc_html( implode( '، ', $titles ) ) . '</td>';
					echo '<td>' . esc_html( $row->booking_date . ' ' . $row->booking_time ) . '</td>';
					echo '<td>' . esc_html( number_format( (float) $row->total, 2 ) . ' ' . $row->currency ) . '</td>';

					echo '<td>';
					echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:flex;gap:6px;">';
					wp_nonce_field( 'kayan_booking_status' );
					echo '<input type="hidden" name="action" value="kayan_booking_update_status">';
					echo '<input type="hidden" name="booking_id" value="' . (int) $row->id . '">';
					echo '<select name="status">';
					foreach ( self::STATUSES as $skey => $slabel ) {
						echo '<option value="' . esc_attr( $skey ) . '" ' . selected( $row->status, $skey, false ) . '>' . esc_html( $slabel ) . '</option>';
					}
					echo '</select>';
					echo '<button type="submit" class="button button-small">تحديث</button>';
					echo '</form>';
					echo '</td>';

					echo '<td>' . esc_html( 'paid' === $row->payment_status ? 'مدفوع' : 'غير مدفوع' ) . '</td>';
					echo '<td>' . esc_html( $row->created_at ) . '</td>';
					echo '<td><a class="button button-small" href="' . esc_url( add_query_arg( array( 'page' => 'kayan-bookings', 'view' => $row->id ), admin_url( 'admin.php' ) ) ) . '">عرض</a></td>';
					echo '</tr>';
				}
			}
			echo '</tbody></table>';

			$total_pages = $total ? (int) ceil( $total / $per_page ) : 1;
			if ( $total_pages > 1 ) {
				echo '<div class="tablenav"><div class="tablenav-pages">';
				echo paginate_links( array(
					'base'      => add_query_arg( 'paged', '%#%' ),
					'format'    => '',
					'current'   => $paged,
					'total'     => $total_pages,
				) );
				echo '</div></div>';
			}

			if ( isset( $_GET['view'] ) ) {
				$this->RenderDetail( absint( $_GET['view'] ) );
			}

			echo '</div>';
		}

		public function RenderDetail( $booking_id ) {
			global $wpdb;
			$bookings_table = $wpdb->prefix . 'kayan_bookings';
			$items_table    = $wpdb->prefix . 'kayan_booking_items';
			$activity_table = $wpdb->prefix . 'kayan_booking_activity';

			$booking = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bookings_table} WHERE id = %d", $booking_id ) );
			if ( ! $booking ) return;

			$items    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$items_table} WHERE booking_id = %d", $booking_id ) );
			$activity = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$activity_table} WHERE booking_id = %d ORDER BY id DESC", $booking_id ) );
			$payments_table = $wpdb->prefix . 'kayan_payments';
			$payment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$payments_table} WHERE booking_id = %d ORDER BY id DESC LIMIT 1", $booking_id ) );

			echo '<div style="background:#fff;border:1px solid #ccd0d4;border-radius:6px;padding:20px;margin-top:20px;">';
			echo '<h2>تفاصيل الحجز ' . esc_html( $booking->booking_ref ) . '</h2>';

			echo '<h3>بيانات العميل والموقع</h3>';
			echo '<p><strong>الاسم:</strong> ' . esc_html( $booking->customer_name ) . '</p>';
			echo '<p><strong>الهاتف:</strong> ' . esc_html( $booking->customer_phone ) . ' — <strong>واتساب:</strong> ' . esc_html( $booking->customer_whatsapp ) . '</p>';
			echo '<p><strong>البريد:</strong> ' . esc_html( $booking->customer_email ) . '</p>';
			echo '<p><strong>العنوان:</strong> ' . esc_html( trim( "{$booking->country} {$booking->emirate} {$booking->city} {$booking->district} {$booking->address} {$booking->building_name} {$booking->unit_number} {$booking->floor}" ) ) . '</p>';
			if ( $booking->lat && $booking->lng ) {
				echo '<p><a target="_blank" href="https://www.google.com/maps?q=' . esc_attr( $booking->lat ) . ',' . esc_attr( $booking->lng ) . '">عرض على خرائط جوجل</a></p>';
			}
			echo '<p><strong>الموعد:</strong> ' . esc_html( $booking->booking_date . ' ' . $booking->booking_time ) . '</p>';
			echo '<p><strong>ملاحظات:</strong> ' . esc_html( $booking->notes ) . '</p>';

			echo '<h3>الخدمات المطلوبة</h3><table class="wp-list-table widefat striped"><thead><tr><th>الخدمة</th><th>السعر</th><th>تفاصيل إضافية</th></tr></thead><tbody>';
			foreach ( $items as $item ) {
				$fields = json_decode( $item->fields_data, true );
				echo '<tr><td>' . esc_html( $item->service_title ) . '</td><td>' . esc_html( $item->unit_price ) . '</td><td>';
				if ( is_array( $fields ) && ! empty( $fields ) ) {
					echo '<ul style="margin:0;">';
					foreach ( $fields as $fk => $fv ) {
						$fv_display = is_array( $fv ) ? implode( '، ', $fv ) : $fv;
						if ( is_string( $fv_display ) && 0 === strpos( $fv_display, 'http' ) ) {
							echo '<li>' . esc_html( $fk ) . ': <a target="_blank" href="' . esc_url( $fv_display ) . '">صورة مرفقة</a></li>';
						} else {
							echo '<li>' . esc_html( $fk ) . ': ' . esc_html( $fv_display ) . '</li>';
						}
					}
					echo '</ul>';
				}
				echo '</td></tr>';
			}
			echo '</tbody></table>';

			echo '<p><strong>الإجمالي الفرعي:</strong> ' . esc_html( $booking->subtotal ) . ' — <strong>الضريبة:</strong> ' . esc_html( $booking->tax ) . ' — <strong>الإجمالي:</strong> ' . esc_html( $booking->total ) . ' ' . esc_html( $booking->currency ) . '</p>';

			if ( $payment ) {
				echo '<h3>الدفع</h3>';
				echo '<p><strong>الوسيلة:</strong> ' . esc_html( $payment->method ) . ( $payment->card_last4 ? ' (**** ' . esc_html( $payment->card_last4 ) . ')' : '' ) . '</p>';
				echo '<p><strong>حالة الدفع:</strong> ' . esc_html( $payment->status ) . ' — <strong>رقم المعاملة:</strong> ' . esc_html( $payment->txn_ref ) . '</p>';
				if ( $payment->invoice_number ) {
					$invoice_url = trailingslashit( home_url( '/AjaxCenter/kayan_invoice_view' ) ) . '?ref=' . rawurlencode( $booking->booking_ref );
					echo '<p><strong>رقم الفاتورة:</strong> ' . esc_html( $payment->invoice_number ) . ' — <a target="_blank" href="' . esc_url( $invoice_url ) . '">عرض الفاتورة</a></p>';
				}
			}

			echo '<h3>سجل الحالة</h3><ul>';
			foreach ( $activity as $act ) {
				echo '<li>' . esc_html( $act->created_at ) . ' — ' . esc_html( self::STATUSES[ $act->status ] ?? $act->status ) . ' (' . esc_html( $act->actor ) . ')' . ( $act->note ? ' — ' . esc_html( $act->note ) : '' ) . '</li>';
			}
			echo '</ul>';

			echo '</div>';
		}
	}

	( new Kayan_Booking_Admin() )->Setup();
}
