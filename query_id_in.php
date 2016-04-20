<?php
    # query-ids-in.php
    # ----------------
    # support endpoint for the expansion feature
    # within db-viewer

    #todo change interface to take join_field and figure out table

    function val_str($vals) {
        $val_reps = array_map(
            function($val) {
                return "'$val'";
            },
            $vals
        );
        return implode(',', $val_reps);
    }

	$cmp = class_exists('Util');
	if (!$cmp) {
		require_once('init.php');
	}

    $ids = $_POST['ids'];
	if (isset($_POST['join_field'])) {

		list($table, $joinField) = Util::choose_table_and_field($_POST['join_field']);

		#todo error checking
		#todo accept POST

		# block injection attacks using $ids
		#todo allow non-integer expansion
		#if (preg_match('/^[0-9,]+$/', $ids)) {
        if (is_array($ids)) {
            $ids_str = val_str($ids);

			# do query
			$query = "
				select *
				from $table
				where $joinField in ($ids_str)
			";
            #die($query);
			$rows = Util::sql($query, 'array');

			$data = array();
			foreach ($rows as $row) {
				$idVal = $row[$joinField];
				$data[$idVal] = $row;
			}

			die(json_encode($data));
		}
		else { die('Invalid ids'); }
	}
	else { die('No join field'); }
?>
