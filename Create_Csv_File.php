<?php

class Create_Csv_File {

    private $filename = '';

    private $filepath = '';

    private $full_host = '';

    private $first_line = '';

    private $date = '';

    private $data = [];

    private $delimiter = ';';

    private $success = true;

    private $msg = '';

    private $default_encode_function_name = 'no_encode_fields';
    
    private $encode_function_name = '';


    /**
     * Construct function
     *
     * @param array $params
     *  - filename
     *  - filepath
     *  - full_host
     *  - first_line
     *  - date
     *  - data
     *  - nom du type d'encodage
     */
    function __construct($params) {

        foreach ($params as $key => $value) {

            $this->$key = $value;
        }

        $first_line = $params['first_line'] ?? '';
        $this->first_line = $this->set_first_line($first_line);
        
        $this->encode_function_name = $params['encode_function_name'] ?? $this->default_encode_function_name;

    }


    /**
     * Crée l'en-tête du fichier
     * en l'encodant
     *
     * @param string $first_line
     * @return string
     */
    private function set_first_line($first_line) {

        $first_line = explode($this->delimiter, $first_line);
        
        return $this->first_line = implode(
            $this->delimiter,
            array_map([$this, $this->encode_function_name], $first_line)
        );
    }



    function create_file() {

        if (!$handle = fopen($this->filepath, 'w')) {
            
            $this->success = false;
            $this->msg = "Impossible de créer le fichier ($this->filepath)";

            return $this->get_response();
        }

        /** Ecriture de la première ligne */
        if (!empty($this->first_line)) {

            if (fwrite($handle, $this->first_line . "\r\n") === false) {

                $this->success = false;
                $this->msg = "Impossible d'écrire dans le fichier ($this->filepath)";

                return $this->get_response();
            }
        }

        /** Ecriture des lignes de données */
        foreach ($this->data as $row) {

            if (fwrite($handle, implode($this->delimiter, array_map([$this, $this->encode_function_name], $row)) . "\r\n") === false ) {
                $this->result = false;
            }

        }

        fclose($handle);

        return $this->get_response();
    }


    function get_result() {
        return $this->result;
    }


    function get_response() {

        return [
            'success' => $this->success,
            'msg' => $this->msg,
            'filename' => $this->filename,
            'filepath' => $this->filepath,
            'full_host' => $this->full_host,
            'date' => $this->date,
            'encode_function_name' => $this->encode_function_name
        ];
    }


    /** Les fonctions d'encodage */

    /**
     * N'applique pas d'encodage
     * Fonction par défaut
     *
     * @param string $field
     * @return string
     */
    private function no_encode_fields($field) {

        return $field;
    }


    private function encode_field($field) {
        
        if (empty($field))  {

            return $field;
        }
        
        // supprime les espaces inutiles
        $field = trim($field);

        $field = str_replace('\\"','"',$field);

        // $field = str_replace('"','\"',$field);

        return $field;

    }


    /**
     * N'encode que les champs qui comportent des guillemets
     * en les entourant de guillemets
     *
     * @param string $field
     * @return string
     */
    private function encode_fields_with_double_quotes($field) {

        if (empty($field))  {

            return $field;
        }
        
        // supprime les espaces inutiles
        $field = trim($field);
        
        if ( strpos($field, '"') !== false ) {

            $field = '"' . $field . '"';
        }

        return $field;

    }
    
}