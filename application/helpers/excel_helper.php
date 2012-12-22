<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
* Excel library for Code Igniter applications
* Author: Derek Allard, Dark Horse Consulting, www.darkhorse.to, April 2006
* Updated by Wes Johnson, www.wesquire.ca, November 2011
*/

if(!function_exists('to_excel')) {
    function to_excel($query, $fields, $filename='exceloutput')
    {
         $headers = ''; // just creating the var for field headers to append to below
         $data = ''; // just creating the var for field data to append to below

         $obj =& get_instance();

         //$fields = $query->field_data();
         if ($query->num_rows() == 0) {
              echo '<p>The table appears to have no data.</p>';
         } else {
              foreach ($fields as $field) {
                 $headers .= $field->name . "\t";
              }

              foreach ($query->result() as $row) {
                   $line = '';
                   foreach($row as $value) {                                            
                        if ((!isset($value)) OR ($value == "")) {
                             $value = "\t";
                        } else {
                             $value = str_replace('"', '""', $value);
                             $value = '"' . $value . '"' . "\t";
                        }
                        $line .= $value;
                   }
                   $data .= trim($line)."\n";
              }

              $data = str_replace("\r","",$data);

              header('Content-type: application/ms-excel');
              header("Content-Disposition: attachment; filename=$filename.xls");
              echo "$headers\n$data";  
         }
    }
}
/* End of file excel_helper.php */
/* Location: ./application/helpers/excel_helper.php */ 