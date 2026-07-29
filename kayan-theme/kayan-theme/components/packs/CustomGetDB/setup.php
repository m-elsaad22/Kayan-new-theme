<?php
/**
 * DBArguments — إعادة كتابة آمنة (v1.2.0)
 * - كل القيم عبر $wpdb->prepare (لا تجميع نصي خام)
 * - كل المعرّفات (جداول/أعمدة/ترتيب) تُنقّى لأحرف [A-Za-z0-9_] فقط
 * - إزالة اتصال mysqli المستقل نهائياً — كل شيء عبر طبقة $wpdb
 * - نفس التواقيع ونفس قيم الإرجاع للحفاظ على توافق كل المستدعين
 */
class DBArguments {
	function __construct() {
		$this->CompareSlice = array('!=','LIKE','>','>=','<=','<','<>','=');
	}

	private function ident( $name ) {
		return preg_replace( '/[^A-Za-z0-9_]/', '', (string) $name );
	}

	public function get( $data, $per = 20, $offset = 0 ) {
		global $wpdb;
		if ( ! isset( $data['table'] ) ) return false;

		$TableName = $this->ident( $data['table'] );
		if ( '' === $TableName ) return false;

		# الأعمدة المطلوبة — معرّفات آمنة أو *
		$Select = '*';
		if ( isset( $data['select'] ) && '*' !== $data['select'] ) {
			$cols = array();
			foreach ( explode( ',', (string) $data['select'] ) as $col ) {
				$col = $this->ident( trim( $col ) );
				if ( '' !== $col ) $cols[] = '`'.$col.'`';
			}
			if ( ! empty( $cols ) ) $Select = implode( ', ', $cols );
		}

		$sql    = "SELECT $Select FROM `$TableName`";
		$params = array();

		if ( isset( $data['where'] ) && is_array( $data['where'] ) ) {
			$conds = array();
			foreach ( $data['where'] as $skey => $mekon ) {
				$skey = $this->ident( $skey );
				if ( '' === $skey ) continue;
				$op = '=';
				if ( isset( $data['WhersCompare'][ $skey ] ) && in_array( $data['WhersCompare'][ $skey ], $this->CompareSlice, true ) ) {
					$op = $data['WhersCompare'][ $skey ];
				}
				if ( 'LIKE' === $op ) {
					$conds[]  = "`$skey` LIKE %s";
					$params[] = '%'.$wpdb->esc_like( (string) $mekon ).'%';
				} else if ( is_numeric( $mekon ) && false !== strpos( (string) $mekon, '.' ) ) {
					$conds[]  = "`$skey` $op %f";
					$params[] = (float) $mekon;
				} else if ( is_numeric( $mekon ) ) {
					$conds[]  = "`$skey` $op %d";
					$params[] = (int) $mekon;
				} else {
					$conds[]  = "`$skey` $op %s";
					$params[] = (string) $mekon;
				}
			}
			if ( ! empty( $conds ) ) $sql .= ' WHERE '.implode( ' AND ', $conds );
		}

		if ( isset( $data['orderby'] ) ) {
			$orderby = $this->ident( $data['orderby'] );
			$order   = ( isset( $data['order'] ) && 'DESC' === strtoupper( $data['order'] ) ) ? 'DESC' : 'ASC';
			if ( '' !== $orderby ) $sql .= " ORDER BY `$orderby` $order";
		}

		$sql     .= ' LIMIT %d, %d';
		$params[] = max( 0, (int) $offset );
		$params[] = max( 1, (int) $per );

		$row = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
		if ( ! $row ) return false;
		return maybe_unserialize( $row );
	}

	public function insert( $data ) {
		global $wpdb;
		if ( ! isset( $data['table'] ) ) return false;
		$TableName = $this->ident( $data['table'] );
		if ( '' === $TableName ) return false;

		$row = array();
		if ( isset( $data['insertdata'] ) && isset( $data['MyFields'] ) ) {
			foreach ( (array) $data['MyFields'] as $meky ) {
				$col = $this->ident( $meky );
				if ( '' === $col || ! isset( $data['insertdata'][ $meky ] ) ) continue;
				$val = $data['insertdata'][ $meky ];
				$row[ $col ] = is_array( $val ) ? maybe_serialize( $val ) : $val;
			}
		}
		if ( empty( $row ) ) return false;

		$done = $wpdb->insert( $TableName, $row );
		return ( false !== $done ) ? $wpdb->insert_id : false;
	}

	public function update( $data ) {
		global $wpdb;
		if ( ! isset( $data['table'] ) ) return false;
		$TableName = $this->ident( $data['table'] );
		if ( '' === $TableName ) return false;

		if ( ( empty( $data['DefultFields'] ) || count( (array) $data['DefultFields'] ) <= 0 ) && ! isset( $data['id'] ) ) {
			return $this->insert( $data );
		}

		# تجهيز أعمدة التحديث
		$updates = array();
		$wheres  = array();
		if ( isset( $data['insertdata'] ) && is_array( $data['insertdata'] ) ) {
			foreach ( $data['insertdata'] as $skey => $meky ) {
				$col = $this->ident( $skey );
				if ( '' === $col ) continue;
				$val = is_array( $meky ) ? maybe_serialize( $meky ) : $meky;
				if ( ! isset( $data['id'] ) && isset( $data['DefultFields'] ) && in_array( $skey, (array) $data['DefultFields'], true ) ) {
					$wheres[ $col ] = $val;
				}
				if ( isset( $data['MyFields'] ) && in_array( $skey, (array) $data['MyFields'], true ) ) {
					$updates[ $col ] = $val;
				}
			}
		}

		if ( isset( $data['id'] ) ) {
			if ( empty( $updates ) ) return false;
			$done = $wpdb->update( $TableName, $updates, array( 'id' => (int) $data['id'] ) );
			return ( false !== $done ) ? (int) $data['id'] : false;
		}

		# بدون id: upsert بنفس منطق النسخة الأصلية
		$CheckArr = array( 'table' => $TableName );
		if ( ! empty( $wheres ) ) $CheckArr['where'] = $wheres;
		$GetData = $this->get( $CheckArr );
		if ( false === $GetData ) {
			return $this->insert( $data );
		}
		if ( empty( $updates ) || empty( $wheres ) ) return false;
		$done = $wpdb->update( $TableName, $updates, $wheres );
		return ( false !== $done ) ? $wpdb->insert_id : false;
	}

	public function RemoveID( $id ) {
		global $wpdb;
		$table_name = isset( $this->table_name ) ? $this->ident( $this->table_name ) : '';
		if ( '' === $table_name ) return array( 'type' => 'remove', 'alert' => 'error' );
		$deleteOperator = $wpdb->delete( $table_name, array( 'id' => (int) $id ) );
		return $deleteOperator ? array( 'type' => 'remove', 'alert' => 'success' ) : array( 'type' => 'remove', 'alert' => 'error' );
	}
}
