<?php

require("../includes/config.php");

if (!array_key_exists('user_id', $_SESSION)) {
    redirect('index.php');
}

$data = [
    'title' => 'Crear post',
    'titulo' => '',
    'descripcion' => '',
    /*Error*/
    'titulo_err' => '',
    'descripcion_err' => ''
];

// if form was submitted
if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['titulo'], $_POST['descripcion']) )
{
	// Sanitizamos el array POST
	// $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
	
	// $_POST = preg_replace('/\s\s+/', ' ', $trimmed);

	$_POST = filter_post();

	/*
	 * Validamos el nombre
	 */
	if (isEmpty($_POST['titulo']))
	{
	    $data['titulo_err'] = 'Por favor, ingrese un título.';
	}
	else
	{
	    $data['titulo'] = $_POST['titulo'];
	    if(!checkIfOnlyLetters($data['titulo'])) {
	        $data['titulo_err'] = 'El título solo debe incluir letras y espacios en blanco.';
	    }
	    elseif(!meetLength($data['titulo'], 3, 50)) {
	        $data['titulo_err'] = 'El título debe incluir entre 3 y 50 caracteres.';
	    }
	}

	/*
	 * Validamos la descripción
	 */
	if (isEmpty($_POST['descripcion']))
	{
	    $data['descripcion_err'] = 'Por favor, ingrese un descripción.';
	}
	else
	{
	    $data['descripcion'] = $_POST['descripcion'];

	    // if (!preg_match('/^[a-zA-Z-ZáéíóúÁÉÍÓÚÑñÜü0-9\s.!¡;,:¿?()]+$/', $data['descripcion'])) {
	    // 	$data['descripcion_err'] = 'La descripción solo debe incluir letras, algunos signos ortográficos, números y espacios en blanco.';
	    // }
	}

	// Si todo esta OKAY
	if (empty( $data['titulo_err'] ) && empty( $data['descripcion_err'] )) {
		// Creamos la fecha y hora actual
		$dateTime = date('Y-m-d H:i:s');
		$q = 'INSERT INTO post (title, body, author_id, created_on, last_modified_on) VALUES(?,?,?,?,?);';
		$insert_result = query($q, $data['titulo'], $data['descripcion'], $_SESSION['user_id'], $dateTime, $dateTime);
		// Si true => todo salió bien.
		if ($insert_result) {
		    flash('success', 'Guardado correctamente.');
		    redirect("index.php");
		}
		
		flash('error', 'El registro no fue realizado.', 'danger');
		redirect("index.php");
	}
}

// else render form
render("post/create", $data);