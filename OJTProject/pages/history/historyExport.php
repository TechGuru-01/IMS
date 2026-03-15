<?php
include_once __DIR__ . '/../../include/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- FILTER LOGIC ---
$selectedMonth = isset($_SESSION['selected_month']) ? (int)$_SESSION['selected_month'] : (int)date('n');
$selectedYear = isset($_SESSION['selected_year']) ? (int)$_SESSION['selected_year'] : (int)date('Y');

$monthName = date("F", mktime(0, 0, 0, $selectedMonth, 1));
$filename = "Transaction_History_{$monthName}_{$selectedYear}.xls";

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");

echo "<?xml version=\"1.0\"?>\n";
echo "<?mso-application progid=\"Excel.Sheet\"?>\n";
?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Segoe UI" ss:Size="10"/>
  </Style>

  <Style ss:ID="Title">
   <Font ss:Bold="1" ss:Size="14" ss:Color="#1A365D"/>
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
  </Style>

  <Style ss:ID="Header">
   <Font ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#001F3F" ss:Pattern="Solid"/> 
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>

  <Style ss:ID="DataCell">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>
   </Borders>
  </Style>

  <Style ss:ID="DataCellAlt">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Interior ss:Color="#F9FAFB" ss:Pattern="Solid"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D1D5DB"/>
   </Borders>
  </Style>

  <Style ss:ID="FooterNote">
   <Font ss:Italic="1" ss:Size="9" ss:Color="#4B5563"/>
  </Style>
 </Styles>

 <Worksheet ss:Name="Transaction History">
  <Table>
   <Column ss:Width="110"/> <Column ss:Width="80"/>  <Column ss:Width="90"/>  <Column ss:Width="150"/> <Column ss:Width="150"/> <Column ss:Width="250"/> <Column ss:Width="150"/> <Row ss:Height="25">
    <Cell ss:MergeAcross="6" ss:StyleID="Title">
        <Data ss:Type="String">HEPC JIG IMS - TRANSACTION HISTORY (<?= strtoupper($monthName) ?> <?= $selectedYear ?>)</Data>
    </Cell>
   </Row>
   <Row ss:Height="15"/> <Row ss:Height="25">
    <Cell ss:StyleID="Header"><Data ss:Type="String">DATE</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">QUANTITY</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">MIN QUANTITY</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">NAME</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">ITEM</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">DESCRIPTION</Data></Cell>
    <Cell ss:StyleID="Header"><Data ss:Type="String">REMARKS</Data></Cell>
   </Row>

   <?php
   // Siguraduhin na ang table name ay tama (transaction_history)
   $query = "SELECT date_created, quantity, min_quantity, user_name, item_name, description, remarks 
             FROM transaction_history 
             WHERE MONTH(date_created) = $selectedMonth 
             AND YEAR(date_created) = $selectedYear 
             ORDER BY date_created DESC";
             
   $result = $conn->query($query);
   $rowCount = 0;

   if ($result && $result->num_rows > 0) {
       while ($row = $result->fetch_assoc()) {
           $rowCount++;
           $style = ($rowCount % 2 == 0) ? "DataCellAlt" : "DataCell";

           echo "<Row ss:Height=\"22\">";
           echo "<Cell ss:StyleID=\"$style\"><Data ss:Type=\"String\">" . date('Y-m-d H:i', strtotime($row['date_created'])) . "</Data></Cell>";
           echo "<Cell ss:StyleID=\"$style\"><Data ss:Type=\"Number\">" . $row['quantity'] . "</Data></Cell>";
           echo "<Cell ss:StyleID=\"$style\"><Data ss:Type=\"Number\">" . $row['min_quantity'] . "</Data></Cell>";
           echo "<Cell ss:StyleID=\"$style\"><Data ss:Type=\"String\">" . htmlspecialchars($row['user_name']) . "</Data></Cell>";
           echo "<Cell ss:StyleID=\"$style\"><Data ss:Type=\"String\">" . htmlspecialchars($row['item_name']) . "</Data></Cell>";
           echo "<Cell ss:StyleID=\"$style\"><Data ss:Type=\"String\">" . htmlspecialchars($row['description']) . "</Data></Cell>";
           echo "<Cell ss:StyleID=\"$style\"><Data ss:Type=\"String\">" . htmlspecialchars($row['remarks']) . "</Data></Cell>";
           echo "</Row>";
       }
   } else {
       echo "<Row><Cell ss:MergeAcross='6' ss:StyleID='DataCell'><Data ss:Type='String'>No records found for this period.</Data></Cell></Row>";
   }
   ?>

   <Row ss:Height="20"/> <Row ss:Height="20">
    <Cell ss:MergeAcross="6" ss:StyleID="FooterNote">
        <Data ss:Type="String">System Generated: <?= date('F d, Y h:i A') ?></Data>
    </Cell>
   </Row>
  </Table>
 </Worksheet>
</Workbook>
<?php exit(); ?>