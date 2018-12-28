<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../../vendor/autoload.php';

class GeneraPDF extends TCPDF {

    function configIncial($titol) {
        $this->SetCreator('PDF_CREATOR');
        $this->SetAuthor('Xavier Huix');
        $this->SetTitle("Servei d'informes");

        $this->SetHeaderData('../../../../../imatges/logo_Rocagrossa.jpg', 50, '', $titol, array(0, 0, 0), array(0, 0, 0));
        //$this->SetHeaderData('logo_Rocagrossa.jpg', 50, '', $titol, array(0, 0, 0), array(0, 0, 0));
        $this->setFooterData(array(0, 0, 0), array(0, 0, 0));
        //$this->setPrintHeader(false);
        //$this->setPrintFooter(false);
        // set header and footer fonts
        $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
        //$this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);

        $this->SetMargins(10, 30, 10, false);
        $this->SetAutoPageBreak(true, 20);
        $this->SetFont('times', '', 11);

        $this->AddPage();
    }

}
