<?php
	//*****************************************************************************************************************
	//******************************************LEER EL ARCHIVO DE CONFIGURACION***************************************
	//*****************************************************************************************************************




	$filename = 'archivo_de_config.ini'; 
	$data= parse_ini_file($filename,true);
	date_default_timezone_set("America/Santiago"); //DETERMINAR LA ZONA HORARIA


	$tiempo_ejec = $data['date_import']['tiempo_maximo_de_ejecucion'];

	


	ini_set('max_execution_time',$tiempo_ejec); 




	//*****************************************************************************************************************
	//*****************************************************************************************************************
	//*****************************************************************************************************************














	//***************************************************************************************************************
	//******************************************CONECTAR CON BASE DE DATOS*******************************************	
	//***************************************************************************************************************





	//**********************************************COMPROBAR CONEXION**********************************************

	error_reporting(E_ERROR | E_PARSE);
	mysqli_report(MYSQLI_REPORT_STRICT);

	try {
	    $conectar = new mysqli($data['db_config']['servidordb'] , $data['db_config']['userdb'], $data['db_config']['passworddb'], $data['db_config']['nombredb'],"3306"); //Detalles BD 
	    
	    mysqli_set_charset($conectar,'utf8');

	    printf("Conjunto de caracteres inicial: %s\n", mysqli_character_set_name($conectar));

	    echo 'Conexión exitosa con el servidor. '.PHP_EOL." | ";
	} catch (Exception $e) {
	    echo 'ERROR:'.$e->getMessage();
	}

	//********************************************** COMPROBAR QUE LA BASE DE DATOS EXISTA ***************************
	 
	$base=mysqli_select_db($conectar ,$data['db_config']['nombredb']);
	try {
		if(!$base){
			throw new Exception("No se encontro la Base de Datos ", 1);
		}
		
	}catch (Exception $ex) {
		echo "Se capturo la excepcion: ". $ex->getMessage();
		exit();
	}


	//*****************************************************************************************************************
	//*****************************************************************************************************************
	//*****************************************************************************************************************










	//*****************************************************************************************************************
	//**********************************************VARIABLE DE VALIDACION INSERT O UPDATE*****************************
	//*****************************************************************************************************************

	$actualizar = false; //Variable de validación que permite saber si estamos actualizando


	//*****************************************************************************************************************
	//*****************************************************************************************************************
	//*******************************************************************************************************************













	//******************************************************************************************************************
	//*******************CALCULAR LA FECHA DESDE QUE SE REALIZARÁ LA EXTRACCION DE LOS DATOS TRELLO*********************
	//******************************************************************************************************************


	$dia = time()-($data['date_import']['dias_atras']*24*60*60); //Te resta un dia (2*24*60*60) te resta dos y asi...
	$dia_fin = date('Y-m-d', $dia);


	//*******************************************************************************************************************
	//*******************************************************************************************************************
	//*******************************************************************************************************************










	//******************************************************************************************************************
	//******************************************************LOGGER DATA*************************************************
	//******************************************************************************************************************



	
	$ar=fopen(dirname(__FILE__)."/logs/log_".date("Y-m-d").".txt", "a+");
	
	fwrite($ar, "FECHA|TIPO|MENSAJE|ID" .PHP_EOL);

	//*******************************************************************************************************************
	//*******************************************************************************************************************
	//*******************************************************************************************************************








	//*******************************************************************************************************************
	//*******************************************************************************************************************
	//******************************************EXTRAER Y ALMACENAR DATOS DE TRELLO**************************************
	//*******************************************************************************************************************
	//*******************************************************************************************************************



//*******************************************************************************************************************
//***************************************************** GET TABLEROS ************************************************
//*******************************************************************************************************************


	$curl = curl_init();
	$urlBoards ='https://api.trello.com/1/members/me/boards?key='.$data['trello_config']['apikey'].'&token='.$data['trello_config']['token'].'';
	curl_setopt($curl, CURLOPT_URL, $urlBoards);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$jsonBoard = curl_exec($curl);
	curl_close($curl);


	$boards= json_decode($jsonBoard,true);

	//*******************************************************************************************************************
	//*************************************** RECORRER Y ALMACENAR TABLEROS *********************************************
	//*******************************************************************************************************************

	for ($i=0; $i < count($boards); $i++)
 	{ 
	 	if($boards[$i])
	 	$nombre = ($boards[$i]['name']);
		$id =($boards[$i]['id']);
		$shortLink =($boards[$i]['shortLink']);
		$closed =($boards[$i]['closed']);
		

		echo "Obteniendo los datos del tablero: ".$nombre.PHP_EOL." | ";

		$consultaExiste = "SELECT COUNT(*) as cantidad FROM tablero WHERE tablero.id = '".$id."'";
		$ejecutarValidacion = mysqli_query($conectar,$consultaExiste); 
		$num = mysqli_fetch_row($ejecutarValidacion);

		if($num[0] == 1)
		{	
			$sql= " UPDATE tablero SET nombre = '$nombre' , id = '$id', shortLink = '$shortLink',closed = '$closed' WHERE tablero.id = '".$id."' "; 		
			$ejecutar = mysqli_query($conectar ,$sql); 

			if($ejecutar == false)
			{
				$tipolog = "tablero_error";
				createLog($tipolog,$id);
			}else{
			$tipolog = "tablero";
			createLog($tipolog,$id);

			$actualizar = true;
			}
			


		}else
		{
			$sql = "INSERT INTO tablero VALUES ('$nombre' , '$id', '$shortLink','$closed' )"; //EL ORDEN DE INGRESADO DEBE SER EL MISMO ORDEN DE LA BASE DE DATOS 
			$ejecutar = mysqli_query($conectar ,$sql);

			if($ejecutar == false)
			{
				$tipolog = "tablero_error";
				createLog($tipolog, $id);
			}else{
				$tipolog = "tablero";
			createLog($tipolog,$id);
			}
			
		}





		

		//*******************************************************************************************************************
		//****************************************** RECORRER Y ALMACENAR MEMBRESIA *****************************************
		//*******************************************************************************************************************

			for ($q=0; $q <count($boards[$i]['memberships']) ; $q++) 
			{ 

				$idMembresia = ($boards[$i]['memberships'][$q]['id']);
				$idMiembro =($boards[$i]['memberships'][$q]['idMember']);
				$tipoMiembro =($boards[$i]['memberships'][$q]['memberType']);
				$idTablero = ($boards[$i]['id']);
				$nombreTablero = ($boards[$i]['name']);



				$consultaExiste = "SELECT COUNT(*) FROM membresia WHERE membresia.idMembresia = '".$idMembresia."'";
				$ejecutarValidacion = mysqli_query($conectar,$consultaExiste); 
				$num = mysqli_fetch_row($ejecutarValidacion);
				

				if($num[0] == 1)
				{	
					$sql= " UPDATE membresia SET idMembresia ='$idMembresia' , idMiembro = '$idMiembro', tipoMiembro= '$tipoMiembro', idTablero= '$idTablero', nombreTablero='$nombreTablero' WHERE 'miembros.idMembresia' = '".$idMembresia."'"; 		
					$ejecutar = mysqli_query($conectar ,$sql); 

					if($ejecutar == false)
					{
						$tipolog = "membresia_error";
						createLog($tipolog,$idMembresia);
					}else{
						$tipolog = "membresia";
						createLog($tipolog,$idMembresia);

						$actualizar = true;
					}

					
				}else
				{
					$sql = "INSERT INTO membresia VALUES ('$idMembresia','$idMiembro' , '$tipoMiembro','$idTablero','$nombreTablero')"; 
					$ejecutar = mysqli_query($conectar ,$sql);
					if ($ejecutar== false) {
						$tipolog="membresia_error";
						createLog($tipolog,$idMembresia);
					}else{
						$tipolog = "membresia";
						createLog($tipolog,$idMembresia);
					}		
				}
			}

		//*******************************************************************************************************************
		//*******************************************************************************************************************
		//*******************************************************************************************************************





	




		//*******************************************************************************************************************
 		//***************************************************** GET ACCIONES ************************************************
		//*******************************************************************************************************************

    	
    	$urlAcciones ='https://trello.com/1/boards/'.$boards[$i]['shortLink'].'/actions?filter=updateList&filter=updateCard&filter=updateBoard&since='.$dia_fin.'&key='.$data['trello_config']['apikey'].'&token='.$data['trello_config']['token'].''; 

		$conexionAcci= curl_init();
		curl_setopt($conexionAcci, CURLOPT_URL,$urlAcciones);
		curl_setopt($conexionAcci,CURLOPT_RETURNTRANSFER, true);
		$jsonAcci = curl_exec($conexionAcci);
		curl_close($conexionAcci);

		$actions= json_decode($jsonAcci,true);


		


		//*******************************************************************************************************************
		//***************************************************** GET LISTAS **************************************************
		//*******************************************************************************************************************



		$urlListas ='https://api.trello.com/1/boards/'.$boards[$i]['shortLink'].'/lists?filter=all&key='.$data['trello_config']['apikey'].'&token='.$data['trello_config']['token'].''; 
		$conexionList = curl_init();
		curl_setopt($conexionList, CURLOPT_URL, $urlListas);
		curl_setopt($conexionList, CURLOPT_RETURNTRANSFER, true);
		$jsonLists = curl_exec($conexionList);
		curl_close($conexionList);	
		$lists = json_decode($jsonLists,true);


		 //*******************************************************************************************************************
		 //***************************************** RECORER Y ALMACENAR LISTAS **********************************************
		 //*******************************************************************************************************************

		for ($k=0; $k <count($lists) ; $k++)  
		{	

			$nombre = ($lists[$k]['name']);
			$id =($lists[$k]['id']);
			$idTablero =($lists[$k]['idBoard']);
			$posicion =($lists[$k]['pos']);

			
			if($lists[$k]['closed'] == false)
			{
				$archivado = 0;
			}else{
				$archivado  = 1;
			}



			$fechaArchivado = "No Aplica";
			$horaArchivado = "No Aplica";

			if($lists[$k]['closed']==true)
			{				

						 //*****************************************************************************************************************************************************
						 //*************************************** RECORRERMOS LAS ACCIONES EN BUSCA DE LA FECHA DE ARCHIVADO **************************************************
						 //******************************************************************************************************************************************************


						for ($p=0; $p <count($actions) ; $p++)
						{ 	 
							if(isset($actions[$p]['data']['old']['closed']))//SI LA ACCION CORRESPONDE A ARCHIVAR
							{
								
								if(isset($actions[$p]['data']['list']['id'])) //SI LA ARCHIVACION CORRESPONDE A UNA LISTA
								{		
										
										$idLista = $actions[$p]['data']['list']['id']; //ID DE LA LISTA ARCHIVADA
										$accionCerrar = $actions[$p]['data']['old']['closed']; //ALMACENAR VALOR DE ARCHIVADO (PARA VER SI SE ARCHIVO O DES-ARCHIVO)
								}
								if($idLista == $id && $accionCerrar == false ){ 
										$fechaArchivado = explode("T", date(DATE_ISO8601, strtotime($actions[$p]['date']))); //$fecha [0] es la fecha
										$horaArchivado = explode("-", $fechaArchivado[1]); //$hora[0] es la hora
								}
							}
						}

						//******************************************************************************************************************************
						//*************************************** ALMACENAMOS LAS LISTAS ARCHIVADAS ***************************************************
						//*****************************************************************************************************************************

				$consultaExiste = "SELECT COUNT(*) FROM lista WHERE lista.id = '".$id."'";
				$ejecutarValidacion = mysqli_query($conectar,$consultaExiste); 
				$num = mysqli_fetch_row($ejecutarValidacion);
					
				if($num[0] == 1)
				{	
					$sql= "UPDATE lista SET nombre ='$nombre' , id = '$id', idTablero= '$idTablero', posicion= '$posicion',archivado ='$archivado', fechaArchivado = '$fechaArchivado[0]',horaArchivado = '$horaArchivado[0]' WHERE lista.id = '".$id."'"; 		
					$ejecutar = mysqli_query($conectar ,$sql); 

					if($ejecutar == 0)
					{
						$tipolog="lista_error";
						createLog($tipolog,$id);
					}else
					{
						$tipolog = "lista";
						createLog($tipolog,$id);

						$actualizar = true;
					}


					
				}else
				{
					$sql = "INSERT INTO lista VALUES ('$nombre' , '$id', '$idTablero', '$posicion' ,'$archivado', '$fechaArchivado[0]','$horaArchivado[0]')"; 
					$ejecutar = mysqli_query($conectar ,$sql);

					if($ejecutar == 0)
					{
						$tipolog = "lista_error";
						createLog($tipolog,$id);
					}else{
						$tipolog = "lista";
						createLog($tipolog,$id);

						$actualizar = true;
					}
				}	


				//******************************************************************************************************************************
				//******************************************************************************************************************************
				//******************************************************************************************************************************



			}else 
			{

			//*******************************************************************************************************************************
			//*************************************** ALMACENAMOS LAS LISTAS NO ARCHIVADAS ***************************************************
			//********************************************************************************************************************************

				$consultaExiste = "SELECT COUNT(*) FROM lista WHERE lista.id = '".$id."'"; 
				$ejecutarValidacion = mysqli_query($conectar,$consultaExiste); 
				$num = mysqli_fetch_row($ejecutarValidacion);


				if($num[0] == 1)
				{	
					$sql= " UPDATE lista SET nombre ='$nombre', id = '$id', idTablero= '$idTablero', posicion= '$posicion',archivado ='$archivado', fechaArchivado = '$fechaArchivado',horaArchivado = '$horaArchivado' WHERE lista.id = '".$id."'"; 		
					$ejecutar = mysqli_query($conectar ,$sql); 


					ECHO "sql : (sql-string) |";
				var_dump($sql);
				ECHO"| --- ".PHP_EOL;


				ECHO "ejecutar : (sql-string) |";
				var_dump($ejecutar);
				ECHO"| --- ".PHP_EOL;


					if($ejecutar==0)
					{
						$tipolog="lista_error";
						createLog($tipolog,$id);
					}else{
						$tipolog = "lista";
						createLog($tipolog,$id);
						$actualizar = true;
					}
				}else
				{
					$sql = "INSERT INTO lista VALUES ('$nombre' , '$id', '$idTablero', '$posicion' ,'$archivado', '$fechaArchivado','$horaArchivado')"; 
					$ejecutar = mysqli_query($conectar ,$sql);




					ECHO "sql : (sql-string) |";
				var_dump($sql);
				ECHO"| --- ".PHP_EOL;


				ECHO "ejecutar : (sql-string) |";
				var_dump($ejecutar);
				ECHO"| --- ".PHP_EOL;



					if($ejecutar == 0){
						$tipolog = "lista_error";
						createLog($tipolog,$id);
					}else
					{
						$tipolog = "lista";
						createLog($tipolog,$id);
					}
				}	
			}

			//******************************************************************************************************************************
			//******************************************************************************************************************************
			//******************************************************************************************************************************



			

			//******************************************************************************************************************************
			//*************************************************** RECORRER ACCION CREAR LISTA ***********************************************
			//******************************************************************************************************************************


			$urlAcciones ='https://trello.com/1/list/'.$lists[$k]['id'].'/actions?filter=createList&since='.$dia_fin.'&key='.$data['trello_config']['apikey'].'&token='.$data['trello_config']['token']; 

			$conexionAcci= curl_init();
			curl_setopt($conexionAcci, CURLOPT_URL,$urlAcciones);
			curl_setopt($conexionAcci,CURLOPT_RETURNTRANSFER, true);
			$jsonAcci = curl_exec($conexionAcci);
			curl_close($conexionAcci);

			$actionsC= json_decode($jsonAcci,true);


			for ($l=0; $l <=count($actionsC) ; $l++)
			{	
							
					


					if(isset($actionsC[$l])){

						$id = $actionsC[$l]['id'];
						$tipo =$actionsC[$l]['type'];
						$fecha = explode("T", date(DATE_ISO8601, strtotime($actionsC[$l]['date']))); //$fecha [0] es la fecha
						$hora = explode("-", $fecha[1]); //$hora[0] es la hora
						$idTablero = $actionsC[$l]['data']['board']['id'];
						$idLista = $actionsC[$l]['data']['list']['id'];
						$nombreLista = $actionsC[$l]['data']['list']['name'];
						$nombreAutor = $actionsC[$l]['memberCreator']['fullName'];
						$idAutor = $actionsC[$l]['memberCreator']['id'];

					}

					
					

					$consultaExiste = "SELECT COUNT(*) FROM accion_crear_lista WHERE accion_crear_lista.id = '".$id."'";
					$ejecutarValidacion = mysqli_query($conectar,$consultaExiste); 
					$num = mysqli_fetch_row($ejecutarValidacion);

					//*************************************** ALMACENAMOS LAS ACCIONS DE CREAR LISTA ***************************************************

					if($num[0] == 1)
					{	
						$sql= " UPDATE accion_crear_lista SET id ='$id' , tipo = '$tipo', fecha= '$fecha[0]', hora= '$hora[0]', idTablero = '$idTablero', idLista = '$idLista', nombreLista = '$nombreLista' ,nombreAutor = '$nombreAutor',idAutor = '$idAutor' WHERE accion_crear_lista.id = '".$id."'";
						$ejecutar = mysqli_query($conectar ,$sql);
						
						if($ejecutar == 0)
						{
							$tipolog = "accion_crear_lista_error";
							createLog($tipolog,$id);
							
						}else
						{
							$actualizar = 1;
							$tipolog = "accion_crear_lista";
							createLog($tipolog,$id);
						}
								
					}else
						{
							$sql = "INSERT INTO accion_crear_lista VALUES ('$id' ,'$tipo', '$fecha[0]','$hora[0]', '$idTablero', '$idLista', '$nombreLista','$nombreAutor','$idAutor')";
							$ejecutar = mysqli_query($conectar ,$sql); 

							if($ejecutar == 0)
							{
								$tipolog = "accion_crear_lista_error";
								createLog($tipolog,$id);
							}else
							{
								$tipolog = "accion_crear_lista";
								createLog($tipolog,$id);
							}
									
					} 

			}//FOR ACCIONES


		




 	 	}//FOR LISTAS 

 	 	
 	 	
 		

	

	 	//*******************************************************************************************************************
     	//*******************************************************************************************************************
	 	//*******************************************************************************************************************





	

 	  //**********************************************************************************************************************
	  //***************************************************** GET TARJETAS ***************************************************
 	  //**********************************************************************************************************************



		$urlTarjetas= 'https://api.trello.com/1/boards/'.$boards[$i]['shortLink'].'/cards?filter=all&since='.$dia_fin.'&key='.$data['trello_config']['apikey'].'&token='.$data['trello_config']['token'].'';
		$conexionCard = curl_init();
		curl_setopt($conexionCard, CURLOPT_URL, $urlTarjetas);
		curl_setopt($conexionCard, CURLOPT_RETURNTRANSFER, true);
		$jsonCards = curl_exec($conexionCard);
		curl_close($conexionCard);


		$cards = json_decode($jsonCards,true);


		 //*******************************************************************************************************************
		 //***************************************** RECORRER Y ALMACENAR TARJETAS *******************************************
		 //*******************************************************************************************************************


		for ($m=0; $m < count($cards)  ; $m++) 
		{ 
			
				
			$nombre = ($cards[$m]['name']);
			$id =($cards[$m]['id']);
			$idTablero=($cards[$m]['idBoard']);
			$idLista =($cards[$m]['idList']);
			$posicionEnLista =($cards[$m]['pos']);
			$shortLink =($cards[$m]['shortLink']);
			$archivado = ($cards[$m]['closed']);
			if ($cards[$m]['closed']== false) {
				$archivado=0;
			}else{
				$archivado=1;
			}

			$fechaArchivado = "No Aplica";
			$horaArchivado = "No Aplica";
			$dia_ultima_actividad = explode("T", date(DATE_ISO8601, strtotime($cards[$m]['dateLastActivity']))); 
			$hora_ultima_actividad = explode("-", $dia_ultima_actividad[1]); 
			$dia_expiracion =explode("T", date(DATE_ISO8601, strtotime($cards[$m]['due']))); 
			$hora_expiracion = 	 explode("-", $dia_expiracion[1]); 
			
			if ($cards[$m]['dueComplete']== false) {
				$expiracion_Completada=0;
			}else{
				$expiracion_Completada=1;
			}

			
			




			//*********************************************************************************************************************************************************************
		 //***************************************************** RESPECTO A LA TABLA TARJETA...*********************************************************************************
		 //***************************************************** RECORRER ACCIONES PARA SABER CUANDO SE ARCHIVO CADA TARJETA ***************************************************
		 //*********************************************************************************************************************************************************************

		 //***************************************************** SI LA TARJETA ESTÁ ARCHIVADA ***************************************************
		if($cards[$m]['closed']==true)
		{
			 //***************************************************** RECORRERMOS LAS ACCIONES EN BUSCA DE LA FECHA DE ARCHIVADO ***************************************************
			for ($p=0; $p <count($actions) ; $p++)
			{ 	 
				if(isset($actions[$p]['data']['old']['closed']))//SI LA ACCION CORRESPONDE A ARCHIVAR
				{
					if(isset($actions[$p]['data']['card']['id'])) //SI LA ARCHIVACION CORRESPONDE A UNA TARJETA
					{
							$idTarjeta = $actions[$p]['data']['card']['id']; //ID DE LA TARJETA ARCHIVADA
							$accionCerrar = $actions[$p]['data']['old']['closed']; //ALMACENAR VALOR DE ARCHIVADO (PARA VER SI SE ARCHIVO O DES-ARCHIVO)
					}
					if($idTarjeta == $id &&  $accionCerrar == false ) //SI LA ID DE LA TARJETA ARCHIVADA ES IGUAL A LA ID ACTUAL  Y SI LA ACCION ES "ARCHIVARSE"
					{


						$fechaArchivado = explode("T", date(DATE_ISO8601, strtotime($actions[$p]['date']))); //$fecha [0] es la fecha
						$horaArchivado = explode("-", $fechaArchivado[1]); //$hora[0] es la hora
	
					
					}
				
				}
			}

			$consultaExiste = "SELECT COUNT(*) FROM tarjeta WHERE tarjeta.id = '".$id."'";
			$ejecutarValidacion = mysqli_query($conectar,$consultaExiste); 
			$num = mysqli_fetch_row($ejecutarValidacion);

			 //***************************************************** ALMACENAR TARJETA ARCHIVADA ***************************************************

			if($num[0] == 1)
			{	
				$sql= " UPDATE tarjeta SET nombre ='$nombre' , id = '$id', idTablero= '$idTablero', idLista= '$idLista', posicionEnLista='$posicionEnLista', shortLink ='$shortLink' ,archivado = '$archivado' ,fechaArchivado= '$fechaArchivado[0]', horaArchivado = '$horaArchivado[0]',dia_ultima_actividad= '$dia_ultima_actividad[0]',hora_ultima_actividad='$hora_ultima_actividad[0]',dia_expiracion= '$dia_expiracion[0]',hora_expiracion='$hora_expiracion[0]', expiracion_Completada='$expiracion_Completada' WHERE tarjeta.id  ='".$id."'"; 		
				$ejecutar = mysqli_query($conectar ,$sql); 
				if($ejecutar==false)
				{
					$tipolog = "tarjeta_error";
					createLog($tipolog,$id);

					$actualizar = true;
				}else
				{
					$tipolog = "tarjeta";
					createLog($tipolog,$id);

					$actualizar = true;
				}
				
			}else
			{
				$sql = "INSERT INTO tarjeta VALUES ('$nombre' , '$id', '$idTablero', '$idLista', '$posicionEnLista', '$shortLink' ,'$archivado', '$fechaArchivado[0]',  '$horaArchivado[0]','$dia_ultima_actividad[0]','$hora_ultima_actividad[0]','$dia_expiracion[0]','$hora_expiracion[0]', '$expiracion_Completada')"; 
				$ejecutar = mysqli_query($conectar ,$sql);
				if($ejecutar==false)
				{
					$tipolog = "tarjeta_error";
					createLog($tipolog,$id);
				}else
				{
					$tipolog = "tarjeta";
					createLog($tipolog,$id);
				}
				
			}	


		}else
		{	
			 //***************************************************** ALMACENAR TARJETA NO ARCHIVADA ***************************************************

			$consultaExiste = "SELECT COUNT(*) FROM tarjeta WHERE tarjeta.id = '".$id."'";
			$ejecutarValidacion = mysqli_query($conectar,$consultaExiste); 
			$num = mysqli_fetch_row($ejecutarValidacion);

			if($num[0] == 1)
			{	
				$sql= " UPDATE tarjeta SET nombre ='$nombre' , id = '$id', idTablero= '$idTablero', idLista= '$idLista', posicionEnLista='$posicionEnLista', shortLink ='$shortLink' ,archivado = '$archivado' ,fechaArchivado= '$fechaArchivado', horaArchivado = '$horaArchivado',dia_ultima_actividad= '$dia_ultima_actividad[0]',hora_ultima_actividad='$hora_ultima_actividad[0]',dia_expiracion= '$dia_expiracion[0]',hora_expiracion='$hora_expiracion[0]', expiracion_Completada='$expiracion_Completada' WHERE tarjeta.id  ='".$id."'"; 		
				$ejecutar = mysqli_query($conectar ,$sql); 
				if($ejecutar==false)
				{
					$tipolog = "tarjeta_error";
					createLog($tipolog,$id);

					$actualizar = true;
				}else
				{
					$tipolog = "tarjeta";
					createLog($tipolog,$id);

					$actualizar = true;	
				}
				
			}else
			{
				$sql = "INSERT INTO tarjeta VALUES ('$nombre' , '$id', '$idTablero', '$idLista', '$posicionEnLista', '$shortLink' ,'$archivado', '$fechaArchivado',  '$horaArchivado','$dia_ultima_actividad[0]','$hora_ultima_actividad[0]','$dia_expiracion[0]','$hora_expiracion[0]', '$expiracion_Completada')"; 
				$ejecutar = mysqli_query($conectar ,$sql);
				if($ejecutar==false)
				{
					$tipolog = "tarjeta_error";
					createLog($tipolog,$id);
				}else
				{
					$tipolog = "tarjeta";
					createLog($tipolog,$id);
				}
				
			}	
		}



		

	
	
			//************************************************************************************************************************************
			//******************************************* RECORRER ACCIONES QUE CORRESPONDEN A CREAR TARJETA  ************************************
			//************************************************************************************************************************************


	
			$urlAcciones ='https://api.trello.com/1/card/'.$shortLink.'/actions?filter=createCard&key='.$data['trello_config']['apikey'].'&token='.$data['trello_config']['token'];

			$conexionAcci= curl_init();
			curl_setopt($conexionAcci, CURLOPT_URL,$urlAcciones);
			curl_setopt($conexionAcci,CURLOPT_RETURNTRANSFER, true);
			$jsonCrear = curl_exec($conexionAcci);
			curl_close($conexionAcci);

			$actionsC= json_decode($jsonCrear,true);

			for ($l=0; $l <count($actionsC) ; $l++)
			{
				
				if($actionsC[$l]['type'] === 'createCard')
				{	
				
				

				$id = $actionsC[$l]['id'];
				$tipo =$actionsC[$l]['type'];
				$fecha = explode("T", date(DATE_ISO8601, strtotime($actionsC[$l]['date']))); //$fecha [0] es la fecha
				$hora = explode("-", $fecha[1]); //$hora[0] es la hora
				$idTablero = $actionsC[$l]['data']['board']['id'];
				$idLista = $actionsC[$l]['data']['list']['id'];
				$idTarjeta = $actionsC[$l]['data']['card']['id'];
				$nombreAutor = $actionsC[$l]['memberCreator']['fullName'];
				$idAutor = $actionsC[$l]['memberCreator']['id'];		


				


				$consultaExiste = "SELECT COUNT(*) FROM accion_crear_tarjeta WHERE accion_crear_tarjeta.id = '".$id."'";
				$ejecutarValidacion = mysqli_query($conectar,$consultaExiste); 
				$num = mysqli_fetch_row($ejecutarValidacion);

							//*************************************** ALMACENAMOS LAS ACCIONES DE CREAR TARJETA ***************************************************

				if($num[0] == 1)
					{	
						
						$sql= " UPDATE accion_crear_tarjeta SET id ='$id' , tipo = '$tipo', fecha = '$fecha[0]', hora = '$hora[0]', idTablero = '$idTablero', idLista = '$idLista', idTarjeta = '$idTarjeta',nombreAutor = '$nombreAutor',idAutor = '$idAutor' WHERE accion_crear_tarjeta.id = '".$id."'"; 		
						$ejecutar = mysqli_query($conectar ,$sql);

						if($ejecutar == false)
							{

								$tipolog = "accion_crear_tarjeta_error";
								createLog($tipolog,$id);

								$actualizar = true;
							}else
								{
									$tipolog = "accion_crear_tarjeta";
									createLog($tipolog,$id);

									$actualizar = true;
								}			
					}else
					{
						$sql = "INSERT INTO accion_crear_tarjeta VALUES ('$id' , '$tipo', '$fecha[0]' , '$hora[0]' , '$idTablero' , '$idLista' , '$idTarjeta' ,'$nombreAutor' , '$idAutor' )";
						$ejecutar = mysqli_query($conectar ,$sql);
						
						if($ejecutar==false)
							{
								
								$tipolog = "accion_crear_tarjeta_error";
								createLog($tipolog,$id);
							}else
								{
									$tipolog = "accion_crear_tarjeta";
									createLog($tipolog,$id);
								}
					}
				}//if create card	
			}//for acciones 
		



			
		 





		//************************************************************************************************************************************************************
		//************************************************************MOVIMIENTO TARJETAS*****************************************************************************
		//************************************************************************************************************************************************************


		
			$urlTest= 'https://trello.com/1/card/'.$cards[$m]['id'].'/actions?filter=updateCard&since='.$dia_fin.'&key='.$data['trello_config']['apikey'].'&token='.$data['trello_config']['token'];
			$conexiontest = curl_init();
			curl_setopt($conexiontest, CURLOPT_URL, $urlTest);
			curl_setopt($conexiontest, CURLOPT_RETURNTRANSFER, true);
			$json_test = curl_exec($conexiontest);
			curl_close($conexiontest);

			$cont =0;
			$cont2 =0;
			$tableroRecorrido=null;
			
			$test = json_decode($json_test,true);

			for ($l=0; $l <count($test) ; $l++) 
			{ 
				
				
				switch ($test[$l]['type']) 
				{

					case 'updateCard': 
							
						if(isset($test[$l]['data']['old']['idList'])) 
							{
								//********************************************************************************************************
								//************************* SI LA ACCION ES MOVER TARJETA DE UNA LISTA A OTRA ****************************
								//********************************************************************************************************


						
								$id = ($test[$l]['id']);
								$tipo =($test[$l]['type']);
								$fecha = explode("T", date(DATE_ISO8601, strtotime($test[$l]['date']))); //$fecha [0] es la fecha
								$hora = explode("-", $fecha[1]); //$hora[0] es la hora
								$idTarjeta = $cards[$m]['id'];
								$idTablero = $test[$l]['data']['board']['id'];
								$listaOrigen = $test[$l]['data']['listBefore']['id'];
								$listaDestino = $test[$l]['data']['listAfter']['id'];
								$nombreAutor = $test[$l]['memberCreator']['fullName'];
								$idAutor = $test[$l]['memberCreator']['id'];
								$tableroRecorrido = $idTablero;


								$cont=$cont+$l;


								$consultaExiste = "SELECT COUNT(*) FROM accion_mover_tarjeta_de_lista WHERE accion_mover_tarjeta_de_lista.id = '".$id."'";
								$ejecutarValidacion = mysqli_query($conectar,$consultaExiste); 
								$num = mysqli_fetch_row($ejecutarValidacion);

								//*************************************** ALMACENAMOS EL MOVIMIENTO DE UNA TARJETA DE UNA LISTA A OTRA ***************************************************

								if($num[0] == 1)
								{	
									$sql= " UPDATE accion_mover_tarjeta_de_lista SET id ='$id' , tipo = '$tipo', fecha= '$fecha[0]', hora= '$hora[0]', idTarjeta = '$idTarjeta', idTablero = '$idTablero', listaOrigen = '$listaOrigen', listaDestino = '$listaDestino', nombreAutor = '$nombreAutor',idAutor = '$idAutor' WHERE accion_mover_tarjeta_de_lista.id = '".$id."'";
									$ejecutar = mysqli_query($conectar ,$sql); 
									if($ejecutar==false)
									{
										$tipolog = "accion_mover_tarjeta_de_lista_error";
										createLog($tipolog,$id);
									}else
									{
										$tipolog = "accion_mover_tarjeta_de_lista";
									createLog($tipolog,$id);

									$actualizar = true;
									}
									
								}else
								{
									$sql = "INSERT INTO accion_mover_tarjeta_de_lista VALUES ('$id' ,'$tipo', '$fecha[0]','$hora[0]','$idTarjeta', '$idTablero', '$listaOrigen', '$listaDestino','$nombreAutor','$idAutor' )";
									$ejecutar = mysqli_query($conectar ,$sql); 	
									if($ejecutar==false)
									{
										$tipolog = "accion_mover_tarjeta_de_lista_error";
										createLog($tipolog,$id);
									}else
									{
										$tipolog = "accion_mover_tarjeta_de_lista";
										createLog($tipolog,$id);
									}
								}
							}





							//*****************************************************************************************************
							//**************************FUERA DE LA ACCIÓN MOVER_DE_LISTA PERO DENTRO DEL CASE*********************
							//*****************************************************************************************************



							 
							if(isset($test[$l]['data']['old']['closed'])) 
							{ 
								//*************************************************************************************************
								//************************* ACCION_REGISTROS_DE_ARCHIVADO_DE_TARJETAS  ****************************
								//*************************************************************************************************


								$estado = "archivar";
								if($test[$l]['data']['old']['closed'] == true)
								{
									$estado = "desarchivar";
								}

							
								$id = ($test[$l]['id']);	
								$tipo =($test[$l]['type']);
								$fecha = explode("T",date(DATE_ISO8601, strtotime($test[$l]['date']))); //$fecha [0] es la fecha
								$hora = explode("-", $fecha[1]); //$hora[0] es la hora
								$idTarjeta = $cards[$m]['id'];
								$idLista = $test[$l]['data']['list']['id'];
								$idTablero = $test[$l]['data']['board']['id'];
								$nombreAutor = $test[$l]['memberCreator']['fullName'];
								$idAutor = $test[$l]['memberCreator']['id'];	

								

								$consultaExiste = "SELECT COUNT(*) FROM accion_registros_archivado WHERE accion_registros_archivado.id = '".$id."'";
								$ejecutarValidacion = mysqli_query($conectar,$consultaExiste); 
								$num = mysqli_fetch_row($ejecutarValidacion);

								//*************************************** ALMACENAMOS UNA TARJETA ACHIVADA ***************************************************

								if($num[0] == 1)
								{	
									$sql= " UPDATE accion_registros_archivado SET id ='$id' , tipo = '$tipo', fecha= '$fecha[0]', hora= '$hora[0]', idTarjeta = '$idTarjeta', idLista = '$idLista', idTablero = '$idTablero',estado ='$estado ',nombreAutor = '$nombreAutor',idAutor = '$idAutor' WHERE accion_registros_archivado.id = '".$id."'";
									$ejecutar = mysqli_query($conectar ,$sql); 
									if($ejecutar==false)
									{
									$tipolog = "accion_registros_archivado_error";
									createLog($tipolog,$id);
									}else
									{
									$tipolog = "accion_registros_archivado";
									createLog($tipolog,$id);
									$actualizar = true;
									}
									
									
								}else
								{
									$sql = "INSERT INTO accion_registros_archivado VALUES ('$id' ,'$tipo', '$fecha[0]','$hora[0]','$idTarjeta', '$idLista', '$idTablero','$estado','$nombreAutor','$idAutor')";
									$ejecutar = mysqli_query($conectar ,$sql); 
									if($ejecutar==false)
									{
									$tipolog = "accion_registros_archivado_error";
									createLog($tipolog,$id);
									$actualizar = true;
									}else
									{
									$tipolog = "accion_registros_archivado";
									createLog($tipolog,$id);
									}
								}
							}
							break;

							//*********************************************************************************************************************************************************************
							//********************************************* FUERA DE LA ACCION REGISTRO_ARCHIVADO_TARJETAS Y FUERA DEL CASE *******************************************************
							//*********************************************************************************************************************************************************************

					default: break;
				}//switch
			}//for
			











			//************************************************************************************************************************************
			//******************************************* RECORRER ACCIONES QUE CORRESPONDEN A CREAR TARJETA EN EL TABLERO -->ANTERIOR<-- ************************************
			//************************************************************************************************************************************


			if($tableroRecorrido==$boards[$i]['id'])
			{ 
				//No se almacenan los datos pues ya fueron ingresados
			}
			else{
				

					 $urlAcciones ='https://trello.com/1/boards/'.$boards[$i]['shortLink'].'/actions?filter=updateCard&limit=1000&since='.$dia_fin.'&key='.$data['trello_config']['apikey'].'&token='.$data['trello_config']['token']; 

					$conexionAcci= curl_init();
					curl_setopt($conexionAcci, CURLOPT_URL,$urlAcciones);
					curl_setopt($conexionAcci,CURLOPT_RETURNTRANSFER, true);
					$jsonAcci = curl_exec($conexionAcci);
					curl_close($conexionAcci);

					$actions= json_decode($jsonAcci,true);
				
					for ($l=0; $l <count($actions) ; $l++)
					{	
						switch ($actions[$l]['type']) 
						{
							//***************************************************** ACCION ACTUALIZAR CARDS ***************************************************
							case 'updateCard' : 	
								if(isset($actions[$l]['data']['old']['idList'])) //************************* SI LA ACCION ES MOVER TARJETA DE UNA LISTA A OTRA ****************************
									{
										$id = ($actions[$l]['id']);
										$tipo =($actions[$l]['type']);
										$fecha = explode("T", $actions[$l]['date']); //$fecha [0] es la fecha
										$hora = explode(".", $fecha[1]); //$hora[0] es la hora
										$idTarjeta = $actions[$l]['data']['card']['id'];
										$idTablero = $actions[$l]['data']['board']['id'];
										$listaOrigen = $actions[$l]['data']['listBefore']['id'];
										$listaDestino = $actions[$l]['data']['listAfter']['id'];
										$nombreAutor = $actions[$l]['memberCreator']['fullName'];
										$idAutor = $actions[$l]['memberCreator']['id'];		



										$consultaExiste = "SELECT COUNT(*) FROM accion_mover_tarjeta_de_lista WHERE accion_mover_tarjeta_de_lista.id = '".$id."'";
										$ejecutarValidacion = mysqli_query($conectar,$consultaExiste); 
										$num = mysqli_fetch_row($ejecutarValidacion);

										//*************************************** ALMACENAMOS EL MOVIMIENTO DE UNA TARJETA DE UNA LISTA A OTRA ***************************************************

										if($num[0] == 1)
										{	
											$sql= " UPDATE accion_mover_tarjeta_de_lista SET id ='$id' , tipo = '$tipo', fecha= '$fecha[0]', hora= '$hora[0]', idTablero = '$idTablero', listaOrigen = '$listaOrigen', listaDestino = '$listaDestino', nombreAutor = '$nombreAutor',idAutor = '$idAutor' WHERE accion_mover_tarjeta_de_lista.id = '".$id."'";
											$ejecutar = mysqli_query($conectar ,$sql); 
											if($ejecutar==false)
											{
												$tipolog = "accion_mover_tarjeta_de_lista_error";
												createLog($tipolog,$id);
											}else
											{
												$tipolog = "accion_mover_tarjeta_de_lista";
											createLog($tipolog,$id);

											$actualizar = true;
											}
											
										}else
										{
											$sql = "INSERT INTO accion_mover_tarjeta_de_lista VALUES ('$id' ,'$tipo', '$fecha[0]','$hora[0]','$idTarjeta', '$idTablero', '$listaOrigen', '$listaDestino','$nombreAutor','$idAutor' )";
											$ejecutar = mysqli_query($conectar ,$sql); 
											if($ejecutar==false)
											{
												$tipolog = "accion_mover_tarjeta_de_lista_error";
												createLog($tipolog,$id);
											}else
											{
												$tipolog = "accion_mover_tarjeta_de_lista";
												createLog($tipolog,$id);
											}
										}
									}
								}
							}
				}












			//*********************************************************************************************************************************************************************
			//******************************************************************* ACCION MOVER_TARJETA_DE_TABLERO *****************************************************************
			//*********************************************************************************************************************************************************************


			$urlTest2= 'https://trello.com/1/card/'.$cards[$m]['id'].'/actions?filter=moveCardToBoard&since='.$dia_fin.'&key='.$data['trello_config']['apikey'].'&token='.$data['trello_config']['token'];
			$conexiontest = curl_init();
			curl_setopt($conexiontest, CURLOPT_URL, $urlTest2);
			curl_setopt($conexiontest, CURLOPT_RETURNTRANSFER, true);
			$json_test2 = curl_exec($conexiontest);
			curl_close($conexiontest);

			
			$test2 = json_decode($json_test2,true);

			for ($l=0; $l <count($test2) ; $l++)
			{ 
				

				switch ($test2[$l]['type']) 
				{

				case 'moveCardToBoard' : 	
						
						$id = ($test2[$l]['id']);
						$tipo =($test2[$l]['type']);
						$fecha = explode("T", date(DATE_ISO8601, strtotime($test2[$l]['date']))); //$fecha [0] es la fecha
						$hora = explode("-", $fecha[1]); //$hora[0] es la hora
						$idTarjeta = $cards[$m]['id'];
						$tableroOrigen = $test2[$l]['data']['boardSource']['id'];
						$tableroDestino = $test2[$l]['data']['board']['id'];
						$nombreAutor = $test2[$l]['memberCreator']['fullName'];
						$idAutor = $test2[$l]['memberCreator']['id'];		




						$cont2=$cont2+$l;


						$consultaExiste = "SELECT COUNT(*) FROM accion_mover_tarjeta_de_tablero WHERE accion_mover_tarjeta_de_tablero.id = '".$id."'";
						$ejecutarValidacion = mysqli_query($conectar,$consultaExiste); 
						$num = mysqli_fetch_row($ejecutarValidacion);

						//*************************************** ALMACENAMOS EL MOVIMIENTO DE UNA TARJETA DE UN TABLERO OTRO ***************************************************

						if($num[0] == 1)
						{	
							$sql= " UPDATE accion_mover_tarjeta_de_tablero SET id ='$id' , tipo = '$tipo', fecha= '$fecha[0]', hora= '$hora[0]',idTarjeta = '$idTarjeta' ,tableroOrigen = '$tableroOrigen', tableroDestino = '$tableroDestino', nombreAutor = '$nombreAutor',idAutor = '$idAutor' WHERE accion_mover_tarjeta_de_tablero.id = '".$id."'";
							$ejecutar = mysqli_query($conectar ,$sql); 
							if($ejecutar==false)
							{
								$tipolog = "accion_mover_tarjeta_de_tablero_error";
								createLog($tipolog,$id);
							}else
							{
								$tipolog = "accion_mover_tarjeta_de_tablero";
								createLog($tipolog,$id);

							$actualizar = true;
							}
							
						}else
						{
							$sql = "INSERT INTO accion_mover_tarjeta_de_tablero VALUES ('$id' ,'$tipo', '$fecha[0]','$hora[0]','$idTarjeta', '$tableroOrigen', '$tableroDestino','$nombreAutor','$idAutor' )";
							$ejecutar = mysqli_query($conectar ,$sql); 
							if($ejecutar==false)
							{
								$tipolog = "accion_mover_tarjeta_de_tablero_error";
								createLog($tipolog,$id);
							}else
							{
								$tipolog = "accion_mover_tarjeta_de_tablero";
								createLog($tipolog,$id);
							}
						}
			}//SWITCH

		}//FOR TEST 2
 	
	}//tarjetas


	//**********************************************************************************************************************************************************
	//****************************************************************FIN DEL RECORRIDO DE TARJETAS*************************************************************
	//**********************************************************************************************************************************************************


	


	



	//**********************************************************************************************************************************************************
	//***************************************************** RECORRER ACCIONES QUE CORRESPONDEN A CREAR  LISTA **************************************************
	//**********************************************************************************************************************************************************

	



	



	//******************************************** GET ACCIONES QUE SEAN ACTUALIZAR UNA TARJETA, LISTA O TABLERO ***************************************************

    $urlAcciones ='https://trello.com/1/boards/'.$boards[$i]['shortLink'].'/actions?filter=updateList&filter=updateCard&filter=updateBoard&since='.$dia_fin.'&key='.$data['trello_config']['apikey'].'&token='.$data['trello_config']['token'].''; 

	$conexionAcci= curl_init();
	curl_setopt($conexionAcci, CURLOPT_URL,$urlAcciones);
	curl_setopt($conexionAcci,CURLOPT_RETURNTRANSFER, true);
	$jsonAcci = curl_exec($conexionAcci);
	curl_close($conexionAcci);

	$actions= json_decode($jsonAcci,true);


	for ($l=0; $l <count($actions) ; $l++)
	{	
		switch ($actions[$l]['type']) 
		{
			//***************************************************** ACCION ACTUALIZAR CARDS ***************************************************
						
			case 'updateList': 
				//******************************************** SI LA ACCION ES ARCHIVAR UNA LISTA ***************************************************

				if(isset($actions[$l]['data']['old']['closed'])) 
				{
					$estado = "archivar";
						if($actions[$l]['data']['old']['closed'] == true)
						{
							$estado = "desarchivar";
						}

						$id = ($actions[$l]['id']);
						$tipo =($actions[$l]['type']);
						$fecha = explode("T", date(DATE_ISO8601, strtotime($actions[$l]['date']))); //$fecha [0] es la fecha
						$hora = explode("-", $fecha[1]); //$hora[0] es la hora
						$idTarjeta = "No aplica";
						$idLista = $actions[$l]['data']['list']['id'];
						$idTablero = $actions[$l]['data']['board']['id'];
						$nombreAutor = $actions[$l]['memberCreator']['fullName'];
						$idAutor = $actions[$l]['memberCreator']['id'];	

						$consultaExiste = "SELECT COUNT(*) FROM accion_registros_archivado WHERE accion_registros_archivado.id = '".$id."'";
						$ejecutarValidacion = mysqli_query($conectar,$consultaExiste); 
						$num = mysqli_fetch_row($ejecutarValidacion);

						//*************************************** ALMACENAMOS UNA LISTA ACHIVADA ***************************************************

						if($num[0] == 1)
						{	
							$sql= " UPDATE accion_registros_archivado SET id ='$id' , tipo = '$tipo', fecha= '$fecha[0]', hora= '$hora[0]', idTarjeta = '$idTarjeta', idLista = '$idLista', idTablero = '$idTablero',estado ='$estado ',nombreAutor = '$nombreAutor',idAutor = '$idAutor' WHERE accion_registros_archivado.id= '".$id."'";
							$ejecutar = mysqli_query($conectar ,$sql); 
							if($ejecutar==false)
							{
								$tipolog="accion_registros_archivado_error";
								createLog($tipolog,$id);
							}else
							{
								$tipolog = "accion_registros_archivado";
								createLog($tipolog,$id);
								$actualizar = true;
							}
							
							
						}else
						{
								$sql = "INSERT INTO accion_registros_archivado VALUES ('$id' ,'$tipo', '$fecha[0]','$hora[0]','$idTarjeta', '$idLista', '$idTablero','$estado','$nombreAutor','$idAutor')";
								$ejecutar = mysqli_query($conectar ,$sql); 
								if($ejecutar==false)
								{
									$tipolog="accion_registros_archivado_error";
									createLog($tipolog,$id);
								}else
								{
									$tipolog = "accion_registros_archivado";
									createLog($tipolog,$id);
								}
							
						}		
				}

				break;


				//******************************************** SI LA ACCION ES MODIFICAR UN TABLERO ***************************************************

				case 'updateBoard' : 

				if(isset($actions[$l]['data']['old']['closed'])) //SI LA ACCION ES ARCHIVAR UN Board
				{
					$estado = "archivar";
						if($actions[$l]['data']['old']['closed'] == true)
						{
							$estado = "desarchivar";
						}

						$id = ($actions[$l]['id']);
						$tipo =($actions[$l]['type']);
						$fecha = explode("T", date(DATE_ISO8601, strtotime($actions[$l]['date']))); //$fecha [0] es la fecha
						$hora = explode("-", $fecha[1]); //$hora[0] es la hora
						$idTarjeta = "No aplica";
						$idLista = "No aplica";
						$idTablero = $actions[$l]['data']['board']['id'];
						$nombreAutor = $actions[$l]['memberCreator']['fullName'];
						$idAutor = $actions[$l]['memberCreator']['id'];	



						$consultaExiste = "SELECT COUNT(*) FROM accion_registros_archivado WHERE accion_registros_archivado.id = '".$id."'";
						$ejecutarValidacion = mysqli_query($conectar,$consultaExiste);  
						$num = mysqli_fetch_row($ejecutarValidacion);

						//*************************************** ALMACENAMOS UN TABLERO ACHIVADO ***************************************************

						if($num[0] == 1)
						{	
							$sql= "UPDATE accion_registros_archivado SET id ='$id' , tipo = '$tipo', fecha= '$fecha[0]', hora= '$hora[0]', idTarjeta = '$idTarjeta', idLista = '$idLista', idTablero = '$idTablero',estado ='$estado ',nombreAutor = '$nombreAutor',idAutor = '$idAutor' WHERE accion_registros_archivado.id = '".$id."'";
							$ejecutar = mysqli_query($conectar ,$sql); 
							if($ejecutar==false)
							{
								$tipolog = "accion_registros_archivado_error";
								createLog($tipolog,$id);
							}else
							{
								$tipolog = "accion_registros_archivado";
							createLog($tipolog,$id);
							$actualizar = true;
							}
						}else
						{
							$sql = "INSERT INTO accion_registros_archivado VALUES('$id' ,'$tipo', '$fecha[0]','$hora[0]','$idTarjeta', '$idLista', '$idTablero','$estado','$nombreAutor','$idAutor' )";  
							$ejecutar = mysqli_query($conectar ,$sql); 
							if($ejecutar==false)
							{
								$tipolog = "accion_registros_archivado_error";
								createLog($tipolog,$id);
							}else
							{
								$tipolog = "accion_registros_archivado";
								createLog($tipolog,$id);
							}
							
						}		
				}

				break;



			//******************************************** SI LA ACCION NO ES DE NINGUN TIPO NOMBRADO EN LOS CASE ***************************************************
					
			default:
					$id = ($actions[$l]['id']);
					$tipo =($actions[$l]['type']);
					$fecha = explode("T", date(DATE_ISO8601, strtotime($actions[$l]['date']))); //$fecha [0] es la fecha
					$hora = explode("-", $fecha[1]); //$hora[0] es la hora
					$idTablero = $actions[$l]['data']['board']['id'];
					$nombreAutor = $actions[$l]['memberCreator']['fullName'];
					$idAutor = $actions[$l]['memberCreator']['id'];	


					$consultaExiste = "SELECT COUNT(*) FROM accion_extras WHERE accion_extras.id = '".$id."'";
					$ejecutarValidacion = mysqli_query($conectar,$consultaExiste); 
					$num = mysqli_fetch_row($ejecutarValidacion);


					//*************************************** ALMACENAMOS UNA ACCION EXTRA ***************************************************

					if($num[0] == 1)
					{	
						$sql= "UPDATE accion_extras SET (id ='$id' , tipo = '$tipo', fecha = '$fecha[0]', hora = '$hora[0]', idTablero = '$idTablero' ,nombreAutor = '$nombreAutor',idAutor = '$idAutor') WHERE accion_extras.id = '".$id."'";
						$ejecutar = mysqli_query($conectar ,$sql); 
						if($ejecutar==false)
						{
						$tipolog="accion_extras_error";
						createLog($tipolog,$id);
						}else
						{
						$tipolog = "accion_extras";
						createLog($tipolog,$id);
						$actualizar = true;
						}
						
					}else
					{
						$sql = "INSERT INTO accion_extras VALUES ('$id' ,'$tipo', '$fecha[0]','$hora[0]', '$idTablero' ,'$nombreAutor','$idAutor' )";   
						$ejecutar = mysqli_query($conectar ,$sql); 
						if($ejecutar==false)
						{
						$tipolog="accion_extras_error";
						createLog($tipolog,$id);
						}else
						{
						$tipolog = "accion_extras";
						createLog($tipolog,$id);
						}
						
					}		
					break;
		}//switch
	}//for actions	
	
 }//tableros



 //******************************************CONFIRMACION DE LA IMPORTACION DE DATOS A LA BASE DE DATOS*********************************************

 if(!$ejecutar)
		{
			if($actualizar == true){
				echo "Los datos han sido actualizados";
				}else echo  "Hubo un ERROR.";
		}else
			{
				echo "Datos guardados correctamente. ";
			}






	function shutdown() 
	{ 
	   
	    $a=error_get_last(); 
	    if($a==null) {
	     echo "No hubo ningun error de ejecución"; 
	    }else{ 
	    		print_r("Existe un error: |");
	    		var_dump($a);
	    		print_r("|");
	      		$id=0;
	      		$error="time_execution";
	 	 		 createLog($error,$id);
	 		  }

	}

	register_shutdown_function('shutdown'); 











	//******************************************CREAR DATA LOGGER********************************************************



	function createLog($tipomsj,$id)
	{		
		
		$ar=fopen(dirname(__FILE__)."/logs/log_".date("Y-m-d").".txt", "a+");
		
		switch ($tipomsj) {

			//******************************************MENSAJES EXITOSOS********************************************************
			case 'tablero':
				fwrite($ar, "".date("Y-m-d H:i:s")."|S|Registro Exitoso del Tablero|".$id.PHP_EOL);
				break;
			case 'lista':
				fwrite($ar, "".date("Y-m-d H:i:s")."|S|Registro Exitoso de la Lista|".$id.PHP_EOL);
				break;
			case 'tarjeta':
				fwrite($ar, "".date("Y-m-d H:i:s")."|S|Registro Exitoso de la Tarjeta|".$id.PHP_EOL);
				break;
			case 'membresia':
				fwrite($ar, "".date("Y-m-d H:i:s")."|S|Registro Exitoso de la membresia|".$id.PHP_EOL);
				break;
			case 'accion_crear_tarjeta':
				fwrite($ar, "".date("Y-m-d H:i:s")."|S|Registro Exitoso de Accion Crear Tarjeta|".$id.PHP_EOL);
				break;
			case 'accion_registros_archivado':
				fwrite($ar, "".date("Y-m-d H:i:s")."|S|Registro Exitoso de Accion Registro Archivado|".$id.PHP_EOL);
				break;
			case 'accion_mover_tarjeta_de_lista':
				fwrite($ar, "".date("Y-m-d H:i:s")."|S|Registro Exitoso de Accion Mover Tarjeta de Lista|".$id.PHP_EOL);
				break;
			case 'accion_mover_tarjeta_de_tablero':
				fwrite($ar, "".date("Y-m-d H:i:s")."|S|Registro Exitoso de Accion Mover Tarjeta de Tablero|".$id.PHP_EOL);
				break;
			case 'accion_crear_lista':
				fwrite($ar, "".date("Y-m-d H:i:s")."|S|Registro Exitoso de Accion Crear Lista|".$id.PHP_EOL);
				break;
			case 'accion_extras' :
				fwrite($ar, "".date("Y-m-d H:i:s")."|S|Registro Exitoso de Accion Extra|".$id.PHP_EOL);
				break;


			//******************************************MENSAJES NO EXISTOSOS********************************************************

			case 'tablero_error':
				fwrite($ar, "".date("Y-m-d H:i:s")."|F|ERROR,Registro Fallido del Tablero|".$id.PHP_EOL);
				break;
			case 'lista_error':
				fwrite($ar, "".date("Y-m-d H:i:s")."|F|ERROR,Registro Fallido de la Lista|".$id.PHP_EOL);
				break;
			case 'tarjeta_error':
				fwrite($ar, "".date("Y-m-d H:i:s")."|F|ERROR,Registro Fallido de la Tarjeta|".$id.PHP_EOL);
				break;
			case 'membresia_error':
				fwrite($ar, "".date("Y-m-d H:i:s")."|F|ERROR,Registro Fallido  de la membresia|".$id.PHP_EOL);
				break;
			case 'accion_crear_tarjeta_error':
				fwrite($ar, "".date("Y-m-d H:i:s")."|F|ERROR,Registro Fallido de Accion Crear Tarjeta|".$id.PHP_EOL);
				break;
			case 'accion_registros_archivado_error':
				fwrite($ar, "".date("Y-m-d H:i:s")."|F|ERROR,Registro Fallido de Accion Registro Archivado|".$id.PHP_EOL);
				break;
			case 'accion_mover_tarjeta_de_lista_error':
				fwrite($ar, "".date("Y-m-d H:i:s")."|F|ERROR,Registro Fallido de Accion Mover Tarjeta de Lista|".$id.PHP_EOL);
				break;
			case 'accion_mover_tarjeta_de_tablero_error':
				fwrite($ar, "".date("Y-m-d H:i:s")."|F|ERROR,Registro Fallido de Accion Mover Tarjeta de Tablero|".$id.PHP_EOL);
				break;
			case 'accion_crear_lista_error':
				fwrite($ar, "".date("Y-m-d H:i:s")."|F|ERROR,Registro Fallido de Accion Crear Lista|".$id.PHP_EOL);
				break;
			case 'accion_extras_error' :
				fwrite($ar, "".date("Y-m-d H:i:s")."|F|ERROR,Registro Fallido de Accion Extra|".$id.PHP_EOL);
				break;
			case 'time_execution' :
				fwrite($ar, "".date("Y-m-d H:i:s")."|F|ERROR, TIEMPO EXCEDIDO DE LA EJECUCIÓN|".PHP_EOL);
				break;
			
		}


	}

?>