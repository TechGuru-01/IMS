<?php
include_once __DIR__ . '/../../include/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$selectedMonth = isset($_SESSION['selected_month']) ? (int)$_SESSION['selected_month'] : (int)date('n');
$selectedYear = isset($_SESSION['selected_year']) ? (int)$_SESSION['selected_year'] : (int)date('Y');
$selectedIds = isset($_GET['ids']) ? $_GET['ids'] : null;

$monthName = date("F", mktime(0, 0, 0, $selectedMonth, 1));
$filename = "Transaction_History_" . ($selectedIds ? "Selected" : "{$monthName}_{$selectedYear}") . ".xls";

if (ob_get_length()) ob_clean();

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Cache-Control: max-age=0");


if ($selectedIds) {
    $idArray = explode(',', $selectedIds);
    $cleanIds = implode(',', array_map('intval', $idArray));
    $query = "SELECT date, `quantity out`, name, item, description, cabinet, customer, jig_equipment, remaining, remarks 
              FROM history WHERE id IN ($cleanIds) ORDER BY date ASC";
    $stmt = $conn->prepare($query);
} else {
    $query = "SELECT date, `quantity out`, name, item, description, cabinet, customer, jig_equipment, remaining, remarks 
              FROM history WHERE MONTH(date) = ? AND YEAR(date) = ? ORDER BY date ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $selectedMonth, $selectedYear);
}

$stmt->execute();
$result = $stmt->get_result();


$fields = $result->fetch_fields();
$columnNames = [];
foreach ($fields as $field) {
    $columnNames[] = strtoupper(str_replace('_', ' ', $field->name));
}
$totalColumns = count($columnNames);
$mergeAcross = ($totalColumns > 0) ? $totalColumns - 1 : 1;

echo "<?xml version=\"1.0\"?>\n";
echo "<?mso-application progid=\"Excel.Sheet\"?>\n";
?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 
 <Styles>
  <Style ss:ID="Default">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#333333"/>
  </Style>

  <Style ss:ID="MainTitle">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Bold="1" ss:Size="18" ss:Color="#1A365D"/>
  </Style>

  <Style ss:ID="Subtitle">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="11" ss:Color="#4A5568"/>
  </Style>

  <Style ss:ID="Header">
   <Font ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#2D3748" ss:Pattern="Solid"/> 
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
   </Borders>
  </Style>

  <Style ss:ID="CellData">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
   </Borders>
  </Style>

  <Style ss:ID="CellDataAlt">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Interior ss:Color="#F7FAFC" ss:Pattern="Solid"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#4A5568"/>
   </Borders>
  </Style>

  <Style ss:ID="SignatureLine">
   <Borders>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#000000"/>
   </Borders>
   <Alignment ss:Horizontal="Center" ss:Vertical="Top"/>
   <Font ss:Bold="1"/>
  </Style>
 </Styles>

 <Worksheet ss:Name="Transaction History">
  <Table>
   <?php for($i=0; $i < $totalColumns; $i++) echo '<Column ss:Width="120"/>'; ?>

   <Row ss:Height="40">
    <Cell ss:MergeAcross="<?= $mergeAcross ?>" ss:StyleID="MainTitle">
        <Data ss:Type="String">HEPC JIG INVENTORY MANAGEMENT SYSTEM</Data>
    </Cell>
   </Row>

   <Row ss:Height="25">
    <Cell ss:MergeAcross="<?= $mergeAcross ?>" ss:StyleID="Subtitle">
        <Data ss:Type="String"><?= $selectedIds ? "SELECTED TRANSACTION LOGS" : "OFFICIAL TRANSACTION HISTORY | " . strtoupper($monthName) . " " . $selectedYear ?></Data>
    </Cell>
   </Row>

   <Row ss:Height="15"/> <Row ss:Height="30">
    <?php foreach ($columnNames as $name): ?>
        <Cell ss:StyleID="Header"><Data ss:Type="String"><?= $name ?></Data></Cell>
    <?php endforeach; ?>
   </Row>

   <?php
   $rowCount = 0;
   if ($result && $result->num_rows > 0) {
       while ($row = $result->fetch_assoc()) {
           $rowCount++;
           $rowStyle = ($rowCount % 2 == 0) ? "CellDataAlt" : "CellData";

           echo "<Row ss:Height=\"25\">";
           foreach ($row as $key => $value) {
               $type = (is_numeric($value) && $key !== 'date') ? "Number" : "String";
               if ($key === 'date') $value = date('Y-m-d H:i', strtotime($value));
               echo "<Cell ss:StyleID=\"$rowStyle\"><Data ss:Type=\"$type\">" . htmlspecialchars($value, ENT_XML1, 'UTF-8') . "</Data></Cell>";
           }
           echo "</Row>";
       }
   } else {
       echo "<Row ss:Height=\"25\"><Cell ss:MergeAcross=\"$mergeAcross\" ss:StyleID=\"CellData\"><Data ss:Type=\"String\">No records found.</Data></Cell></Row>";
   }
   ?>

   <Row ss:Height="20"/> <Row ss:Height="20">
    <Cell ss:MergeAcross="<?= $mergeAcross ?>"><Data ss:Type="String">Date Exported: <?= date('F d, Y | h:i A') ?></Data></Cell>
   </Row>

   <Row ss:Height="15"/> <Row ss:Height="25">
    <Cell ss:StyleID="SignatureLine"><Data ss:Type="String">Prepared By: </Data></Cell>
    <Cell ss:Index="<?= $totalColumns - 1 ?>" ss:MergeAcross="1" ss:StyleID="SignatureLine"><Data ss:Type="String">Verified / Approved By</Data></Cell>
   </Row>
  </Table>
 </Worksheet>
</Workbook>
<?php 
$stmt->close();
exit(); 
?>