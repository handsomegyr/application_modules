<?php

// DataTables PHP library
include( "../../php/DataTables.php" );

// Alias Editor classes so they are easy to use
use
	DataTables\Editor,
	DataTables\Editor\Field,
	DataTables\Editor\Format,
	DataTables\Editor\Join,
	DataTables\Editor\Upload,
	DataTables\Editor\Validate;


/*
 * Example PHP implementation used for the joinSelf.html example - the basic idea
 * here is that the join performed is simply to get extra information about the
 * 'manager' (in this case, the name of the manager). To alter the manager for
 * a user, you would change the 'manager' value in the 'users' table, so the
 * information from the join is read-only.
 */
Editor::inst( $db, 'users' )
	->field( 
		Field::inst( 'users.first_name' ),
		Field::inst( 'users.last_name' ),
		Field::inst( 'users.manager' )
			->options( function () use ( $db ) {
				// Use `selectDistinct` to get the full list of names from the
				// database and then concatenate the first and last names
				$userList = $db->selectDistinct( 'users', 'id, first_name, last_name', null, 'first_name ASC' );
				$out = array();

				while ( $row = $userList->fetch() ) {
					$out[] = array(
						"value" => $row['id'],
						"label" => $row['first_name'].' '.$row['last_name']
					);
				}

				return $out;
			} ),
		Field::inst( 'manager.first_name' ),
		Field::inst( 'manager.last_name' )
	)
	->leftJoin( 'users as manager', 'users.manager', '=', 'manager.id' )
	->process($_POST)
	->json();
