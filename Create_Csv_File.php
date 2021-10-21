<?php

class Create_Csv_File {

    private $filepath = '';

    private $first_line = '';

    private $data = [];

    private $delimiter = ';';

    private $result = true;


    function __construct($data, $filepath, $first_line = '') {

        $this->data = $data;
        $this->filepath = $filepath;
        $this->first_line = $this->set_first_line($first_line);        

    }


    private function set_first_line($first_line) {

        $first_line = explode($this->delimiter, $first_line);

        return $this->first_line = implode($this->delimiter, array_map([$this, 'encode_field'], $first_line));
    }


    function create_file() {

        if (!$handle = fopen($this->filepath, 'w')) {
            exit;
        }

        /** Ecriture de la première ligne */
        if (!empty($this->first_line)) {

            if (fwrite($handle, $this->first_line . "\r\n") === false) {
                echo "Impossible d'écrire dans le fichier ($this->filepath)";
            }
        }

        /** Ecriture des lignes de données */
        foreach ($this->data as $row) {

            if (fwrite($handle, implode($this->delimiter, array_map([$this, 'encode_field'], $row)) . "\r\n") === false ) {
                $this->result = false;
            }

        }

        fclose($handle);

        return ($this->result) ? $this->filepath : $this->result;
    }


    function get_result() {
        return $this->result;
    }


    private function encode_field($field) {
        
        if (empty($field))  {

            return $field;
        }
        
        $field = trim($field);

        $field = str_replace('\\"','"',$field);

        // $field = str_replace('"','\"',$field);

        return '"'. $field . '"';

    }
    
}