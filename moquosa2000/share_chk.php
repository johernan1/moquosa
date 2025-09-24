<?php

if (!trait_exists('share_chk')) {
    trait share_chk {
        // /**
        //  * Comparar con tolerancia y manejar KO/OK.
        //  *
        //  * @param float $val_SOL Valor esperado.
        //  * @param float $val_RES Valor obtenido.
        //  * @param float $tolerancia Tolerancia permitida.
        //  * @param resource $f Recurso de archivo para escritura.
        //  * @param string $textoKO Mensaje de error en caso de KO.
        //  * @param string $textoOK Mensaje de éxito en caso de OK.
        //  * @return bool Retorna true si está dentro de la tolerancia, false si no.
        //  */
        // private function compararConToleranciaKOExit($val_SOL, $val_RES, $tolerancia, $f, $textoKO, $textoOK)
        // {
        //     if (abs($val_SOL - $val_RES) > $tolerancia) {
        //         $f->write( $textoKO . "\n");
        //         return false;
        //     } else {
        //         $f->write( $textoOK);
        //         return true;
        //     }
        // }

        /**
         * Comparar con tolerancia.
         *
         * @param float $val_SOL Valor esperado.
         * @param float $val_RES Valor obtenido.
         * @param float $tolerancia Tolerancia permitida.
         * @param resource $f Recurso de archivo para escritura.
         * @param string $textoKO Mensaje de error en caso de KO.
         * @param string $textoOK Mensaje de éxito en caso de OK.
         * @return bool Retorna true si está dentro de la tolerancia, false si no.
         */
        private function comparar_con_tolerancia($val_SOL, $val_RES, $tolerancia, $f, $textoKO, $textoOK)
        {
            if (abs($val_SOL - $val_RES) > abs($tolerancia)) {
                $f->write( $textoKO );
                return false;
            } else {
                if ($textoOK !== '') {
                    $f->write( $textoOK);
                }
                return true;
            }
        }
        
        /**
         * Comparar con tolerancia y manejar KO/OK.
         *
         * @param float $val_SOL Valor esperado.
         * @param float $val_RES Valor obtenido.
         * @param float $tolerancia Tolerancia permitida.
         * @param resource $f Recurso de archivo para escritura.
         * @param string $textoKO Mensaje de error en caso de KO.
         * @param string $textoOK Mensaje de éxito en caso de OK.
         * @return bool Retorna true si está dentro de la tolerancia, false si no.
         */
        public function comparar_con_tolerancia_KOexit($valor1, $valor2, $tolerancia0, $f,
                                                       $textoKO = "No cumple con la tolerancia. Saliendo del programa.",
                                                       $textoOK = "OK") {
            $diferencia = abs($valor1 - $valor2);
        
            $tolerancia = abs($tolerancia0);
            if (abs($tolerancia) < 0.000001) {
                $tolerancia = 0.000001;
            }

            if ($diferencia < abs($tolerancia)) {
                if (!empty($textoOK)) {
                    $f->write( $textoOK );
                }
                return 1;
            } else {
                $f->write( $textoKO );
                return 0;
            }
        }
        
        public function comparar_listas_con_tolerancia2($lista1, $lista2, $listatol) {
            $son_iguales = true;
            //echo "---***-", implode(", ", $lista1), "\n";
            //echo "---***-", implode(", ", $lista2), "\n";
            //echo "---***-", implode(", ", $listatol), "\n";

            if ((count($lista1) == count($lista2)) && (count($lista1) == count($listatol))) {
                foreach ($lista1 as $i => $num1) {
                    $num2 = $lista2[$i];
                    $tolerancia = $listatol[$i];

                    //tolerancia=num1*tol_porcentual
                    // echo "**** ", $num1, " ", $num2, " ", $tolerancia, "\n";

                    if (abs($tolerancia) < 0.000001) {
                        $tolerancia = 0.000001;
                    }

                    if (abs($num1 - $num2) > abs($tolerancia)) {
                        $son_iguales = false;
                        break;
                    }
                }
            } else {
                $son_iguales = false;
            }

            return $son_iguales;
        }
    }
}
