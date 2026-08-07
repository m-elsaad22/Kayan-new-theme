<?php 
if ( ! class_exists( 'Kayan_Report_Share' ) ) {

	class Kayan_Report_Share {

		public function __construct() {
			add_action( 'template_redirect', array( $this, 'maybe_render_report' ), 1 );
		}

		public function maybe_render_report() {
			$token = isset( $_GET['kayan_report'] ) ? sanitize_text_field( wp_unslash( $_GET['kayan_report'] ) ) : '';
			if ( ! $token || strlen( $token ) !== 64 || ! ctype_xdigit( $token ) ) {
				return;
			}

			global $wpdb;
			$table  = kayan_track_table( 'reports' );
			$report = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM {$table} WHERE token = %s AND expires_at > NOW() LIMIT 1",
					$token
				)
			);

			if ( ! $report ) {
				wp_die(
					'<h2 style="font-family:Tajawal,sans-serif;text-align:center;margin-top:4rem">التقرير غير موجود أو انتهت صلاحيته</h2>',
					'تقرير KAYAN',
					array( 'response' => 404 )
				);
			}

			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$table} SET view_count = view_count + 1 WHERE token = %s",
					$token
				)
			);

			$data    = json_decode( $report->data_json, true );
			$filters = json_decode( $report->filters_json, true );
			$this->render_public_report( $report, is_array( $data ) ? $data : array(), is_array( $filters ) ? $filters : array() );
			exit;
		}

		private function render_public_report( $report, $data, $filters ) {
			$logo_url  = yc_get_option( 'logo__data' );
			$logo_url  = is_array( $logo_url ) && ! empty( $logo_url['url'] ) ? $logo_url['url'] : get_site_icon_url();
			$site_name = get_bloginfo( 'name' );
			$totals    = isset( $data['totals'] ) ? $data['totals'] : array();
			$rows      = isset( $data['rows'] ) ? $data['rows'] : array();
			?>
			<!DOCTYPE html>
			<html dir="rtl" lang="ar">
			<head>
				<meta charset="UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<title><?php echo esc_html( $report->title ? $report->title : 'تقرير التحويلات' ); ?></title>
				<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
				<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
				<style>
					*, *::before, *::after { box-sizing: border-box; }
					body { font-family: 'Tajawal', sans-serif; background: #f8fafc; color: #0f172a; margin: 0; direction: rtl; }
					.rpt-header { background: linear-gradient(135deg, #0056b3, #1a7fe0); color: #fff; padding: 1.5rem 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
					.rpt-logo { height: 48px; }
					.rpt-title { font-size: 1.3rem; font-weight: 800; }
					.rpt-meta { font-size: .78rem; opacity: .85; }
					.rpt-body { max-width: 1100px; margin: 2rem auto; padding: 0 1.5rem; }
					.stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin: 1.5rem 0; }
					.stat-box { background: #f0f7ff; border-right: 6px solid #0056b3; border-radius: 15px; padding: 1.2rem; box-shadow: 0 4px 6px rgba(0,0,0,.05); }
					.stat-box.wa { background: #f0fdf4; border-right-color: #25d366; }
					.stat-val { font-size: 2rem; font-weight: 800; }
					.stat-lbl { font-size: .75rem; color: #64748b; font-weight: 600; }
					table.rpt-tbl { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
					table.rpt-tbl thead tr { background: #0056b3; }
					table.rpt-tbl th { color: #fff; padding: .6rem .8rem; text-align: right; font-size: .75rem; }
					table.rpt-tbl td { padding: .5rem .8rem; font-size: .78rem; border-bottom: 1px solid #e2e8f0; }
					.btn-print { background: #0056b3; color: #fff; border: none; padding: .5rem 1.2rem; border-radius: 8px; font-family: 'Tajawal', sans-serif; font-weight: 700; cursor: pointer; }
					@media print { .btn-print { display: none; } }
				</style>
			</head>
			<body>
				<div class="rpt-header">
					<div>
						<?php if ( $logo_url ) : ?>
							<img src="<?php echo esc_url( $logo_url ); ?>" class="rpt-logo" alt="<?php echo esc_attr( $site_name ); ?>">
						<?php else : ?>
							<span class="rpt-title"><?php echo esc_html( $site_name ); ?></span>
						<?php endif; ?>
					</div>
					<div>
						<div class="rpt-title"><?php echo esc_html( $report->title ? $report->title : 'تقرير التحويلات' ); ?></div>
						<div class="rpt-meta">
							الفترة: <?php echo esc_html( isset( $filters['date_from'] ) ? $filters['date_from'] : '' ); ?> — <?php echo esc_html( isset( $filters['date_to'] ) ? $filters['date_to'] : '' ); ?>
						</div>
					</div>
					<button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> طباعة</button>
				</div>
				<div class="rpt-body">
					<div class="stats-row">
						<div class="stat-box">
							<div class="stat-lbl"><i class="fas fa-chart-bar"></i> إجمالي التحويلات</div>
							<div class="stat-val"><?php echo esc_html( isset( $totals['total'] ) ? $totals['total'] : 0 ); ?></div>
						</div>
						<div class="stat-box">
							<div class="stat-lbl"><i class="fas fa-phone"></i> مكالمات</div>
							<div class="stat-val"><?php echo esc_html( isset( $totals['calls'] ) ? $totals['calls'] : 0 ); ?></div>
						</div>
						<div class="stat-box wa">
							<div class="stat-lbl"><i class="fab fa-whatsapp"></i> واتساب</div>
							<div class="stat-val"><?php echo esc_html( isset( $totals['whatsapp'] ) ? $totals['whatsapp'] : 0 ); ?></div>
						</div>
					</div>
					<?php if ( ! empty( $rows ) ) : ?>
					<table class="rpt-tbl">
						<thead><tr><th>الصفحة</th><th>اتصال</th><th>واتساب</th><th>الإجمالي</th></tr></thead>
						<tbody>
						<?php foreach ( $rows as $row ) : ?>
							<tr>
								<td><?php echo esc_html( isset( $row['page_title'] ) ? $row['page_title'] : '' ); ?></td>
								<td><?php echo esc_html( isset( $row['calls'] ) ? $row['calls'] : 0 ); ?></td>
								<td><?php echo esc_html( isset( $row['whatsapp'] ) ? $row['whatsapp'] : 0 ); ?></td>
								<td><?php echo esc_html( isset( $row['total'] ) ? $row['total'] : 0 ); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
					<?php endif; ?>
				</div>
			</body>
			</html>
			<?php
		}

		public static function create_report( $title, $filters, $data, $user_id = 0 ) {
			global $wpdb;
			$days  = (int) kayan_track_get_option( 'kayan_report_expiry_days', 30 );
			$token = bin2hex( random_bytes( 32 ) );

			$wpdb->insert(
				kayan_track_table( 'reports' ),
				array(
					'token'        => $token,
					'title'        => sanitize_text_field( $title ),
					'filters_json' => wp_json_encode( $filters ),
					'data_json'    => wp_json_encode( $data ),
					'created_by'   => (int) $user_id,
					'expires_at'   => date( 'Y-m-d H:i:s', strtotime( '+' . $days . ' days' ) ),
					'created_at'   => current_time( 'mysql' ),
				)
			);

			return array(
				'token' => $token,
				'url'   => add_query_arg( 'kayan_report', $token, home_url( '/' ) ),
			);
		}
	}
}
