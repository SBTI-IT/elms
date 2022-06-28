<?php
session_start();
error_reporting(0);
include('includes/config.php');

//Load phpspreadsheet
require "vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$dept = $_SESSION['department'];

    if (isset($_POST['submit'])) {
        //echo'<script>alert("Successfully downloaded")</script>';
        //Create spreadsheet
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        //$sheet->setTitle("Users");
    
        $sheet->setCellValue('A1', 'Count');
        $sheet->setCellValue('B1', 'Employee Details');
        $sheet->setCellValue('C1', 'Leave Type');
        $sheet->setCellValue('D1', 'Posting Date');
        $sheet->setCellValue('E1', 'From Date');
        $sheet->setCellValue('F1', 'To Date');
        $sheet->setCellValue('G1', 'Count Days Left');
        
        $sqlStatement="SELECT CONCAT(employees.FirstName,' ',employees.LastName,'(',employees.EmpId,')') AS fullname, leaves.LeaveType,leaves.PostingDate,leaves.FromDate, leaves.ToDate, leaves.leftDays
                    from leaves join employees on leaves.empid=employees.id WHERE employees.Department='$dept'";
        $query = $dbh -> prepare($sqlStatement);
        $query->execute();
        
        $i=2;
        while ($row = $query->fetch()) {

            $sheet->setCellValue("A".$i, ($i-1));
            $sheet->setCellValue("B".$i, $row["fullname"]);
            $sheet->setCellValue("C".$i, $row["LeaveType"]);
            $sheet->setCellValue("D".$i, $row["PostingDate"]);
            $sheet->setCellValue("E".$i, $row["FromDate"]);
            $sheet->setCellValue("F".$i, $row["ToDate"]);
            $sheet->setCellValue("G".$i, $row["leftDays"]);
            $i++;
        }
        
        $writer = new Xlsx($spreadsheet);
        
        $writer->save("users.xlsx");
        header("Location:leaves.php");
    }
    