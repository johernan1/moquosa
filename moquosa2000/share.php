<?php

if (!trait_exists('share')) {
    trait share {

        /*******************************************************************
         * Esta función es necesaria hasta que los alumnos puedan adjuntar
         * un fichero. Por ahora marcan y pegan en la casilla "respuesta"
         * el contenido del fichero *.$2k. La dificultad es que moodle elimina 
         * la entrada los saltos de linea, que ahora se añaden automáticamente
         */
        function prepo_entrada_alumno($response) {
            // Se elimina todo lo que sigue a "END TABLE DATA"
            $patron = 'END TABLE DATA';
            $miresponse = explode($patron, $response);
            $miresponse = $miresponse[0];
            
            // Se divide, el resultado obtenido, por el patron "TABLE:"
            $patron = 'TABLE:';
            $mistablas=  explode($patron, $miresponse);
            //print_object($mistablas);
            
            // Se obtiene el nombre de cada tabla y su contenido
            $patron = '/"([^"]*)"\s*(.*)/';
            
            // Matrices para almacenar los nombres de las tablas y los contenidos de las tablas
            $nombres_de_tablas = [];
            $contenidos_de_tablas = [];

            // Iterar sobre cada elemento de la matriz
            foreach ($mistablas as $mi_tabla) {
                // Inicializar variables para almacenar los resultados de cada iteración
                $nombre_de_tabla = '';
                $contenido_de_tabla = '';

                // Usar preg_match para dividir la cadena en dos partes
                if (preg_match($patron, $mi_tabla, $matches)) {
                    // El primer término es el contenido entre las comillas
                    $nombre_de_tabla = $matches[1];
                    // El segundo término es el resto de la cadena
                    $contenido_de_tabla = $matches[2];
                    // Se eliminan los patrones " _ "
                    // ¿puede aparecer un error si se define algun nombre de
                    // material, sección incluyendo este patron??? A saber
                    $contenido_de_tabla = str_replace(" _ ", "", $contenido_de_tabla);
                    // Agregar el nombre de la tabla a la matriz de nombres
                    $nombres_de_tablas[] = $nombre_de_tabla;
                    // Agregar el contenido de la tabla a la matriz de contenidos
                    $contenidos_de_tablas[] = $contenido_de_tabla;
                }
            }

            
            // Extraer el primer token de cada contenido de tabla
            // Es el texto que aparece antes del primer "="
            foreach ($contenidos_de_tablas as $contenido_de_tabla) {
                // Expresión regular para capturar el primer token antes del primer "="
                if (preg_match('/^\s*([^=\s]+)=/', $contenido_de_tabla, $matches)) {
                    // Eliminar espacios en blanco y obtener el token
                    $primer_token = trim($matches[1]);
                    // Agregar el primer token a la matriz de primeros tokens
                    $primeros_tokens[] = $primer_token;
                } else {
                    // En caso de no encontrar un token válido
                    $primeros_tokens[] = null;
                }
            }

            // Ahora se extrae cada fila de la tabla, que esta determinada por
            // el valor de su primer token
            $filas_de_tablas = [];

            // Iterar sobre el contenido de cada tabla
            foreach ($contenidos_de_tablas as $indice => $contenido_de_tabla) {
                // Obtener el nombre de la tabla actual
                $nombre_de_tabla = $nombres_de_tablas[$indice];

                // Matriz para almacenar las filas con sus tokens como claves
                $filas_con_tokens = [];

                $primer_token=$primeros_tokens[$indice];
                
                // Iterar sobre los tokens únicos para dividir el contenido de la tabla
               
                $patron_fila = '/(?=\b' . preg_quote($primer_token) . '=)/';
                $partes = preg_split($patron_fila, $contenido_de_tabla);
                
                // Asociar las filas con tokens a su respectiva tabla por nombre
                $filas_de_tablas[$nombre_de_tabla] = $partes;
                $filas_de_tablas_n[$nombre_de_tabla]= implode("\n", $partes);
            }
            
            $resultado= implode("\n", $filas_de_tablas_n);
            return $resultado;
        }

        
        
        function buscar_patron_SAP_tokens($contenido_archivo, $patron, $tokens) {
            try {
                // print_object('---------------------------------');
                //echo "inicio buscar_patron_SAP_tokens. 100 primeros caracteres del archivo: ";
                //print_object(substr($contenido_archivo,0,100)."\n\n");
                //print_object('tokens:');
                //print_object($tokens."\n\n");
                $separador = ' ';
                        
                // Dividir el contenido del archivo en líneas usando '\n'
                $lineas = explode("\n", $contenido_archivo);
                //print_object($lineas);
                        
                // Inicializar una lista para almacenar las líneas que coinciden con el patrón
                $lineas_con_patron = [];
                        
                // Crear una expresión regular a partir del patrón
                // $expresion_regular = '/' . $patron . '/';
                $expresion_regular = $patron ;

                // print_object('****************$patron:');
                // print_object("$expresion_regular\n");
                
                // Buscar el patrón en cada línea y dividir cada línea
                $nlinea = 0;
                while ($nlinea < count($lineas)) {
                    $linea = $lineas[$nlinea];
                         
                    // Comprueba si la línea contiene el patrón
                    if (preg_match($expresion_regular, $linea)) {
                        // Si la línea acaba en _ se lee la siguiente
                        // Antes se elimina el retorno de carro si existe
                        // $linea = rtrim($linea, "\n");
                        $linea = rtrim($linea);
                        while (substr($linea, -1) === "_") {
                            $nlinea++;
                            if ($nlinea < count($lineas)) {
                                $linea .= $lineas[$nlinea];
                                //$linea = rtrim($linea, "\n");
                                $linea = rtrim($linea);
                            } else {
                                break; // Romper si no hay más líneas para leer
                            }
                        }
                                        
                        // Elimina los espacios entre comillas
                        $linea = preg_replace_callback(
                            '/"([^"]*)"/',
                            function ($match) {
                                return str_replace(' ', '', $match[0]);
                            },
                            $linea
                        );
                                        
                        // Divide la línea en palabras usando el espacio como separador
                        // print_object("linea: ".$linea);
                        $palabras = preg_split('/\s+/', $linea);
                                        
                        // Inicializa un array para almacenar los valores de los tokens
                        $linea_con_patron = [];
                                        
                        // Busca los tokens en la lista y extrae sus valores
                        foreach ($tokens as $token) {
                            $encontrado_token = false;
                            foreach ($palabras as $palabra) {
                                if (strpos($palabra, $token . '=') === 0) {
                                    $valor = explode('=', $palabra)[1];
                                    $valor = str_replace(',', '.', $valor); // Sustituir , por .
                                    $linea_con_patron[] = $valor;
                                    $encontrado_token = true;
                                    break; // Se encontró el token. Salir del bucle.
                                }
                            }
                            if (!$encontrado_token) {
                                $linea_con_patron[] = $token;
                            }
                        }
                        $lineas_con_patron[] = $linea_con_patron;
                    }
                    $nlinea++;
                }
                
                // print_object('$lineas_con_patron');
                // print_object($lineas_con_patron);
                // print_object('---------------------------------');
                        
                // Verificar si se encontraron líneas con el patrón
                if (count($lineas_con_patron) > 0) {
                    return $lineas_con_patron; // Devolver la lista de líneas que coinciden con el patrón
                } else {
                    return []; // Devolver una lista vacía si no se encontraron líneas con el patrón
                }
                        
            } catch (Exception $e) {
                echo "Ocurrió un error al procesar el contenido: " . $e->getMessage() . "\n";
                echo "Debería continuar el programa\n";
                return [];
            }
        }

        function obtener_factor_cambio_unidades_longitud($unidades) {
            $factor = 1;
            switch ($unidades) {
            case "in":
                $factor = 0.0254;
                break;
            case "m":
                $factor = 1;
                break;
            case "mm":
                $factor = 0.001;
                break;
            case "cm":
                $factor = 0.01;
                break;
            case "ft":
                $factor = 0.3048;
                break;
            default:
                echo "Error en unidades de longitud: " . $unidades . " no definido";
                exit();
            }
            return $factor;
        }

        function obtener_factor_cambio_unidades_fuerza($unidades) {
            $factor = 1;
            switch ($unidades) {
            case "lb":
                $factor = 0.00444822;
                break;
            case "KN":
                $factor = 1;
                break;
            case "Kip":
                $factor = 4.4482;
                break;
            case "Kgf":
                $factor = 0.00980665;
                break;
            case "N":
                $factor = 0.001;
                break;
            case "Tonf":
                $factor = 9.80665;
                break;
            default:
                echo "Error en unidades de fuerza: " . $unidades . " no definido";
                exit();
            }
            return $factor;
        }

        
        function obtener_unidades($archivo) {
            $program_control = self::buscar_patron_SAP_tokens($archivo, "/CurrUnits/", ["CurrUnits"]);
            

            if (empty($program_control)) {
                return array(0, 0);
            }

            $unidades = $program_control[0][0];
            // Se eliminan las primeras comillas
            $unidades = substr($unidades, 1);
            // Se separan por el punto
            $unidades_T = explode(".", $unidades);

            $unidadesF = $unidades_T[0];
            $unidadesL = $unidades_T[1];

            return array($unidadesF, $unidadesL);
        }

        
        function ordenar_lista_lista_coli_colj_colk($lista_lista, $coli, $colj, $colk) {
            usort($lista_lista, function($filaA, $filaB) use ($coli, $colj, $colk) {
                // Convertimos las columnas a float y las comparamos en orden
                if (floatval($filaA[$coli]) == floatval($filaB[$coli])) {
                    if (floatval($filaA[$colj]) == floatval($filaB[$colj])) {
                        return floatval($filaA[$colk]) <=> floatval($filaB[$colk]);
                    }
                    return floatval($filaA[$colj]) <=> floatval($filaB[$colj]);
                }
                return floatval($filaA[$coli]) <=> floatval($filaB[$coli]);
            });

            return $lista_lista;
        }

        
        function ordenar_lista_lista_strcoli_colj_colk($lista_lista, $coli, $colj, $colk) {
            usort($lista_lista, function($filaA, $filaB) use ($coli, $colj, $colk) {
                // Comparamos la primera columna como string
                if ($filaA[$coli] == $filaB[$coli]) {
                    // Las siguientes dos columnas se comparan como float
                    if (floatval($filaA[$colj]) == floatval($filaB[$colj])) {
                        return floatval($filaA[$colk]) <=> floatval($filaB[$colk]);
                    }
                    return floatval($filaA[$colj]) <=> floatval($filaB[$colj]);
                }
                return strcmp($filaA[$coli], $filaB[$coli]);  // Comparación de string
            });

            return $lista_lista;
        }

        // Obtiene el valor mínimo de una columna específica de una matriz bidimensional
        public function obtener_min_coli($mi_lista, $coli)
        {
            // Extrae todos los valores de la columna especificada
            $valores_columna = array_map(function($fila) use ($coli) {
                return (float)$fila[$coli];
            }, $mi_lista);

            // Devuelve el valor mínimo de la columna
            return min($valores_columna);
        }

        // Obtiene el valor máximo de una columna específica de una matriz bidimensional
        public function obtener_max_coli($mi_lista, $coli)
        {
            // Extrae todos los valores de la columna especificada
            $valores_columna = array_map(function($fila) use ($coli) {
                return (float)$fila[$coli];
            }, $mi_lista);

            // Devuelve el valor máximo de la columna
            return max($valores_columna);
        }

        // Obtiene la diferencia entre el valor máximo y mínimo de una columna específica
        public function obtener_max_min_coli($mi_lista, $coli)
        {
            $max = $this->obtener_max_coli($mi_lista, $coli);
            $min = $this->obtener_min_coli($mi_lista, $coli);

            // Devuelve la diferencia entre el valor máximo y mínimo
            return $max - $min;
        }

            /**
     * Imprime una lista de listas con un comentario opcional.
     *
     * @param array $mi_lista La lista de listas a imprimir.
     * @param string $comentario El comentario a imprimir antes de la lista.
     */
    public function printL($mi_lista, $comentario = '') {
        return; //Función de depuración INHABILITADA
        if (!empty($comentario)) {
            echo "'" . $comentario . "'\n";
        }
        
        foreach ($mi_lista as $linea) {
            echo implode(' ', $linea) . "\n";
        }
    }
    }
}
